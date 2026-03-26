<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\City;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CityService
{
    /**
     * @return Collection<int, City>
     */
    public function listCities(): Collection
    {
        return City::query()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @param  array{name: array{ar: string, en: string}}  $data
     */
    public function createCity(array $data): City
    {
        return City::query()->create([
            'name' => $data['name'],
        ]);
    }

    /**
     * @param  array{name: array{ar: string, en: string}}  $data
     */
    public function updateCity(City $city, array $data): City
    {
        $city->update([
            'name' => $data['name'],
        ]);

        return $city->fresh();
    }

    public function deleteCity(City $city): void
    {
        DB::transaction(static function () use ($city): void {
            $city->delete();
        });
    }
}
