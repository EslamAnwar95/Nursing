<?php

namespace App\Http\Requests\Patient\Auth;

use Illuminate\Foundation\Http\FormRequest;

class PatientRegisterRequest extends FormRequest
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
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:patients,email'],
            'phone_number' => ['required', 'string', 'max:15', 'unique:patients,phone_number'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'date_of_birth' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'full_name.required' => 'الاسم بالكامل مطلوب.',
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'صيغة البريد غير صحيحة.',
            'email.unique' => 'هذا البريد مسجل مسبقًا.',
            'phone_number.required' => 'رقم الهاتف مطلوب.',
            'phone_number.unique' => 'رقم الهاتف مستخدم بالفعل.',
            'password.required' => 'كلمة المرور مطلوبة.',
            'password.min' => 'كلمة المرور يجب ألا تقل عن 6 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ];
    }
}
