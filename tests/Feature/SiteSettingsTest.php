<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SiteSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $student;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->student = User::factory()->create(['role' => 'student']);
    }

    public function test_non_admin_cannot_access_settings()
    {
        $response = $this->actingAs($this->student)
            ->get(route('admin.settings.index'));
        $response->assertRedirect(route('home'));
    }

    public function test_admin_can_update_settings_and_upload_files()
    {
        Storage::fake('public');

        $logo = UploadedFile::fake()->image('logo.png');
        $favicon = UploadedFile::fake()->image('favicon.png');

        $response = $this->actingAs($this->admin)
            ->post(route('admin.settings.update'), [
                'site_name' => 'Custom Platform',
                'site_description' => 'A custom description',
                'support_email' => 'support@custom.com',
                'support_phone' => '123456',
                'social_telegram' => 'https://t.me/custom',
                'social_instagram' => 'https://instagram.com/custom',
                'social_youtube' => 'https://youtube.com/custom',
                'hero_video_url' => 'https://youtube.com/embed/12345',
                'site_logo' => $logo,
                'site_favicon' => $favicon,
            ]);

        $response->assertRedirect();
        
        // Assert text values are stored
        $this->assertEquals('Custom Platform', SiteSetting::get('site_name'));
        $this->assertEquals('A custom description', SiteSetting::get('site_description'));
        $this->assertEquals('support@custom.com', SiteSetting::get('support_email'));
        $this->assertEquals('123456', SiteSetting::get('support_phone'));
        $this->assertEquals('https://t.me/custom', SiteSetting::get('social_telegram'));
        $this->assertEquals('https://instagram.com/custom', SiteSetting::get('social_instagram'));
        $this->assertEquals('https://youtube.com/custom', SiteSetting::get('social_youtube'));
        $this->assertEquals('https://youtube.com/embed/12345', SiteSetting::get('hero_video_url'));

        // Assert file storage
        $logoPath = SiteSetting::get('site_logo');
        $faviconPath = SiteSetting::get('site_favicon');

        $this->assertNotNull($logoPath);
        $this->assertNotNull($faviconPath);

        Storage::disk('public')->assertExists($logoPath);
        Storage::disk('public')->assertExists($faviconPath);
    }
}
