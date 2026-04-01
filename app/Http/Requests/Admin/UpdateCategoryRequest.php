<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\NormalizesAdminDynamicFields;
use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateCategoryRequest extends FormRequest
{
    use NormalizesAdminDynamicFields;

    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage categories');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return array_merge([
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
        ], $this->adminDynamicFieldsRules());
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $this->validateAdminDropdownOptions($v);

            $category = $this->route('category');

            if (! $category instanceof Category) {
                return;
            }

            $fields = $this->input('dynamic_fields', []);

            if (! is_array($fields) || $fields === []) {
                return;
            }

            $newCategoryKeys = [];

            foreach ($fields as $f) {
                if (! is_array($f)) {
                    continue;
                }

                $k = strtolower(trim((string) ($f['key'] ?? '')));

                if ($k !== '') {
                    $newCategoryKeys[] = $k;
                }
            }

            if ($newCategoryKeys === []) {
                return;
            }

            $subKeys = [];
            $category->loadMissing('subCategories');

            foreach ($category->subCategories as $sub) {
                $subFields = $sub->dynamic_fields;

                if (! is_array($subFields)) {
                    continue;
                }

                foreach ($subFields as $sf) {
                    if (! is_array($sf)) {
                        continue;
                    }

                    $k = strtolower(trim((string) ($sf['key'] ?? '')));

                    if ($k !== '') {
                        $subKeys[] = $k;
                    }
                }
            }

            if ($subKeys === []) {
                return;
            }

            $overlap = array_values(array_unique(array_intersect($newCategoryKeys, $subKeys)));

            if ($overlap !== []) {
                $v->errors()->add(
                    'dynamic_fields',
                    'Dynamic field keys must be unique across category and sub-categories. Duplicated keys: '.implode(', ', $overlap)
                );
            }
        });
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeAdminDynamicFieldsInput();
    }
}
