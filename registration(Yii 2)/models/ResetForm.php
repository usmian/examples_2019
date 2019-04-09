<?php
/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 09.10.2017
 * Time: 10:58
 */


namespace app\models;

use yii\base\Model;

class ResetForm extends Model
{
    public $password_secret_phrase;
    public $password;

    public function rules()
    {
        return [
            // username and password are both required
            [['password', 'password_secret_phrase'], 'safe'],
            // rememberMe must be a boolean value
        ];
    }
}