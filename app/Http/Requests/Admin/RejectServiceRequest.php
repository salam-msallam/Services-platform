<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RejectServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user()?->can('reject services');
    }

    public function rules(): array
    {
        return [];
    }
}

