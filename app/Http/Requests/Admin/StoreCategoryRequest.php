<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage categories');
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'array'],
            'name.ar' => ['required', 'string', 'max:255'],
            'name.en' => ['required', 'string', 'max:255'],
            'dynamic_fields' => ['nullable', 'array'],
            'dynamic_fields.*' => ['required', 'array'],
            'dynamic_fields.*.label' => ['required', 'array'],
            'dynamic_fields.*.label.ar' => ['required', 'string', 'max:255'],
            'dynamic_fields.*.label.en' => ['required', 'string', 'max:255'],
            'dynamic_fields.*.type' => ['required', 'string', Rule::in(['text', 'number', 'checkbox', 'dropdown'])],
        ];
    }

    protected function prepareForValidation(): void
    {
        $dynamicFields = $this->input('dynamic_fields');

        if (! is_array($dynamicFields)) {
            return;
        }

        $normalizedFields = array_values(array_map(
            static function (array $field): array {
                return [
                    'label' => [
                        'ar' => trim((string) data_get($field, 'label.ar', '')),
                        'en' => trim((string) data_get($field, 'label.en', '')),
                    ],
                    'type' => trim((string) data_get($field, 'type', '')),
                ];
            },
            $dynamicFields
        ));

        $this->merge([
            'dynamic_fields' => $normalizedFields,
        ]);
    }
}
