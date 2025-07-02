<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'cpf' => ['required', 'string', 'max:20', Rule::unique(User::class)->ignore($this->user()->id)],
            'cnh_category' => ['required', 'string', Rule::in(['A', 'B', 'AB', 'C', 'AC', 'D', 'E', 'ACC'])],
            'cnh_address' => ['required', 'string', 'max:1000'],
            'phone' => ['required', 'string', 'max:20'],
        ];
    }
}
