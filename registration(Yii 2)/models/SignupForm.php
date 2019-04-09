<?php
/**
 * Created by PhpStorm.
 * User: Усиков
 * Date: 05.10.2017
 * Time: 5:37
 */

namespace app\models;


use yii\base\Model;


class SignupForm extends Model
{

    public $username;
    public $email;
    public $password;
    public $password_secret_phrase;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 1, 'max' => 255],
            ['password_secret_phrase', 'trim'],
            ['password_secret_phrase', 'required'],
            ['password_secret_phrase', 'string', 'min' => 1, 'max' => 255],
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\app\models\User', 'message' => 'This email address has already been taken.'],
            ['password', 'required'],
            ['password', 'string', 'min' => 1],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
    }
        $user = new User();
        $user->username = $this->username;
        $user->email = $this->email;
        $user->password_secret_phrase = $this->password_secret_phrase;
        $user->setPassword($this->password);
        $user->generateAuthKey();
        return $user->save() ? $user : null;
    }

}