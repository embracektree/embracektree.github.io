<?php
use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use app\modules\adminSettingsConfig\models\AdminSettingsConfig;
//use yii\bootstrap\Tabs;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Tabs;

?>
<?php
$form = ActiveForm::begin([
    'enableAjaxValidation' => true,
    'id' => 'admin-settings-form',
    'options' => ['enctype' => 'multipart/form-data']
]);
?>
<div class="header_title">
    <h1><?php echo Yii::t('app', 'Update Admin Settings'); ?></h1>
</div>
<?= $form->errorSummary($adminSettingsModel); ?>
<div class="categories-form box box-primary">
    <div class="box-body">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => 'btn btn-primary pull-right']) ?>
        <input type="hidden" name="DynamicModel[file_input]" id="dynamicmodel-file_input">
        <?php

        foreach ($groupName as $groupNameValue) {
            $items[] = [
                'label' => Yii::t('app', $groupNameValue),
                'content' => $this->render('admin-form', ['adminSettingsModel' => $adminSettingsModel, 'form' => $form, 'group' => $groupNameValue], true, false), //rendering the admin-form view

            ];
        }
        echo Tabs::widget([
            'items' => $items
        ]);
        ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
