<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\SubCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class SubCategoryService
{
    public function listSubCategories(): Collection
    {
        return SubCategory::query()
            ->with('category')
            ->orderByDesc('created_at')
            ->get();
    }

    public function createSubCategory(array $data): SubCategory
    {
        return SubCategory::query()->create([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'dynamic_fields' => $this->normalizeDynamicFields($data),
        ]);
    }

    public function updateSubCategory(SubCategory $subCategory, array $data): SubCategory
    {
        $subCategory->update([
            'category_id' => $data['category_id'],
            'name' => $data['name'],
            'dynamic_fields' => $this->normalizeDynamicFields($data),
        ]);

        return $subCategory->fresh();
    }

    public function deleteSubCategory(SubCategory $subCategory): void
    {
        DB::transaction(static function () use ($subCategory): void {
            $subCategory->delete();
        });
    }

    private function normalizeDynamicFields(array $data): ?array
    {
        if (! isset($data['dynamic_fields']) || ! is_array($data['dynamic_fields']) || $data['dynamic_fields'] === []) {
            return null;
        }

        return array_values(array_map(
            static function (array $field): array {
                $type = (string) data_get($field, 'type');
                $out = [
                    'key' => strtolower(trim((string) data_get($field, 'key'))),
                    'label' => [
                        'ar' => (string) data_get($field, 'label.ar'),
                        'en' => (string) data_get($field, 'label.en'),
                    ],
                    'type' => $type,
                ];

                if ($type === 'dropdown') {
                    $opts = data_get($field, 'options', []);
                    $out['options'] = is_array($opts)
                        ? array_values(array_filter(
                            array_map(
                                static fn (mixed $o): string => is_string($o) ? trim($o) : '',
                                $opts
                            ),
                            static fn (string $s): bool => $s !== ''
                        ))
                        : [];
                }

                return $out;
            },
            $data['dynamic_fields']
        ));
    }
}
