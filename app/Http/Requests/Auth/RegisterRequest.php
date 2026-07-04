<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'affiliate_code' => $this->filled('affiliate_code')
                ? strtoupper(trim((string) $this->input('affiliate_code')))
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'school'   => ['nullable', 'string', 'max:255'],
            'city'     => ['nullable', 'string', 'max:255'],
            'grade_id' => ['required', 'integer', 'exists:grades,id'],
            'affiliate_code' => [
                'nullable', 'string', 'max:32',
                Rule::exists('users', 'affiliate_code')->where(fn ($q) => $q->where('role', 'student')),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.unique'       => 'Email sudah terdaftar.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'grade_id.required'  => 'Kelas wajib dipilih.',
            'grade_id.exists'    => 'Kelas yang dipilih tidak valid.',
            'affiliate_code.exists'=> 'Kode affiliate tidak ditemukan.',
        ];
    }
}
