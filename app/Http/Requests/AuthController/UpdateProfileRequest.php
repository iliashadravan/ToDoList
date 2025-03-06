<?php

namespace App\Http\Requests\AuthController;

use App\Http\Requests\Request;

class UpdateProfileRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstname' => 'required|string|max:255',
            'lastname'  => 'required|string|max:255',
            'phone'     => 'nullable|string|max:20|unique:users,phone,' . auth()->id(),
            'email'     => 'required|string|email|max:255|unique:users' . auth()->id(),
            'password'  => 'required|string|min:4',
        ];
    }
}
