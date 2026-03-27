<?php

namespace App\Http\Requests;

use App\Models\AppUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('edit user');
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var AppUser|null $appUser */
        $appUser = $this->route('appUser');

        return [
            'name' => [ 'string', 'max:255'],
            'phone' => [
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/',
                Rule::unique('app_users', 'phone')->ignore($appUser?->id),
            ],
        ];
    }
}
