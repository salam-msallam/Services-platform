<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\NormalizesAdminDynamicFields;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreSubCategoryRequest extends FormRequest
{
    use NormalizesAdminDynamicFields;

    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage sub-categories');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge([
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
        ], $this->adminDynamicFieldsRules());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $this->validateAdminDropdownOptions($v);

            $categoryId = (int) $this->input('category_id');
            $fields = $this->input('dynamic_fields', []);

            if ($categoryId < 1 || ! is_array($fields) || $fields === []) {
                return;
            }

            $category = Category::query()->find($categoryId);

            if ($category === null) {
                return;
            }

            $categoryKeys = [];
            $categoryFields = $category->dynamic_fields;

            if (is_array($categoryFields)) {
                foreach ($categoryFields as $f) {
                    if (! is_array($f)) {
                        continue;
                    }

                    $k = strtolower(trim((string) ($f['key'] ?? '')));

                    if ($k !== '') {
                        $categoryKeys[] = $k;
                    }
                }
            }

            if ($categoryKeys === []) {
                return;
            }

            $subKeys = [];

            foreach ($fields as $f) {
                if (! is_array($f)) {
                    continue;
                }

                $k = strtolower(trim((string) ($f['key'] ?? '')));

                if ($k !== '') {
                    $subKeys[] = $k;
                }
            }

            $overlap = array_values(array_unique(array_intersect($subKeys, $categoryKeys)));

            if ($overlap !== []) {
                $v->errors()->add(
                    'dynamic_fields',
                    'Dynamic field keys must be unique across category and sub-category. Duplicated keys: '.implode(', ', $overlap)
                );
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeAdminDynamicFieldsInput();
    }
}
