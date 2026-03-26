<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\BusinessAccount;

use App\Http\Requests\BusinessAccount\StoreBusinessAccountRequest;
use App\Http\Requests\BusinessAccount\UpdateBusinessAccountRequest;
use App\Http\Resources\BusinessAccount\BusinessAccountResource;
use App\Http\Responses\ApiResponse;
use App\Models\BusinessAccount;
use App\Models\User;
use App\Services\BusinessAccount\BusinessAccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessAccountController
{
    public function __construct(protected BusinessAccountService $businessAccountService) {}

    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $businessAccounts = $this->businessAccountService->listForUser($user);

        return ApiResponse::success(
            BusinessAccountResource::collection($businessAccounts)->resolve(),
            __('api.business_accounts_fetched'),
        );
    }

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

    public function update(UpdateBusinessAccountRequest $request, BusinessAccount $businessAccount): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $updatedBusinessAccount = $this->businessAccountService->update(
            $user,
            $businessAccount,
            $request->validated(),
            $request->file('images', []),
            $request->file('documents', []),
        );

        return ApiResponse::success(
            BusinessAccountResource::make($updatedBusinessAccount)->toArray($request),
            __('api.business_account_updated'),
        );
    }

    public function destroy(Request $request, BusinessAccount $businessAccount): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        $this->businessAccountService->delete($user, $businessAccount);

        return ApiResponse::success([], __('api.business_account_deleted'));
    }
}
