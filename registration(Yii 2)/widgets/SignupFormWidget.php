<?php

namespace app\widgets;

use Yii;
use yii\base\Widget;
use app\models\SignupForm;

class SignupFormWidget extends Widget {

    public function run() {

            $model = new SignupForm();
            return $this->render('SignupFormWidget', [
                'model' => $model,
            ]);
    }

}