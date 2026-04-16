<?php

declare(strict_types=1);

namespace App\Services\Favorite;


use App\Models\Favorite;
use App\Models\Service;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;

class FavoriteService
{
    public function toggleFavorite(User $user, array $data){
        $exists = Favorite::where('user_id', $user->id)
                          ->where('service_id', $data['service_id'])
                          ->first();
       if($exists){
        $exists->forceDelete();
        return ;
       }
        $favorite=$user->favorites()->create([
            'service_id'=>$data['service_id']
    ]);
       return $favorite;

    }

}
