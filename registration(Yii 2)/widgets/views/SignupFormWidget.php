<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

Modal::begin([
    'id' => 'signup-modal',
]);

$this->title = 'Signup';
$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="site-signup">
        <p style="color:black">Please fill out the following fields to signup:</p>

        <?php $form = ActiveForm::begin(['id' => 'form-signup',
            'enableAjaxValidation' => true,
            'action' => ['site/signup']]); ?>
        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <?= $form->field($model, 'password_secret_phrase') ?>
        <div class="form-group">
            <?= Html::submitButton('Зарегистрироваться', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
        </div>
    </div>
<?php
ActiveForm::end();
Modal::end();