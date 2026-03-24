<?php

namespace Database\Seeders;

use App\Models\ActivityType;
use Illuminate\Database\Seeder;

class ActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        $activityTypes = [
            ['en' => 'Construction contractor', 'ar' => 'مقاول انشاءات'],
            ['en' => 'Interior designer', 'ar' => 'مصمم ديكور'],
            ['en' => 'Material supplier', 'ar' => 'مورد مواد'],
            ['en' => 'Real estate broker', 'ar' => 'وسيط عقاري'],
        ];

        foreach ($activityTypes as $item) {
            ActivityType::query()->updateOrCreate(
                ['name->en' => $item['en']],
                ['name' => ['en' => $item['en'], 'ar' => $item['ar']]],
            );
        }
    }
}
