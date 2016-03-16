<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php $form = ActiveForm::begin(['action' => Url::to('/admin/install')]); ?>
<?= $form->field($model, 'host', ['inputOptions' => ['title' => "Host Name"]]) ?>
<?= $form->field($model, 'db_username', ['inputOptions' => ['title' => "Database Username"]]) ?>
<?= $form->field($model, 'db_password', ['inputOptions' => ['title' =>"Database Password"]]) ?>
<?= $form->field($model, 'db_name', ['inputOptions' => ['title' => "Database Name"]]) ?>
<?= $form->field($model, 'tbl_prefix') ?>
<?= $form->field($model, 'user_name', ['inputOptions' => ['title' => "User Name"]]) ?>
<?= $form->field($model, 'user_password', ['inputOptions' => ['title' => "User Password"]])->passwordInput(); ?>
<?= $form->field($model, 'user_email', ['inputOptions' => ['title' => "User Email"]]) ?>
<?= Html::submitButton('Install', ['class' => 'btn btn-lg btn-primary btn-block']) ?>
<?php ActiveForm::end(); ?>
