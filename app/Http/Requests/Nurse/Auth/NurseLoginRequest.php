<?php

namespace App\Http\Requests\Nurse\Auth;

use Illuminate\Foundation\Http\FormRequest;

class NurseLoginRequest extends FormRequest
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
            'email' => ['required', 'email', 'exists:nurses,email'],
            'password' => ['required', 'string', 'min:6'],
            'fcm_token' => 'required|string',
            'device_type' => 'required|in:android,ios',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('messages.email_required'),
            'email.email' => __('messages.email_invalid'),
            'email.exists' => __('messages.email_not_found'),
            'password.required' => __('messages.password_required'),
            'password.min' => __('messages.password_min'),
            'fcm_token.required' => __('messages.fcm_token_required'),
            'device_type.required' => __('messages.device_type_required'),
            'device_type.in' => __('messages.device_type_invalid'),
           
        ];
    }
  
}
