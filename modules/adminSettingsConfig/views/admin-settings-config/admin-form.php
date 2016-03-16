<?php
/*Form which renders form fields from the adminindex.php file to display fields in the form*/

use yii\helpers\Html;
use yii\web\View;
use app\modules\adminSettingsConfig\models\AdminSettingsConfig;
use kartik\widgets\FileInput;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;

?>

<div class="form">
    <?php
    //To get all values of SettingsCore model with group name and status active condition
    $adminSettings = AdminSettingsConfig::find()->where(['group' => $group, 'status' => AdminSettingsConfig::ACTIVE])->all();
    foreach ($adminSettings as $adminSettingsValue) {
        $type = $adminSettingsValue->type;
        $name = $adminSettingsValue->name;
        //converting the $adminSettingsValue->options into an array
        $dropDownList = json_decode($adminSettingsValue->options, true);
        switch ($type) {
            case 'text':
                echo $form->field($adminSettingsModel, Yii::t('app', $adminSettingsValue->name))->textInput(['maxlength' => true,]);
                break;
            case 'password':
                echo $form->field($adminSettingsModel, $adminSettingsValue->name)->PasswordInput(['maxlength' => true,]);
                break;
            case 'dropdown':
                echo $form->field($adminSettingsModel, $adminSettingsValue->name)->dropDownList($dropDownList, ['rows' => '6']);
                break;
            case 'textarea':
                echo $form->field($adminSettingsModel, $adminSettingsValue->name)->widget(CKEditor::className(), [
                    'options' => ['rows' => 6],
                    'preset' => 'basic' /*'custom,basic,full,standard'*/
                ]);
                //echo $form->field($adminSettingsModel, $adminSettingsValue->name)->textArea(['rows' => '6']);
                break;
            case 'multiselect':
                //converting the comma separated $adminSettingsModel->$name fields into an array
                $adminSettingsModel->$name = explode(",", $adminSettingsModel->$name);
                echo $form->field($adminSettingsModel, $adminSettingsValue->name)->dropDownList($dropDownList, ['multiple' => true]);
                break;
            case 'image':
                $initialPreview = '';
                //echo '<pre>';
                //print_r($adminSettingsModel);
                //echo $adminSettingsModel->$name;die;
                if (!empty($adminSettingsModel->$name)) {
                    $initialPreview = Html::img(Url::To('/upload') . '/' . $adminSettingsModel->$name, ['class' => 'file-preview-image']);
                }

                echo $form->field($adminSettingsModel, $adminSettingsValue->name)->widget(FileInput::classname(), [
                    'options' => ['accept' => 'image/*'],
                    'pluginOptions' => [
                        'showRemove' => true,
                        'removeClass' => 'btn btn-default file_remove',
                        'showUpload' => false,
                        'initialPreview' => $initialPreview,
                    ]
                ]);
                break;
            case 'checkbox':
                echo $form->field($adminSettingsModel, $adminSettingsValue->name)->checkBox();
                break;
            default:
                echo $form->field($adminSettingsModel, $adminSettingsValue->name)->$type(['maxlength' => true,]);
        }
    }

    ?>
</div>
