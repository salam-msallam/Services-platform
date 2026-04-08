<?php

declare(strict_types=1);

namespace App\Http\Requests\Service;

use App\Models\Service;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class ServiceBrowseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'city_id' => ['sometimes', 'integer', 'exists:cities,id'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'sub_category_id' => [
                'sometimes',
                'integer',
                Rule::exists('sub_categories', 'id')->where(function ($query) {
                    $categoryId = $this->input('category_id');

                    if ($categoryId === null || $categoryId === '') {
                        return $query;
                    }

                    return $query->where('category_id', (int) $categoryId);
                }),
            ],
            'price_min' => ['sometimes', 'numeric', 'min:0'],
            'price_max' => ['sometimes', 'numeric', 'min:0'],
            'property_type' => [
                'sometimes',
                'string',
                Rule::in([Service::PROPERTY_TYPE_SELLER, Service::PROPERTY_TYPE_RENT]),
            ],
            'search' => ['sometimes', 'string', 'max:255'],
            'page' => ['sometimes', 'integer', 'min:1'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:50'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v): void {
            $min = $this->input('price_min');
            $max = $this->input('price_max');

            if ($min === null || $max === null || $min === '' || $max === '') {
                return;
            }

            if ((float) $min > (float) $max) {
                $v->errors()->add('price_max', __('api.services_browse_price_range_invalid'));
            }
        });
    }
}
