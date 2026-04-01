<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Validation\Validator;

final class DynamicFieldSchema
{
    public static function merge(?array $categoryFields, ?array $subCategoryFields): array
    {
        $merged = [];

        foreach (self::onlyWithKey($categoryFields) as $field) {
            $key = (string) $field['key'];
            $merged[$key] = $field;
        }

        foreach (self::onlyWithKey($subCategoryFields) as $field) {
            $key = (string) $field['key'];
            $merged[$key] = $field;
        }

        return $merged;
    }

    public static function onlyWithKey(?array $fields): array
    {
        if ($fields === null || $fields === []) {
            return [];
        }

        $out = [];

        foreach ($fields as $field) {
            if (! is_array($field)) {
                continue;
            }

            $key = isset($field['key']) ? trim((string) $field['key']) : '';

            if ($key === '') {
                continue;
            }

            $field['key'] = $key;
            $out[] = $field;
        }

        return $out;
    }

    public static function mergeForCategoryAndOptionalSub(Category $category, ?SubCategory $subCategory): array
    {
        $subFields = null;

        if ($subCategory !== null && (int) $subCategory->category_id === (int) $category->id) {
            $subFields = $subCategory->dynamic_fields;
        }

        return self::merge($category->dynamic_fields, is_array($subFields) ? $subFields : null);
    }

    public static function validateValuesAgainstSchema(
        array $schemaByKey,
        array $values,
        string $attributePrefix,
        Validator $validator
    ): void {
        if ($schemaByKey === []) {
            if ($values !== []) {
                $validator->errors()->add($attributePrefix, __('api.dynamic_values_must_be_empty'));
            }

            return;
        }

        $schemaKeys = array_keys($schemaByKey);
        $valueKeys = array_keys($values);

        sort($schemaKeys);
        $sortedValueKeys = $valueKeys;
        sort($sortedValueKeys);

        if ($schemaKeys !== $sortedValueKeys) {
            $validator->errors()->add($attributePrefix, __('api.dynamic_values_keys_mismatch'));

            return;
        }

        foreach ($schemaByKey as $key => $def) {
            $type = (string) ($def['type'] ?? 'text');
            $raw = $values[$key];

            match ($type) {
                'text' => self::assertText($validator, $attributePrefix, $key, $raw),
                'number' => self::assertNumber($validator, $attributePrefix, $key, $raw),
                'checkbox' => self::assertCheckbox($validator, $attributePrefix, $key, $raw),
                'dropdown' => self::assertDropdown($validator, $attributePrefix, $key, $raw, $def),
                default => $validator->errors()->add(
                    "{$attributePrefix}.{$key}",
                    __('api.dynamic_field_unknown_type')
                ),
            };
        }
    }

    private static function assertText(
        Validator $validator,
        string $prefix,
        string $key,
        mixed $raw
    ): void {
        if (! is_string($raw) || trim($raw) === '') {
            $validator->errors()->add("{$prefix}.{$key}", __('api.dynamic_field_text_required'));
        }
    }

    private static function assertNumber(
        \Illuminate\Validation\Validator $validator,
        string $prefix,
        string $key,
        mixed $raw
    ): void {
        if (! is_numeric($raw)) {
            $validator->errors()->add("{$prefix}.{$key}", __('api.dynamic_field_number_invalid'));

            return;
        }

        if (is_string($raw) && trim($raw) === '') {
            $validator->errors()->add("{$prefix}.{$key}", __('api.dynamic_field_number_invalid'));
        }
    }

    private static function assertCheckbox(
        \Illuminate\Validation\Validator $validator,
        string $prefix,
        string $key,
        mixed $raw
    ): void {
        if (! is_bool($raw)) {
            $validator->errors()->add("{$prefix}.{$key}", __('api.dynamic_field_checkbox_invalid'));
        }
    }
    private static function assertDropdown(
        \Illuminate\Validation\Validator $validator,
        string $prefix,
        string $key,
        mixed $raw,
        array $def
    ): void {
        if (! is_string($raw) || $raw === '') {
            $validator->errors()->add("{$prefix}.{$key}", __('api.dynamic_field_dropdown_required'));

            return;
        }

        $options = $def['options'] ?? [];

        if (! is_array($options)) {
            $validator->errors()->add("{$prefix}.{$key}", __('api.dynamic_field_dropdown_invalid'));

            return;
        }

        $allowed = [];

        foreach ($options as $opt) {
            if (is_string($opt) && $opt !== '') {
                $allowed[] = $opt;
            }
        }

        if (! in_array($raw, $allowed, true)) {
            $validator->errors()->add("{$prefix}.{$key}", __('api.dynamic_field_dropdown_invalid'));
        }
    }
}
