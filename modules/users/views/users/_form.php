<?php
/* Version 100 User create and update form KT173 */
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\modules\adminSettings\models\Users */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$statusList = ['0' => 'Inactive', '1' => 'Active'];
$rolesList = ['admin' => 'Admin', 'front_end' => 'Front End']

?>
<div class="users-form">
    <div class="box box-primary">
        <div class="box-body">
            <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'user_username')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'user_firstname')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'user_lastname')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'user_password')->passwordInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'user_email')->textInput(['maxlength' => true]) ?>

            <?= $form->field($model, 'roles')->dropDownList($rolesList, []) ?>

            <?= $form->field($model, 'user_status')->dropDownList($statusList, []) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
