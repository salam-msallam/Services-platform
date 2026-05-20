<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\StatusEnum;
use App\Models\ActivityType;
use App\Models\Admin;
use App\Models\BusinessAccount;
use App\Models\Category;
use App\Models\City;
use App\Models\Order;
use App\Models\Service;
use App\Models\SubCategory;
use App\Models\User;
use App\Services\Admin\AdminService;
use App\Services\Admin\CategoryService;
use App\Services\Admin\CityService;
use App\Services\Admin\SubCategoryService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class SoftDeleteDeletionLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_is_soft_deleted(): void
    {
        $user = User::factory()->create(['type' => 'admin']);
        $admin = Admin::query()->create([
            'user_id' => $user->id,
            'email' => 'admin-soft-delete@example.com',
            'main_admin' => false,
        ]);

        $admin->delete();

        $this->assertSoftDeleted('admins', [
            'id' => $admin->id,
        ]);
    }

    public function test_main_admin_cannot_be_deleted(): void
    {
        $user = User::factory()->create(['type' => 'admin']);
        $admin = Admin::query()->create([
            'user_id' => $user->id,
            'email' => 'main-admin@example.com',
            'main_admin' => true,
        ]);

        $this->expectException(AuthorizationException::class);

        app(AdminService::class)->deleteAdmin($admin, 99999);
    }

    public function test_business_account_soft_delete_cascades_to_services_and_soft_deletes_non_accepted_orders(): void
    {
        $service = $this->createServiceGraph();

        $acceptedOrder = Order::query()->create([
            'business_account_id' => $service->business_account_id,
            'service_id' => $service->id,
            'status' => StatusEnum::Accepted->value,
            'quantity' => 1,
        ]);

        $pendingOrder = Order::query()->create([
            'business_account_id' => $service->business_account_id,
            'service_id' => $service->id,
            'status' => StatusEnum::Pending->value,
            'quantity' => 1,
        ]);

        $rejectedOrder = Order::query()->create([
            'business_account_id' => $service->business_account_id,
            'service_id' => $service->id,
            'status' => StatusEnum::Rejected->value,
            'quantity' => 1,
        ]);

        $service->businessAccount->delete();

        $this->assertSoftDeleted('business_accounts', [
            'id' => $service->business_account_id,
        ]);
        $this->assertSoftDeleted('services', [
            'id' => $service->id,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $acceptedOrder->id,
            'deleted_at' => null,
        ]);
        $this->assertSoftDeleted('orders', [
            'id' => $pendingOrder->id,
        ]);
        $this->assertSoftDeleted('orders', [
            'id' => $rejectedOrder->id,
        ]);
    }

    public function test_business_account_restore_endpoint_restores_related_services(): void
    {
        $service = $this->createServiceGraph();
        $owner = $service->businessAccount->user;

        $service->businessAccount->delete();

        $this->actingAs($owner, 'api')
            ->postJson(route('business-accounts.restore', ['businessAccountId' => $service->business_account_id]))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('business_accounts', [
            'id' => $service->business_account_id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'deleted_at' => null,
        ]);
    }

    public function test_service_restore_endpoint_restores_service_and_eligible_orders(): void
    {
        $service = $this->createServiceGraph();
        $owner = $service->businessAccount->user;

        $pendingOrder = Order::query()->create([
            'business_account_id' => $service->business_account_id,
            'service_id' => $service->id,
            'status' => StatusEnum::Pending->value,
            'quantity' => 1,
        ]);
        $acceptedOrder = Order::query()->create([
            'business_account_id' => $service->business_account_id,
            'service_id' => $service->id,
            'status' => StatusEnum::Accepted->value,
            'quantity' => 1,
        ]);

        $service->delete();

        $this->assertSoftDeleted('orders', ['id' => $pendingOrder->id]);
        $this->assertDatabaseHas('orders', ['id' => $acceptedOrder->id, 'deleted_at' => null]);

        $this->actingAs($owner, 'api')
            ->postJson(route('services.restore', ['serviceId' => $service->id]))
            ->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'deleted_at' => null,
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $pendingOrder->id,
            'deleted_at' => null,
        ]);
    }

    public function test_category_with_linked_services_cannot_be_deleted(): void
    {
        $service = $this->createServiceGraph();

        try {
            app(CategoryService::class)->deleteCategory($service->category);
            $this->fail('Expected category deletion to be blocked.');
        } catch (ValidationException $exception) {
            $this->assertSame(
                __('admin.category_has_services'),
                $exception->errors()['category'][0]
            );
        }

        $this->assertDatabaseHas('categories', [
            'id' => $service->category_id,
        ]);
    }

    public function test_category_without_linked_services_is_hard_deleted(): void
    {
        $category = Category::query()->create([
            'name' => ['en' => 'Empty category'],
        ]);

        app(CategoryService::class)->deleteCategory($category);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
        ]);
    }

    public function test_sub_category_delete_sets_service_sub_category_to_null(): void
    {
        $service = $this->createServiceGraph();
        $subCategory = SubCategory::query()->create([
            'category_id' => $service->category_id,
            'name' => ['en' => 'Sub category'],
        ]);

        $service->update([
            'sub_category_id' => $subCategory->id,
        ]);

        app(SubCategoryService::class)->deleteSubCategory($subCategory);

        $this->assertDatabaseMissing('sub_categories', [
            'id' => $subCategory->id,
        ]);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'sub_category_id' => null,
        ]);
    }

    public function test_city_delete_sets_service_and_business_account_city_to_null(): void
    {
        if (DB::getDriverName() === 'sqlite') {
            $this->markTestSkipped('The production migration alters services.city_id nullability for MySQL; sqlite keeps the original NOT NULL column in this test suite.');
        }

        $service = $this->createServiceGraph();
        $city = $service->city;

        app(CityService::class)->deleteCity($city);

        $this->assertDatabaseMissing('cities', [
            'id' => $city->id,
        ]);
        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'city_id' => null,
        ]);
        $this->assertDatabaseHas('business_accounts', [
            'id' => $service->business_account_id,
            'city_id' => null,
        ]);
    }

    private function createServiceGraph(): Service
    {
        $city = City::query()->create([
            'name' => ['en' => 'Damascus'],
        ]);
        $activityType = ActivityType::query()->create([
            'name' => ['en' => 'General Services'],
        ]);
        $category = Category::query()->create([
            'name' => ['en' => 'Home Services'],
        ]);
        $user = User::factory()->create(['type' => 'app_user']);

        $businessAccount = BusinessAccount::query()->create([
            'user_id' => $user->id,
            'city_id' => $city->id,
            'activity_type_id' => $activityType->id,
            'name' => ['en' => 'Account'],
            'description' => ['en' => 'Description'],
            'activities' => ['en' => 'Maintenance'],
            'status' => StatusEnum::Accepted->value,
        ]);

        return Service::query()->create([
            'business_account_id' => $businessAccount->id,
            'category_id' => $category->id,
            'city_id' => $city->id,
            'title' => ['en' => 'Service title'],
            'description' => ['en' => 'Service description'],
            'quantity' => 1,
            'work_type' => 'on_site',
            'price' => 100.00,
            'price_syp' => 1300000,
            'price_usd' => 100.00,
            'currency' => Service::CURRENCY_USD,
            'latitude' => 33.5138,
            'longitude' => 36.2765,
            'property_type' => Service::PROPERTY_TYPE_SELLER,
            'status' => StatusEnum::Accepted->value,
        ]);
    }
}
