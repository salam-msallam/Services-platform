<?php

namespace App\Services\Admin;

use App\Models\AppUser;
use App\Models\BusinessAccount;
use App\Models\Evaluation;
use App\Models\Favorite;
use App\Models\Order;
use App\Models\Report;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class AppUserManagementService
{
    /**
     * @return Collection<int, AppUser>
     */
    public function listAppUsers(): Collection
    {
        return AppUser::query()
            ->withTrashed()
            ->with([
                'user' => fn ($query) => $query->withTrashed(),
            ])
            ->orderByDesc('created_at')
            ->get();
    }

    public function createAppUser(string $name, string $phone, string $password): AppUser
    {
        return DB::transaction(function () use ($name, $phone, $password): AppUser {
            $user = User::query()->create([
                'name' => $name,
                'password' => $password,
                'type' => 'app_user',
            ]);

            $appUser = $user->appUser()->create([
                'phone' => $phone,
                'phone_verified_at' => null,
            ]);

            $role = Role::query()
                ->where('name', 'user')
                ->where('guard_name', 'api')
                ->first();

            if ($role !== null) {
                $user->syncRoles([$role]);
            }

            return $appUser->fresh(['user']);
        });
    }

    /**
     * @param  array{name: string, phone: string}  $data
     */
    public function updateAppUser(AppUser $appUser, array $data): AppUser
    {
        return DB::transaction(function () use ($appUser, $data): AppUser {
            $appUser->loadMissing('user');

            $appUser->user?->update([
                'name' => $data['name'],
            ]);

            $appUser->update([
                'phone' => $data['phone'],
            ]);

            return $appUser->fresh(['user']);
        });
    }

    public function softDeleteAppUser(AppUser $appUser): void
    {
        DB::transaction(function () use ($appUser): void {
            $appUser->loadMissing('user');

            $user = $appUser->user;
            $userId = (int) $appUser->user_id;

            $businessAccountIds = BusinessAccount::query()
                ->where('user_id', $userId)
                ->pluck('id');

            $serviceIds = Service::query()
                ->whereIn('business_account_id', $businessAccountIds)
                ->pluck('id');

            $orderIds = Order::query()
                ->whereIn('business_account_id', $businessAccountIds)
                ->orWhereIn('service_id', $serviceIds)
                ->pluck('id');

            Report::query()
                ->where('user_id', $userId)
                ->orWhereIn('order_id', $orderIds)
                ->delete();

            Favorite::query()
                ->where('user_id', $userId)
                ->orWhereIn('service_id', $serviceIds)
                ->delete();

            Evaluation::query()
                ->where('user_id', $userId)
                ->orWhereIn('service_id', $serviceIds)
                ->delete();

            Order::query()
                ->whereIn('id', $orderIds)
                ->delete();

            Service::query()
                ->whereIn('id', $serviceIds)
                ->delete();

            BusinessAccount::query()
                ->whereIn('id', $businessAccountIds)
                ->delete();

            $appUser->delete();
            $user?->delete();
        });
    }

    public function restoreAppUser(int $appUserId): AppUser
    {
        return DB::transaction(function () use ($appUserId): AppUser {
            $appUser = AppUser::query()
                ->withTrashed()
                ->findOrFail($appUserId);

            $user = User::query()
                ->withTrashed()
                ->find($appUser->user_id);

            if ($user !== null && $user->trashed()) {
                $user->restore();
            }

            if ($appUser->trashed()) {
                $appUser->restore();
            }

            $businessAccounts = BusinessAccount::query()
                ->withTrashed()
                ->where('user_id', (int) $appUser->user_id)
                ->get();

            $businessAccountIds = $businessAccounts->pluck('id');

            BusinessAccount::query()
                ->withTrashed()
                ->whereIn('id', $businessAccountIds)
                ->restore();

            $services = Service::query()
                ->withTrashed()
                ->whereIn('business_account_id', $businessAccountIds)
                ->get();

            $serviceIds = $services->pluck('id');

            Service::query()
                ->withTrashed()
                ->whereIn('id', $serviceIds)
                ->restore();

            $orders = Order::query()
                ->withTrashed()
                ->whereIn('business_account_id', $businessAccountIds)
                ->orWhereIn('service_id', $serviceIds)
                ->get();

            $orderIds = $orders->pluck('id');

            Order::query()
                ->withTrashed()
                ->whereIn('id', $orderIds)
                ->restore();

            Evaluation::query()
                ->withTrashed()
                ->where('user_id', (int) $appUser->user_id)
                ->orWhereIn('service_id', $serviceIds)
                ->restore();

            Favorite::query()
                ->withTrashed()
                ->where('user_id', (int) $appUser->user_id)
                ->orWhereIn('service_id', $serviceIds)
                ->restore();

            Report::query()
                ->withTrashed()
                ->where('user_id', (int) $appUser->user_id)
                ->orWhereIn('order_id', $orderIds)
                ->restore();

            return $appUser->fresh([
                'user' => fn ($query) => $query->withTrashed(),
            ]);
        });
    }
}
