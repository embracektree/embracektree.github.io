<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use pendalf89\filemanager\widgets\FileInput;


/* @var $this yii\web\View */
/* @var $model app\models\Topics */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="courses-form">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'topic_quick_create_form',
            'options' => ['enctype' => 'multipart/form-data'],
            'action' => '/topics/create-topic',
            'enableClientValidation' => true,
            'enableAjaxValidation' => true,
            'validateOnSubmit' => true,
            'validateOnChange' => true,
        ]
    ); ?>

    <?= $form->field($topicInfoModel, 'topic_name')->textInput(['maxlength' => true]) ?>



    <?= $form->field($topicInfoModel, 'topic_short_desc')->textarea(['rows' => 6]) ?>

    <?php
    echo $form->field($model, 'topic_image')->widget(FileInput::className(), [
        'buttonTag' => 'button',
        'buttonName' => Yii::t('app', 'Browse'),
        'buttonOptions' => ['class' => 'btn btn-default'],
        'options' => ['class' => 'form-control'],
        // Widget template
        'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
        // Optional, if set, only this image can be selected by user
        'thumb' => 'original',
        // Optional, if set, in container will be inserted selected image
        'imageContainer' => '.img',
        //'allowedFileExtensions'=>['jpg'],
        'class' => 'image_class',
        //'showCloseButton'=>true,
        // Default to FileInput::DATA_URL. This data will be inserted in input field
        'pasteData' => FileInput::DATA_URL,
        // JavaScript function, which will be called before insert file data to input.
        // Argument data contains file data.
        // data example: [alt: "Ведьма с кошкой", description: "123", url: "/uploads/2014/12/vedma-100x100.jpeg", id: "45"]
        'callbackBeforeInsert' => 'function(e, data) {
        console.log( data );
    }',
    ]);
    ?>
    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
