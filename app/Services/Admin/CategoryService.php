<?php

declare(strict_types=1);

namespace App\Services\Admin;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function listCategories(): Collection
    {
        return Category::query()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function createCategory(array $data): Category
    {
        return Category::query()->create([
            'name' => $data['name'],
            'dynamic_fields' => $this->normalizeDynamicFields($data),
        ]);
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update([
            'name' => $data['name'],
            'dynamic_fields' => $this->normalizeDynamicFields($data),
        ]);

        return $category->fresh();
    }

    public function deleteCategory(Category $category): void
    {
        DB::transaction(static function () use ($category): void {
            if ($category->services()->exists()) {
                throw ValidationException::withMessages([
                    'category' => __('admin.category_has_services'),
                ]);
            }

            $category->delete();
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
