<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Slider;
use App\Models\User;
use App\Services\Slider\SliderService;
use Carbon\CarbonImmutable;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SliderManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_admin_without_view_sliders_permission_cannot_access_slider_index(): void
    {
        $user = User::factory()->create(['type' => 'admin']);
        $user->givePermissionTo('access admin dashboard');

        $this->actingAs($user, 'web')
            ->get(route('admin.sliders.index'))
            ->assertForbidden();
    }

    public function test_admin_with_view_sliders_permission_can_access_slider_index(): void
    {
        $user = User::factory()->create(['type' => 'admin']);
        $user->givePermissionTo(['access admin dashboard', 'view-sliders']);

        $this->actingAs($user, 'web')
            ->get(route('admin.sliders.index'))
            ->assertOk()
            ->assertSee(__('admin.sliders'));
    }

    public function test_admin_with_create_sliders_permission_can_upload_slider_image(): void
    {
        Storage::fake('public');

        $user = User::factory()->create(['type' => 'admin']);
        $user->givePermissionTo(['access admin dashboard', 'view-sliders', 'create-sliders']);

        $this->actingAs($user, 'web')
            ->post(route('admin.sliders.store'), [
                'title' => [
                    'ar' => 'Arabic title',
                    'en' => 'English title',
                ],
                'image' => $this->fakePng('slider.png'),
            ])
            ->assertRedirect(route('admin.sliders.index'));

        $slider = Slider::query()->firstOrFail();

        $this->assertSame('English title', $slider->getTranslation('title', 'en'));
        $this->assertCount(1, $slider->getMedia('scroll_bar_image'));
    }

    public function test_admin_without_edit_sliders_permission_cannot_update_slider(): void
    {
        $slider = Slider::query()->create([
            'title' => ['en' => 'Old title', 'ar' => 'Old AR'],
            'status' => true,
        ]);
        $user = User::factory()->create(['type' => 'admin']);
        $user->givePermissionTo(['access admin dashboard', 'view-sliders']);

        $this->actingAs($user, 'web')
            ->put(route('admin.sliders.update', $slider), [
                'title' => ['en' => 'New title', 'ar' => 'New AR'],
                'status' => '0',
            ])
            ->assertForbidden();

        $this->assertSame('Old title', $slider->fresh()->getTranslation('title', 'en'));
    }

    public function test_admin_with_edit_sliders_permission_can_update_title_and_status(): void
    {
        $slider = Slider::query()->create([
            'title' => ['en' => 'Old title', 'ar' => 'Old AR'],
            'status' => true,
        ]);
        $user = User::factory()->create(['type' => 'admin']);
        $user->givePermissionTo(['access admin dashboard', 'view-sliders', 'edit-sliders']);

        $this->actingAs($user, 'web')
            ->put(route('admin.sliders.update', $slider), [
                'title' => ['en' => 'Updated title', 'ar' => 'Updated AR'],
                'status' => '0',
            ])
            ->assertRedirect(route('admin.sliders.index'));

        $slider->refresh();

        $this->assertSame('Updated title', $slider->getTranslation('title', 'en'));
        $this->assertSame('Updated AR', $slider->getTranslation('title', 'ar'));
        $this->assertFalse($slider->status);
    }

    public function test_admin_with_edit_sliders_permission_can_replace_image_as_single_file(): void
    {
        Storage::fake('public');

        $slider = app(SliderService::class)->createSlider([
            'title' => ['en' => 'Image title', 'ar' => 'Image AR'],
            'image' => $this->fakePng('old-slider.png'),
        ]);
        $user = User::factory()->create(['type' => 'admin']);
        $user->givePermissionTo(['access admin dashboard', 'view-sliders', 'edit-sliders']);

        $this->assertCount(1, $slider->getMedia('scroll_bar_image'));

        $this->actingAs($user, 'web')
            ->post(route('admin.sliders.update', $slider), [
                '_method' => 'PUT',
                'title' => ['en' => 'Image title', 'ar' => 'Image AR'],
                'status' => '1',
                'image' => $this->fakePng('new-slider.png'),
            ])
            ->assertRedirect(route('admin.sliders.index'));

        $slider->refresh();

        $this->assertCount(1, $slider->getMedia('scroll_bar_image'));
        $this->assertSame('new-slider.png', $slider->getFirstMedia('scroll_bar_image')?->file_name);
    }

    public function test_admin_without_delete_sliders_permission_cannot_delete_slider(): void
    {
        $slider = Slider::query()->create([
            'title' => ['en' => 'Keep title', 'ar' => 'Keep AR'],
            'status' => true,
        ]);
        $user = User::factory()->create(['type' => 'admin']);
        $user->givePermissionTo(['access admin dashboard', 'view-sliders']);

        $this->actingAs($user, 'web')
            ->delete(route('admin.sliders.destroy', $slider))
            ->assertForbidden();

        $this->assertDatabaseHas('sliders', [
            'id' => $slider->id,
        ]);
    }

    public function test_admin_with_delete_sliders_permission_can_hard_delete_slider(): void
    {
        $slider = Slider::query()->create([
            'title' => ['en' => 'Delete title', 'ar' => 'Delete AR'],
            'status' => true,
        ]);
        $user = User::factory()->create(['type' => 'admin']);
        $user->givePermissionTo(['access admin dashboard', 'view-sliders', 'delete-sliders']);

        $this->actingAs($user, 'web')
            ->delete(route('admin.sliders.destroy', $slider))
            ->assertRedirect(route('admin.sliders.index'));

        $this->assertDatabaseMissing('sliders', [
            'id' => $slider->id,
        ]);
    }

    public function test_current_slider_api_returns_null_when_no_active_sliders_exist(): void
    {
        $this->getJson(route('api.current-slider'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('data', null);
    }

    public function test_daily_rotation_uses_expected_active_slider_and_ignores_inactive_sliders(): void
    {
        $first = Slider::query()->create([
            'title' => ['en' => 'First', 'ar' => 'First AR'],
            'status' => true,
        ]);
        Slider::query()->create([
            'title' => ['en' => 'Inactive', 'ar' => 'Inactive AR'],
            'status' => false,
        ]);
        $second = Slider::query()->create([
            'title' => ['en' => 'Second', 'ar' => 'Second AR'],
            'status' => true,
        ]);

        $this->travelTo(CarbonImmutable::createFromTimestampUTC(86400 * 2));

        $slider = app(SliderService::class)->getDailyRotatingSlider();

        $this->assertNotNull($slider);
        $this->assertTrue($slider->is($first));

        $this->travelTo(CarbonImmutable::createFromTimestampUTC(86400 * 3));

        $slider = app(SliderService::class)->getDailyRotatingSlider();

        $this->assertNotNull($slider);
        $this->assertTrue($slider->is($second));
    }

    public function test_deleted_slider_no_longer_appears_in_daily_rotation(): void
    {
        $deleted = Slider::query()->create([
            'title' => ['en' => 'Deleted', 'ar' => 'Deleted AR'],
            'status' => true,
        ]);
        $remaining = Slider::query()->create([
            'title' => ['en' => 'Remaining', 'ar' => 'Remaining AR'],
            'status' => true,
        ]);

        $deleted->delete();
        $this->travelTo(CarbonImmutable::createFromTimestampUTC(86400 * 2));

        $slider = app(SliderService::class)->getDailyRotatingSlider();

        $this->assertNotNull($slider);
        $this->assertTrue($slider->is($remaining));
    }

    private function fakePng(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent(
            $name,
            (string) base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII=')
        );
    }
}
