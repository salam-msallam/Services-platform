<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [
            ['en' => 'Damascus', 'ar' => 'دمشق'],
            ['en' => 'Aleppo', 'ar' => 'حلب'],
            ['en' => 'homs', 'ar' => 'حمص'],
        ];

        foreach ($cities as $city) {
            City::query()->updateOrCreate(
                ['name->en' => $city['en']],
                [
                    'name' => [
                        'en' => $city['en'],
                        'ar' => $city['ar'],
                    ],
                ],
            );
        }
    }
}
