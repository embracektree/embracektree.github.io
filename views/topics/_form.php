<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\vendor\KTComponents\Admin;
use yii\helpers\Url;
use \app\models\Topics;
use pendalf89\filemanager\widgets\FileInput;
use dosamigos\ckeditor\CKEditor;


/* @var $this yii\web\View */
/* @var $model app\models\Topics */
/* @var $form yii\widgets\ActiveForm */
?>
<?php

$statusList = json_decode(Admin::getStatusList(), true);
$languageList = json_decode(Admin::getLanguagesList(), true);


?>
<div class="courses-form">
    <div class="box box-primary">
        <div class="box-body">
            <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

            <?= $form->field($model, 'topic_name')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'language')->dropDownList($languageList, []) ?>

            <?php echo  $form->field($model, 'topic_description')->widget(CKEditor::className(), [
                'options' => ['rows' => 6],
                'preset' => 'basic'
            ]);?>

            <?php echo  $form->field($model, 'topic_short_desc')->widget(CKEditor::className(), [
                'options' => ['rows' => 6],
                'preset' => 'basic'
            ]);?>

            <?php

            $initialPreview = '';
            if ($mediaModel->image_path) {
                $initialPreview = Html::img(Url::base(true) . '/upload' . Yii::t('app', Topics::TOPIC_SAVE_IMAGE) . $model->topic_id . '/' . $mediaModel->image_path, ['class' => 'file-preview-image']);

            }
            ?>
            <?php


            echo $form->field($mainModel, 'topic_image')->widget(FileInput::className(), [
                'buttonTag' => 'button',
                'buttonName' => Yii::t('app','Browse'),
                'buttonOptions' => ['class' => 'btn btn-default'],
                'options' => ['class' => 'form-control'],
                // Widget template
                'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
                // Optional, if set, only this image can be selected by user
                'thumb' => 'original',
                // Optional, if set, in container will be inserted selected image
                'imageContainer' => '.img',
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

            <?= Html::hiddenInput('topicImageHidden', $mediaModel->image_path, ['class' => 'hiddenImageTopic']) ?>

            <?= $form->field($model, 'topic_status')->dropDownList($statusList, ['prompt' => '---Please Select---']) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>
    </div>
</div>
