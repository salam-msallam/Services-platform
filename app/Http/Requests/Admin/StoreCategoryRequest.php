<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use App\Http\Requests\Admin\Concerns\NormalizesAdminDynamicFields;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreCategoryRequest extends FormRequest
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
        });
    }

    protected function prepareForValidation(): void
    {
        $this->normalizeAdminDynamicFieldsInput();
    }
}
