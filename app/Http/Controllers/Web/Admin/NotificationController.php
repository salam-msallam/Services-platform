<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateDeviceTokenRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Notification\NotificationService;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    public function updateDeviceToken(UpdateDeviceTokenRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $admin = $user->admin;

        if ($admin === null) {
            return ApiResponse::error(__('admin.notification_admin_profile_missing'), [], 403);
        }

        $validated = $request->validated();

        $record = $this->notificationService->updateDeviceToken(
            $admin,
            $validated['device_token'],
            isset($validated['platform']) ? (string) $validated['platform'] : null,
        );

        return ApiResponse::success(
            [
                'id' => $record->id,
                'platform' => $record->platform,
                'last_used_at' => $record->last_used_at?->toIso8601String(),
            ],
            __('admin.device_token_updated'),
        );
    }
}
