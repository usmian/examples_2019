<?php
/**
 * Created by PhpStorm.
 * User: usikov
 * Date: 09.10.2017
 * Time: 10:54
 */

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\ResetForm;

class ResetPasswordWidget extends Widget
{
    public function run() {
        if (!(Yii::$app->user->isGuest)){
            $model = new ResetForm();
            return $this->render('ResetPasswordWidget', [
                'model' => $model,
            ]);
        }else{
            return false;
        }
    }

}