<?php

namespace App\Http\Requests\Favorite;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFavoriteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'service_id' => [
                'required',
                'integer',
                // Rule::exists('services', 'id')->where(function ($query) {
                //     $query->whereNull('deleted_at');
                // }),
            ]
        ];
    }
    public function messages(): array
    {
        return [
            'service_id.required' => __('api.favorite_validation_service_id_required'),
            'service_id.integer' => __('api.favorite_validation_service_id_integer'),
            'service_id.exists' => __('api.favorite_validation_service_id_exists'),
        ];
    }
}
