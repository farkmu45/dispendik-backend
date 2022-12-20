<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'date_of_birth' => 'date|required',
            'phone_number' => 'string|required',
            'institution_id' => '',
        ];
    }

    public function attributes()
    {
        return [
            'date_of_birth' => 'tanggal lahir',
            'phone_number' => 'nomor telepon',
            'institution_id' => 'ID institusi',
        ];
    }
}
