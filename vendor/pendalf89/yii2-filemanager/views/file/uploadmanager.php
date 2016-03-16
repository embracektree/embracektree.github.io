<?php
use dosamigos\fileupload\FileUploadUI;
use pendalf89\filemanager\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel pendalf89\filemanager\models\Mediafile */

?>

<header id="header"><span class="glyphicon glyphicon-upload"></span> <?= Module::t('main', 'Upload manager') ?></header>

<div id="uploadmanager">
    <p><?= Html::a('← ' . Module::t('main', 'Back to file manager'), ['file/filemanager']) ?></p>
    <?= FileUploadUI::widget([
        'model' => $model,
        'attribute' => 'file',
        'fieldOptions' => [
            'accept' => 'image/*',
        ],
        'clientOptions' => [
            'acceptFileTypes'=>new yii\web\JsExpression('/(\.|\/)(gif|jpe?g|png)$/i'),
            'autoUpload'=> Yii::$app->getModule('filemanager')->autoUpload,

        ],
        'url' => ['upload'],
        'gallery' => false,
    ]) ?>
</div>