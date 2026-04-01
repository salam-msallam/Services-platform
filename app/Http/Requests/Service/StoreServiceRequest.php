<?php

declare(strict_types=1);

namespace App\Http\Requests\Service;

use App\Enums\StatusEnum;
use App\Models\Category;
use App\Models\Service;
use App\Models\SubCategory;
use App\Models\User;
use App\Support\DynamicFieldSchema;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    protected function prepareForValidation(): void
    {
        if ($this->input('sub_category_id') === '') {
            $this->merge(['sub_category_id' => null]);
        }

        $rawDynamic = $this->input('dynamic_values');

        if (is_string($rawDynamic) && $rawDynamic !== '') {
            $decoded = json_decode($rawDynamic, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->merge(['dynamic_values' => $decoded]);
            }
        }
    }

    public function rules(): array
    {
        return [
            'business_account_id' => [
                'required',
                'integer',
                Rule::exists('business_accounts', 'id')
                    ->where('user_id', (int) $this->user()?->id)
                    ->where('status', StatusEnum::Accepted->value)
                    ->whereNull('deleted_at'),
            ],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'sub_category_id' => [
                'nullable',
                'integer',
                Rule::exists('sub_categories', 'id')->where(
                    fn ($query) => $query->where('category_id', (int) $this->input('category_id'))
                ),
            ],
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'title' => ['required', 'array'],
            'title.ar' => ['required', 'string', 'max:255'],
            'title.en' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.ar' => ['nullable', 'string'],
            'description.en' => ['nullable', 'string'],
            'quantity' => ['required', 'integer', 'min:1'],
            'work_type' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'currency' => ['required', 'string', 'max:10'],
            'property_type' => [
                'required',
                'string',
                Rule::in([Service::PROPERTY_TYPE_SELLER, Service::PROPERTY_TYPE_RENT]),
            ],
            'dynamic_values' => ['nullable', 'array'],
            'main_image' => ['required', 'file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'images' => ['nullable', 'array'],
            'images.*' => ['file', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $categoryId = (int) $this->input('category_id');

            if ($categoryId < 1) {
                return;
            }

            $category =Category::query()->find($categoryId);

            if ($category === null) {
                return;
            }

            $subCategoryId = $this->input('sub_category_id');
            $subCategory = null;

            if ($subCategoryId !== null && $subCategoryId !== '') {
                $subCategory = SubCategory::query()->find((int) $subCategoryId);
            }

            $schema = DynamicFieldSchema::mergeForCategoryAndOptionalSub($category, $subCategory);
            $values = $this->input('dynamic_values');

            if (! is_array($values)) {
                $values = [];
            }

            DynamicFieldSchema::validateValuesAgainstSchema($schema, $values, 'dynamic_values', $v);
        });
    }
}
