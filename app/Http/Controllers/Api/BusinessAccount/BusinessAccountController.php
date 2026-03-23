<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BusinessAccount;

use App\Http\Requests\BusinessAccount\StoreBusinessAccountRequest;
use App\Http\Resources\BusinessAccount\BusinessAccountResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\BusinessAccount\BusinessAccountService;
use Illuminate\Http\JsonResponse;

class BusinessAccountController
{
    public function __construct(protected BusinessAccountService $businessAccountService) {}

    public function store(StoreBusinessAccountRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $businessAccount = $this->businessAccountService->store(
            $user,
            $request->validated(),
            $request->file('images', []),
            $request->file('documents', []),
        );

        return ApiResponse::success(
            BusinessAccountResource::make($businessAccount)->toArray($request),
            __('api.business_account_created'),
        );
    }
}
