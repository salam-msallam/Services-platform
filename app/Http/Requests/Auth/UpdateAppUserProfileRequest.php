<?php

declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAppUserProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user instanceof User
            && $user->type === 'app_user'
            && $user->appUser !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $appUserId = $this->user()?->appUser?->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'phone' => [
                'sometimes',
                'required',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s()]+$/',
                Rule::unique('app_users', 'phone')->ignore($appUserId),
            ],
        ];
    }
}
