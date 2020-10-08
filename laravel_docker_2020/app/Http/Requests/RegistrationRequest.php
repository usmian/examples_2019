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
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:12', 'max:255'], // TODO: сделать валидацию пароля с проверкой на криптостойкость(хотя бы 1 цифра символ, разные регистры..)
            'email' => "required|string"
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'Поле обязательно',
            'string' => 'Строка',
            'max' => 'Максимальная длина :max символов',
            'min' => 'Минимальная длина :min символов',
        ];
    }
}
