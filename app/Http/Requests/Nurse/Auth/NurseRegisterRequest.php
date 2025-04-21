<?php

namespace App\Http\Requests\Nurse\Auth;

use Illuminate\Foundation\Http\FormRequest;

class NurseRegisterRequest extends FormRequest
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
            'email' => ['required', 'email', 'unique:nurses,email'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'in:male,female'],
            'union_number' => ['nullable', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'national_id' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],

            // صور إلزامية
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'id_card_front' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'id_card_back' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'union_card_back' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'criminal_record' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => 'البريد الإلكتروني مستخدم بالفعل.',
            'password.confirmed' => 'تأكيد كلمة المرور غير مطابق.',
            'profile_image.required' => 'الصورة الشخصية مطلوبة.',
            'id_card_front.required' => 'صورة البطاقة (الوجه) مطلوبة.',
            'id_card_back.required' => 'صورة البطاقة (الخلف) مطلوبة.',
            'union_card_back.required' => 'صورة النقابة مطلوبة.',
            'criminal_record.required' => 'صورة الفيش الجنائي مطلوبة.',
        ];
    }
}
