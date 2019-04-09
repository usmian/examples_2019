<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;


Modal::begin([
    'id'=>'reset-modal',
]);
?>

    <p style="color:black">Для смены пароля введите секретную фразу:</p>
    <div  id="info-reset" style="color:red"></div>
<?php $form = ActiveForm::begin([
    'id' => 'reset-form',
    'enableAjaxValidation' => true,
    'action' => ['site/ajax-reset']
]);
echo $form->field($model, 'password_secret_phrase')->textInput(['id' => 'secret-phrase'])->label('фраза для смены пароля');
echo $form->field($model, 'password')->passwordInput(['id'=>'refresh-password','readonly'=>'readonly'])->label('поле станет активным если фраза верна',['id'=>'pass-label']);
?>

    <div class="form-group">
        <div class="text-right">

            <?php
            echo Html::button('Отмена', ['class' => 'btn btn-default', 'data-dismiss' => 'modal','style'=>'margin:5px;']);
            echo Html::submitButton('проверить фразу', ['class' => 'btn btn-primary','id'=>'reset-button', 'name' => 'reset-button','style'=>'margin:5px;']);
            ?>

        </div>
    </div>

<?php
ActiveForm::end();
Modal::end();