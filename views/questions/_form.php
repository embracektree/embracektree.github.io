<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\vendor\KTComponents\Admin;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $model app\models\Questions */
/* @var $form yii\widgets\ActiveForm */
?>
<?php

$statusList = json_decode(Admin::getStatusList(), true);
$languageList = json_decode(Admin::getLanguagesList(), true);
$coursesList = json_decode(Admin::getCourses(), true);
$questionsList = json_decode(Admin::getQuestionsListInfo(), true);
$questionsList = ArrayHelper::map($questionsList, 'question_id', 'question_name');
?>
<div class="topics-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'question_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'question_description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'language')->dropDownList($languageList, ['prompt' => '---Please Select---']) ?>

    <?= $form->field($model, 'parent_question_id')->dropDownList($questionsList, ['prompt' => '---Please Select---']) ?>

    <?= $form->field($model, 'topic_id')->dropDownList($coursesList, ['prompt' => '---Please Select---']) ?>

    <?= $form->field($model, 'question_status')->dropDownList($statusList, ['prompt' => '---Please Select']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
