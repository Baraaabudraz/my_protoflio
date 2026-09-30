<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Turns an uploaded image into a web-optimised WebP (max 1600px) plus a thumbnail (max 600px)
 * on the "gallery" disk. Falls back to storing the original file when GD/WebP is unavailable.
 */
class GalleryImageProcessor
{
    private const MAX_SIZE = 1600;

    private const THUMB_SIZE = 600;

    private const QUALITY = 82;

    /**
     * @return array{path: string, thumb_path: string, width: ?int, height: ?int}
     */
    public function store(UploadedFile $file, int $projectId): array
    {
        $directory = "projects/{$projectId}";
        $name = Str::lower(Str::random(20));
        $disk = Storage::disk('gallery');

        $source = function_exists('imagewebp') ? $this->load($file) : null;

        if (! $source) {
            $extension = $file->guessExtension() ?: 'jpg';
            $path = $disk->putFileAs($directory, $file, "{$name}.{$extension}");

            return ['path' => $path, 'thumb_path' => $path, 'width' => null, 'height' => null];
        }

        $large = $this->resize($source, self::MAX_SIZE);
        $thumb = $this->resize($source, self::THUMB_SIZE);

        $path = "{$directory}/{$name}.webp";
        $thumbPath = "{$directory}/{$name}-thumb.webp";
        $disk->put($path, $this->encode($large));
        $disk->put($thumbPath, $this->encode($thumb));

        return ['path' => $path, 'thumb_path' => $thumbPath, 'width' => imagesx($large), 'height' => imagesy($large)];
    }

    /**
     * Delete an image's files from the gallery disk.
     */
    public function delete(string $path, string $thumbPath): void
    {
        Storage::disk('gallery')->delete(array_unique([$path, $thumbPath]));
    }

    private function load(UploadedFile $file): ?GdImage
    {
        $image = @imagecreatefromstring((string) file_get_contents($file->getRealPath()));

        if (! $image) {
            return null;
        }

        // Respect camera orientation for JPEG photos
        if (function_exists('exif_read_data') && in_array($file->getMimeType(), ['image/jpeg', 'image/jpg'], true)) {
            $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
            $image = match ($orientation) {
                3 => imagerotate($image, 180, 0),
                6 => imagerotate($image, -90, 0),
                8 => imagerotate($image, 90, 0),
                default => $image,
            };
        }

        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);

        return $image;
    }

    private function resize(GdImage $image, int $maxSize): GdImage
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $scale = min(1, $maxSize / max($width, $height));

        if ($scale >= 1) {
            return $image;
        }

        $resized = imagescale($image, (int) round($width * $scale), (int) round($height * $scale), IMG_BICUBIC);

        if (! $resized) {
            throw new RuntimeException('Could not resize the image.');
        }

        return $resized;
    }

    private function encode(GdImage $image): string
    {
        ob_start();
        imagewebp($image, null, self::QUALITY);

        return (string) ob_get_clean();
    }
}
