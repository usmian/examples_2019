<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

Modal::begin([
    'id'=>'login-modal',
]);
?>

    <p style="color:black">Please fill out the following fields to login:</p>

<?php $form = ActiveForm::begin([
    'id' => 'login-form',
    'enableAjaxValidation' => true,
    'action' => ['site/ajax-login'],
]);
echo $form->field($model, 'username')->textInput();
echo $form->field($model, 'password')->passwordInput();
echo $form->field($model, 'rememberMe')->checkbox();
?>

    <div class="form-group">
        <div class="text-right">

            <?php
            echo Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal','style'=>'margin:5px;']);
            echo Html::submitButton('Войти', ['class' => 'btn btn-primary', 'name' => 'login-button','style'=>'margin:5px;']);
            ?>

        </div>
    </div>

<?php
ActiveForm::end();
Modal::end();