<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property string $name
 * @property string $password
 * @property string $email
 */
class RegistrationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Find the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => "required|string",
            'password' => "required|string",
            'email' => "required|string"
        ];
    }

    public function messages(): array
    {
        return [
            'name' => 'Невалидный юзернэйм',
            'password' => 'Невалидный пароль',
            'email' => 'Невалидный емэйл'
        ];
    }
}
