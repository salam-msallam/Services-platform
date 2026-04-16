<?php

namespace App\Http\Controllers\Favorite;

use App\Http\Controllers\Controller;
use App\Http\Requests\Favorite\StoreFavoriteRequest;
use App\Http\Resources\Favorite\FavoriteResource;
use App\Http\Responses\ApiResponse;
use App\Models\User;
use App\Services\Favorite\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    public function __construct(protected FavoriteService $favoriteService) {}

    public function toggle(StoreFavoriteRequest $request){
        $user = $request->user();


        if (! $user instanceof User) {
            return ApiResponse::error(__('auth.unauthenticated'), [], 401);
        }
        $favorite=$this->favoriteService->toggleFavorite($user,$request->validated());
        if(!$favorite){
            return ApiResponse::success([], __('api.favorite_deleted'));

        }
        return ApiResponse::success(
            FavoriteResource::make($favorite)->toArray($request),
            __('api.favorite_created'),
        );
    }

}
