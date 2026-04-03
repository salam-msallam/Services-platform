<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Concerns;

use Illuminate\Validation\Validator;

trait NormalizesAdminDynamicFields
{
    protected function normalizeAdminDynamicFieldsInput(): void
    {
        $dynamicFields = $this->input('dynamic_fields');

        if (! is_array($dynamicFields)) {
            return;
        }

        $normalizedFields = [];

        foreach ($dynamicFields as $field) {
            if (! is_array($field)) {
                continue;
            }

            $type = trim((string) data_get($field, 'type', ''));
            $key = strtolower(trim((string) data_get($field, 'key', '')));

            $row = [
                'key' => $key,
                'label' => [
                    'ar' => trim((string) data_get($field, 'label.ar', '')),
                    'en' => trim((string) data_get($field, 'label.en', '')),
                ],
                'type' => $type,
            ];

            if ($type === 'dropdown') {
                $raw = data_get($field, 'options_text');
                $opts = [];

                if (is_string($raw) && $raw !== '') {
                    $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
                    $opts = array_values(array_filter(
                        array_map(static fn(string $s): string => trim($s), $lines),
                        static fn(string $s): bool => $s !== ''
                    ));
                }

                $row['options'] = $opts;
            }

            $normalizedFields[] = $row;
        }

        $this->merge([
            'dynamic_fields' => array_values($normalizedFields),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    protected function adminDynamicFieldsRules(): array
    {
        return [
            'dynamic_fields' => ['nullable', 'array'],
            'dynamic_fields.*' => ['required', 'array'],
            'dynamic_fields.*.key' => ['required', 'string', 'regex:/^[a-z0-9_]+$/', 'max:64', 'distinct'],
            'dynamic_fields.*.label' => ['required', 'array'],
            'dynamic_fields.*.label.ar' => ['required', 'string', 'max:255'],
            'dynamic_fields.*.label.en' => ['required', 'string', 'max:255'],
            'dynamic_fields.*.type' => ['required', 'string', \Illuminate\Validation\Rule::in(['text', 'number', 'checkbox', 'dropdown'])],
            'dynamic_fields.*.options' => ['nullable', 'array'],
            'dynamic_fields.*.options.*' => ['string', 'max:255'],
        ];
    }

    protected function validateAdminDropdownOptions(Validator $validator): void
    {
        $fields = $this->input('dynamic_fields', []);

        if (! is_array($fields)) {
            return;
        }

        foreach ($fields as $index => $field) {
            if (! is_array($field)) {
                continue;
            }

            if (($field['type'] ?? '') !== 'dropdown') {
                continue;
            }

            $opts = $field['options'] ?? [];

            if (! is_array($opts) || $opts === []) {
                $validator->errors()->add(
                    "dynamic_fields.{$index}.options",
                    __('validation.required', ['attribute' => __('admin.field_dropdown_options')])
                );

                continue;
            }

            $nonEmpty = array_filter(
                $opts,
                static fn(mixed $o): bool => is_string($o) && trim($o) !== ''
            );

            if ($nonEmpty === []) {
                $validator->errors()->add(
                    "dynamic_fields.{$index}.options",
                    __('validation.required', ['attribute' => __('admin.field_dropdown_options')])
                );
            }
        }
    }
}
