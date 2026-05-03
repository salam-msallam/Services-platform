<?php

namespace App\Http\Requests\Report;

use App\Enums\StatusEnum;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreReportRequest extends FormRequest
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
                'order_id' => ['required' ,
                'integer',
                Rule::exists('orders', 'id')
                    ->where('status', StatusEnum::Accepted->value)
            ],

                'reason' => 'required|string|max:255',
            ];

    }
}
