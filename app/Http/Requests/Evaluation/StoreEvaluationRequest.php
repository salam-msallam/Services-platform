<?php

declare(strict_types=1);

namespace App\Http\Requests\Evaluation;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEvaluationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() instanceof User;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')->whereNull('deleted_at'),
            ],
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'service_id.required' => __('api.evaluation_validation_service_id_required'),
            'service_id.integer' => __('api.evaluation_validation_service_id_integer'),
            'service_id.exists' => __('api.evaluation_validation_service_id_exists'),
            'rating.required' => __('api.evaluation_validation_rating_required'),
            'rating.integer' => __('api.evaluation_validation_rating_integer'),
            'rating.between' => __('api.evaluation_validation_rating_between'),
            'comment.string' => __('api.evaluation_validation_comment_string'),
        ];
    }
}
