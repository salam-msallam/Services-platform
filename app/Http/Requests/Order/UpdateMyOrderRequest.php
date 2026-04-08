<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Enums\StatusEnum;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class UpdateMyOrderRequest extends FormRequest
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
            'business_account_id' => [
                'sometimes',
                'integer',
                Rule::exists('business_accounts', 'id')
                    ->where('user_id', (int) $this->user()?->id)
                    ->where('status', StatusEnum::Accepted->value)
                    ->whereNull('deleted_at'),
            ],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'date_of_need' => ['sometimes', 'nullable', 'date', 'after_or_equal:today'],
            'time_of_need' => ['sometimes', 'nullable', 'integer', 'between:0,23'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $order = $this->route('order');

            if (! $order instanceof \App\Models\Order) {
                return;
            }

            $order->loadMissing('service');
            $service = $order->service;

            if (! $service instanceof Service) {
                return;
            }

            if ($service->property_type === Service::PROPERTY_TYPE_RENT) {
                $effectiveDateOfNeed = $this->has('date_of_need')
                    ? $this->input('date_of_need')
                    : $order->date_of_need;
                $effectiveTimeOfNeed = $this->has('time_of_need')
                    ? $this->input('time_of_need')
                    : $order->time_of_need;

                if ($effectiveDateOfNeed === null || $effectiveDateOfNeed === '') {
                    $validator->errors()->add(
                        'date_of_need',
                        __('api.order_date_of_need_required_rent'),
                    );
                }

                if ($effectiveTimeOfNeed === null || $effectiveTimeOfNeed === '') {
                    $validator->errors()->add(
                        'time_of_need',
                        __('api.order_time_of_need_required_rent'),
                    );
                }
            }
        });
    }
}
