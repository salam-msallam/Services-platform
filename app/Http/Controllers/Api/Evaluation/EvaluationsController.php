<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Evaluation;

use App\Http\Requests\Evaluation\StoreEvaluationRequest;
use App\Http\Resources\Evaluation\EvaluationResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Evaluation\EvaluationService;
use DomainException;
use Illuminate\Http\JsonResponse;

class EvaluationsController
{
    public function __construct(protected EvaluationService $evaluationService) {}

    public function store(StoreEvaluationRequest $request): JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }

        try {
            $evaluation = $this->evaluationService->store($user, $request->validated());
        } catch (DomainException $exception) {
            $messageKey = $exception->getMessage();

            if ($messageKey === EvaluationService::ERROR_NOT_ALLOWED_TO_RATE) {
                return ApiResponse::error(__($messageKey), [], 403);
            }

            return ApiResponse::error(__($messageKey), [], 422);
        }

        return ApiResponse::success(
            EvaluationResource::make($evaluation)->toArray($request),
            __('api.evaluation_created'),
        );
    }
}
