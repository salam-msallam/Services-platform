<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Notification;

use App\Http\Requests\Notification\IndexNotificationRequest;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController
{
    public function index(IndexNotificationRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $validated = $request->validated();
        $perPage = isset($validated['per_page']) ? (int) $validated['per_page'] : 15;
        $perPage = max(1, min(50, $perPage));

        $paginator = $user->notifications()
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        $items = $paginator->getCollection()->map(
            static function (DatabaseNotification $notification): array {
                $payload = is_array($notification->data) ? $notification->data : [];

                return [
                    'id' => $notification->id,
                    'data' => [
                        'title' => (string) ($payload['title'] ?? ''),
                        'message' => (string) ($payload['message'] ?? ''),
                        'type' => (string) ($payload['type'] ?? ''),
                        'url' => (string) ($payload['url'] ?? ''),
                    ],
                    'read_at' => $notification->read_at?->toIso8601String(),
                ];
            }
        )->values();

        return ApiResponse::success(
            [
                'items' => $items,
                'pagination' => [
                    'current_page' => $paginator->currentPage(),
                    'last_page' => $paginator->lastPage(),
                    'per_page' => $paginator->perPage(),
                    'total' => $paginator->total(),
                    'from' => $paginator->firstItem(),
                    'to' => $paginator->lastItem(),
                ],
            ],
            __('api.notifications_fetched'),
        );
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $notification = $user->notifications()->where('id', $notificationId)->first();

        if ($notification === null) {
            return ApiResponse::error(__('api.notification_not_found'), [], 404);
        }

        $notification->markAsRead();

        return ApiResponse::success(
            [
                'id' => $notification->id,
                'read_at' => $notification->read_at?->toIso8601String(),
            ],
            __('api.notification_marked_as_read'),
        );
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $unreadQuery = $user->unreadNotifications();
        $count = $unreadQuery->count();

        if ($count > 0) {
            $unreadQuery->update(['read_at' => now()]);
        }

        return ApiResponse::success(
            ['marked' => $count],
            __('api.notifications_marked_as_read'),
        );
    }
}
