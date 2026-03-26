<?php

namespace App\Http\Requests;

use App\Models\Admin;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAdminRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('manage admins');
    }

    /**
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Admin|null $admin */
        $admin = $this->route('admin');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('admins', 'email')->ignore($admin?->id),
            ],
            'password' => 'nullable|string|min:6|confirmed',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => [
                'integer',
                Rule::exists('roles', 'id')->where('guard_name', 'web'),
            ],
        ];
    }
}
