<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Service;

use App\Enums\StatusEnum;
use App\Http\Requests\Service\ServiceBrowseRequest;
use App\Http\Resources\Service\ServiceResource;
use App\Http\Responses\ApiResponse;
use App\Models\Service;
use Illuminate\Http\JsonResponse;

class ServiceIndexController
{
    public function __invoke(ServiceBrowseRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $perPage = isset($validated['per_page']) ? (int) $validated['per_page'] : 15;
        $perPage = max(1, min(50, $perPage));

        $query = Service::query()
            ->where('status', StatusEnum::Accepted)
            ->with(['businessAccount', 'category', 'subCategory', 'city', 'media'])
            ->orderByDesc('created_at');

        if (isset($validated['city_id'])) {
            $query->where('city_id', (int) $validated['city_id']);
        }

        if (isset($validated['category_id'])) {
            $query->where('category_id', (int) $validated['category_id']);
        }

        if (isset($validated['sub_category_id'])) {
            $query->where('sub_category_id', (int) $validated['sub_category_id']);
        }

        if (isset($validated['price_min'])) {
            $query->where('price', '>=', (string) $validated['price_min']);
        }

        if (isset($validated['price_max'])) {
            $query->where('price', '<=', (string) $validated['price_max']);
        }

        if (isset($validated['property_type'])) {
            $query->where('property_type', (string) $validated['property_type']);
        }

        $search = isset($validated['search']) ? trim((string) $validated['search']) : '';

        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';

            $query->where(function ($q) use ($like): void {
                $q->where('title->ar', 'like', $like)
                    ->orWhere('title->en', 'like', $like);
            });
        }

        $paginator = $query->paginate($perPage)->withQueryString();

        $payload = [
            'items' => ServiceResource::collection($paginator->getCollection())->resolve(),
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];

        return ApiResponse::success($payload, __('api.services_browse_fetched'));
    }
}
