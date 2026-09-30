<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

/**
 * Optimises uploaded gallery images with Laravel's Image component: a WebP (max 1600px) plus a
 * thumbnail (max 600px) on the "gallery" disk. Photos are auto-oriented from their EXIF data.
 *
 * If optimisation fails (unsupported format, missing WebP support, memory), the original file
 * is stored instead so the upload never fails because of image processing.
 */
class GalleryImageProcessor
{
    private const MAX_SIZE = 1600;

    private const THUMB_SIZE = 600;

    private const QUALITY = 82;

    /** Large photos need more than PHP's default 128M when decoded into memory. */
    private const MEMORY_LIMIT = '512M';

    /**
     * @return array{path: string, thumb_path: string, width: ?int, height: ?int}
     */
    public function store(UploadedFile $file, int $projectId): array
    {
        $directory = "projects/{$projectId}";
        $name = Str::lower(Str::random(20));

        try {
            return $this->storeOptimized($file, $directory, $name);
        } catch (Throwable $exception) {
            report($exception);

            return $this->storeOriginal($file, $directory, $name);
        }
    }

    /**
     * Delete an image's files from the gallery disk.
     */
    public function delete(string $path, string $thumbPath): void
    {
        Storage::disk('gallery')->delete(array_unique([$path, $thumbPath]));
    }

    /**
     * @return array{path: string, thumb_path: string, width: int, height: int}
     */
    private function storeOptimized(UploadedFile $file, string $directory, string $name): array
    {
        $this->raiseMemoryLimit();

        // Image is immutable: each scale() returns a new instance from the same upload
        $image = Image::fromUpload($file);
        $large = $image->scale(self::MAX_SIZE, self::MAX_SIZE)->optimize('webp', self::QUALITY);
        $thumb = $image->scale(self::THUMB_SIZE, self::THUMB_SIZE)->optimize('webp', self::QUALITY);

        $path = $large->storeAs($directory, "{$name}.webp", 'gallery');
        $thumbPath = $thumb->storeAs($directory, "{$name}-thumb.webp", 'gallery');

        return ['path' => $path, 'thumb_path' => $thumbPath, 'width' => $large->width(), 'height' => $large->height()];
    }

    /**
     * @return array{path: string, thumb_path: string, width: ?int, height: ?int}
     */
    private function storeOriginal(UploadedFile $file, string $directory, string $name): array
    {
        $extension = $file->guessExtension() ?: 'jpg';
        $path = Storage::disk('gallery')->putFileAs($directory, $file, "{$name}.{$extension}");
        [$width, $height] = @getimagesize($file->getRealPath()) ?: [null, null];

        return ['path' => $path, 'thumb_path' => $path, 'width' => $width, 'height' => $height];
    }

    private function raiseMemoryLimit(): void
    {
        $current = ini_get('memory_limit');

        if ($current !== '-1' && $this->bytes((string) $current) < $this->bytes(self::MEMORY_LIMIT)) {
            @ini_set('memory_limit', self::MEMORY_LIMIT);
        }
    }

    private function bytes(string $value): int
    {
        $number = (int) $value;

        return match (strtolower(substr(trim($value), -1))) {
            'g' => $number * 1024 ** 3,
            'm' => $number * 1024 ** 2,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
