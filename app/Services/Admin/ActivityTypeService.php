<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\ActivityType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ActivityTypeService
{
    /**
     * @return Collection<int, ActivityType>
     */
    public function listActivityTypes(): Collection
    {
        return ActivityType::query()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * @param  array{name: array{ar: string, en: string}}  $data
     */
    public function createActivityType(array $data): ActivityType
    {
        return ActivityType::query()->create([
            'name' => $data['name'],
        ]);
    }

    /**
     * @param  array{name: array{ar: string, en: string}}  $data
     */
    public function updateActivityType(ActivityType $activityType, array $data): ActivityType
    {
        $activityType->update([
            'name' => $data['name'],
        ]);

        return $activityType->fresh();
    }

    public function deleteActivityType(ActivityType $activityType): void
    {
        DB::transaction(static function () use ($activityType): void {
            $activityType->delete();
        });
    }
}
