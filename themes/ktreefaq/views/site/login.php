<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm*/

$this->title = 'Sign In';

$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback','autocomplete'=>'off'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-envelope form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>

<div class="login-box">
    <div class="login-logo">
        <?php $adminSettings = Yii::$app->getSettings()?>
        <a href="<?= Yii::$app->homeUrl?>"><b><?= $adminSettings['site_name']?></b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">
        <p class="login-box-msg">Login </p>

        <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => true,'options'=>['autocomplete'=>'off']]); ?>

        <?= $form
            ->field($model, 'user_username', $fieldOptions1)
            ->label('User Username')
            ->textInput(['placeholder' => $model->getAttributeLabel('user_username'),'autocomplete'=>'off']) ?>

        <?= $form
            ->field($model, 'user_password', $fieldOptions2)
            ->label('User Password')
            ->passwordInput(['placeholder' => $model->getAttributeLabel('user_password')]) ?>

        <div class="row">
            <div class="col-xs-8">
                <?= $form->field($model, 'rememberMe')->label('Remember me')->checkbox() ?>
            </div>
            <!-- /.col -->
            <div class="col-xs-4">
                <?= Html::submitButton('Sign in', ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
            </div>
            <!-- /.col -->
        </div>


        <?php ActiveForm::end(); ?>
    <!--
        <div class="social-auth-links text-center">
            <p>- OR -</p>
            <a href="#" class="btn btn-block btn-social btn-facebook btn-flat"><i class="fa fa-facebook"></i> Sign in
                using Facebook</a>
            <a href="#" class="btn btn-block btn-social btn-google-plus btn-flat"><i class="fa fa-google-plus"></i> Sign
                in using Google+</a>
        </div>
        <!-- /.social-auth-links -->

        <!--<a href="#">I forgot my password</a><br>-->
        <!--<a href="register.html" class="text-center">Register a new membership</a>-->

    </div>
    <!-- /.login-box-body -->
</div><!-- /.login-box -->
