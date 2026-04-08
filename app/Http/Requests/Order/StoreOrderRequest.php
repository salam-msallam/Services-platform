<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use App\Enums\StatusEnum;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
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
                'required',
                'integer',
                Rule::exists('business_accounts', 'id')
                    ->where('user_id', (int) $this->user()?->id)
                    ->where('status', StatusEnum::Accepted->value)
                    ->whereNull('deleted_at'),
            ],
            'service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')
                    ->where('status', StatusEnum::Accepted->value)
                    ->whereNull('deleted_at'),
            ],
            'quantity' => ['required', 'integer', 'min:1'],
            'date_of_need' => ['nullable', 'date', 'after_or_equal:today'],
            'time_of_need' => ['nullable', 'integer', 'between:0,23'],
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            if ($validator->errors()->isNotEmpty()) {
                return;
            }

            $serviceId = $this->input('service_id');

            if ($serviceId === null || $serviceId === '') {
                return;
            }

            $service = Service::query()->find((int) $serviceId);

            if ($service === null) {
                return;
            }

            $buyerBusinessAccountId = (int) $this->input('business_account_id');

            if ($buyerBusinessAccountId === (int) $service->business_account_id) {
                $validator->errors()->add(
                    'business_account_id',
                    __('api.order_cannot_order_own_service'),
                );

                return;
            }

            if ($service->property_type === Service::PROPERTY_TYPE_RENT) {
                if (! $this->filled('date_of_need')) {
                    $validator->errors()->add(
                        'date_of_need',
                        __('api.order_date_of_need_required_rent'),
                    );
                }

                if (! $this->filled('time_of_need')) {
                    $validator->errors()->add(
                        'time_of_need',
                        __('api.order_time_of_need_required_rent'),
                    );
                }
            }
        });
    }
}
