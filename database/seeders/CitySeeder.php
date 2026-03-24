<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['en' => 'Damascus', 'ar' => 'دمشق', 'x' => 33.5138, 'y' => 36.2765],
            ['en' => 'Aleppo', 'ar' => 'حلب', 'x' => 36.2021, 'y' => 37.1343],
            ['en' => 'homs', 'ar' => 'حمص', 'x' => 34.7324, 'y' => 36.7137],
        ];

        foreach ($cities as $city) {
            City::query()->updateOrCreate(
                ['name->en' => $city['en']],
                [
                    'name' => [
                        'en' => $city['en'],
                        'ar' => $city['ar'],
                    ],
                    'x' => $city['x'],
                    'y' => $city['y'],
                ],
            );
        }
    }
}
