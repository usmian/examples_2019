<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;

use app\assets\AppAsset;
use app\widgets\LoginFormWidget;
use app\widgets\SignupFormWidget;
use app\widgets\ResetPasswordWidget;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>
<?= (Yii::$app->user->isGuest ? LoginFormWidget::widget([]) : ''); ?>
<?= SignupFormWidget::widget([]); ?>
<?= (!(Yii::$app->user->isGuest) ? ResetPasswordWidget::widget([]) : ''); ?>

<div class="container">

    <?= $content ?>
</div>

<div class="site-wrapper">

    <div class="site-wrapper-inner">

        <div class="container">

            <div class="masthead clearfix">
                <div class="container inner">
                    <h3 class="masthead-brand">Cover</h3>
                    <nav>
                        <ul class="nav masthead-nav">
                            <?php if (Yii::$app->user->isGuest) {
                                echo '<li class="active">' .
                                    Html::a(
                                        'Вход', '#', ['data-toggle' => 'modal', 'data-target' => '#login-modal'])
                                    . '</li>';
                            } else {
                                echo '<li>' . '<a href="#">'
                                    . Html::beginForm(['/site/logout'], 'post')
                                    . Html::submitButton(
                                        'Выйти (' . Yii::$app->user->identity->username . ')',
                                        ['class' => 'logout']
                                    )
                                    . Html::endForm()
                                    . '</a>' . '</li>';
                            }

                            echo '<li>' . Html::a('Регистрация', '#', ['data-toggle' => 'modal', 'data-target' => '#signup-modal']) . '</li>';
                            if (!Yii::$app->user->isGuest)
                                echo '<li>' . Html::a('Сменить пароль', '#', ['data-toggle' => 'modal', 'data-target' => '#reset-modal']) . '</li>';
                            ?>
                        </ul>
                    </nav>
                </div>
            </div>

            <div class="inner cover">
                <h1 class="cover-heading"></h1>
                    <a href="#" class="btn btn-lg btn-default">Learn more</a>
                </p>
            </div>

        </div>

    </div>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
