<?php

namespace Tests\Feature;

use App\Services\GalleryImageProcessor;
use Database\Seeders\PortfolioSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Runs against the in-memory test database and a fake "gallery" disk — no real files are written.
 */
class ProjectGalleryTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    protected $seeder = PortfolioSeeder::class;

    private int $projectId;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('gallery');
        $this->projectId = (int) DB::table('projects')->where('visible', 1)->orderBy('id')->value('id');
        // Start from a project without a cover so the gallery-as-cover fallback is exercised
        DB::table('projects')->where('id', $this->projectId)->update(['image' => null]);
    }

    private function asAdmin(): static
    {
        return $this->withSession(['admin_logged_in' => true]);
    }

    private function upload(int $count = 2): void
    {
        $files = array_map(fn ($i) => UploadedFile::fake()->image("shot-{$i}.jpg", 2400, 1600), range(1, $count));

        $this->asAdmin()
            ->post(route('admin.projects.gallery.upload', $this->projectId), ['images' => $files])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    public function test_uploaded_images_are_resized_to_webp_with_thumbnails(): void
    {
        $this->upload(2);

        $images = DB::table('project_images')->where('project_id', $this->projectId)->orderBy('sort_order')->get();
        $this->assertCount(2, $images);

        foreach ($images as $image) {
            Storage::disk('gallery')->assertExists([$image->path, $image->thumb_path]);
            $this->assertStringEndsWith('.webp', $image->path);
            $this->assertSame(1600, (int) $image->width);
            $this->assertSame(600, getimagesizefromstring(Storage::disk('gallery')->get($image->thumb_path))[0]);
        }
        $this->assertSame([0, 1], $images->pluck('sort_order')->map(fn ($o) => (int) $o)->all());
    }

    public function test_portrait_small_and_transparent_images_are_handled(): void
    {
        // Palette PNG with transparency (a common cause of GD resize failures)
        $palette = imagecreate(900, 700);
        imagecolortransparent($palette, imagecolorallocate($palette, 0, 0, 0));
        imagefilledrectangle($palette, 100, 100, 500, 400, imagecolorallocate($palette, 255, 0, 0));
        ob_start();
        imagepng($palette);
        $pngPath = tempnam(sys_get_temp_dir(), 'png');
        file_put_contents($pngPath, ob_get_clean());

        $files = [
            UploadedFile::fake()->image('portrait.jpg', 1200, 3000),
            UploadedFile::fake()->image('small.jpg', 400, 300),
            new UploadedFile($pngPath, 'palette.png', 'image/png', null, true),
        ];

        $this->asAdmin()
            ->post(route('admin.projects.gallery.upload', $this->projectId), ['images' => $files])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $images = DB::table('project_images')->orderBy('sort_order')->get();
        $this->assertCount(3, $images);

        // Portrait: the longest side is capped at 1600
        $this->assertSame([640, 1600], [(int) $images[0]->width, (int) $images[0]->height]);
        // Small images are never upscaled
        $this->assertSame([400, 300], [(int) $images[1]->width, (int) $images[1]->height]);
        // Palette PNG converted to WebP
        $this->assertStringEndsWith('.webp', $images[2]->path);
        Storage::disk('gallery')->assertExists([$images[2]->path, $images[2]->thumb_path]);
    }

    public function test_original_file_is_kept_when_optimisation_fails(): void
    {
        // A file whose header says "image" but cannot be decoded
        $path = tempnam(sys_get_temp_dir(), 'bad');
        file_put_contents($path, "\x89PNG\r\n\x1a\n".str_repeat("\0", 64));

        $stored = (new GalleryImageProcessor)->store(new UploadedFile($path, 'broken.png', 'image/png', null, true), $this->projectId);

        $this->assertSame($stored['path'], $stored['thumb_path']);
        Storage::disk('gallery')->assertExists($stored['path']);
    }

    public function test_only_images_can_be_uploaded(): void
    {
        $this->asAdmin()
            ->post(route('admin.projects.gallery.upload', $this->projectId), ['images' => [UploadedFile::fake()->create('notes.pdf', 100, 'application/pdf')]])
            ->assertSessionHasErrors('images.0');

        $this->assertSame(0, DB::table('project_images')->count());
    }

    public function test_gallery_requires_admin_login(): void
    {
        $this->post(route('admin.projects.gallery.upload', $this->projectId), ['images' => [UploadedFile::fake()->image('a.jpg')]])
            ->assertRedirect(route('admin.login'));

        $this->assertSame(0, DB::table('project_images')->count());
    }

    public function test_captions_and_order_can_be_updated(): void
    {
        $this->upload(2);
        [$first, $second] = DB::table('project_images')->orderBy('sort_order')->pluck('id')->all();

        $this->asAdmin()->put(route('admin.projects.gallery.update', $this->projectId), [
            'caption' => [$first => 'Dashboard', $second => 'Reports'],
            'caption_ar' => [$first => 'لوحة التحكم', $second => ''],
            'sort_order' => [$first => 5, $second => 1],
        ])->assertRedirect();

        $this->assertSame('لوحة التحكم', DB::table('project_images')->where('id', $first)->value('caption_ar'));
        $this->assertNull(DB::table('project_images')->where('id', $second)->value('caption_ar'));
        $this->assertSame($second, (int) DB::table('project_images')->orderBy('sort_order')->value('id'));
    }

    public function test_project_page_shows_the_gallery_and_uses_the_first_image_as_cover(): void
    {
        $this->upload(3);
        $first = DB::table('project_images')->orderBy('sort_order')->first();

        $this->get(route('project.show', $this->projectId).'?lang=en')
            ->assertOk()
            ->assertSee('id="gallery"', false)
            ->assertSee('id="lightbox"', false)
            ->assertSee(asset('uploads/'.$first->path), false)
            ->assertSee('<meta property="og:image" content="'.asset('uploads/'.$first->path).'">', false);

        $this->get('/?lang=en')->assertSee(asset('uploads/'.$first->path), false);
    }

    public function test_project_page_without_images_has_no_gallery(): void
    {
        $this->get(route('project.show', $this->projectId).'?lang=en')
            ->assertOk()
            ->assertDontSee('id="lightbox"', false);
    }

    public function test_deleting_an_image_removes_its_files(): void
    {
        $this->upload(1);
        $image = DB::table('project_images')->first();

        $this->asAdmin()->delete(route('admin.projects.gallery.delete', $image->id))->assertRedirect();

        $this->assertSame(0, DB::table('project_images')->count());
        Storage::disk('gallery')->assertMissing([$image->path, $image->thumb_path]);
    }

    public function test_deleting_a_project_removes_its_gallery(): void
    {
        $this->upload(2);

        $this->asAdmin()->delete(route('admin.projects.delete', $this->projectId))->assertRedirect();

        $this->assertSame(0, DB::table('project_images')->where('project_id', $this->projectId)->count());
        $this->assertSame([], Storage::disk('gallery')->allFiles("projects/{$this->projectId}"));
    }
}
