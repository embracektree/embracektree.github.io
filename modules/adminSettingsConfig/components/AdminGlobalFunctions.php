<?php

namespace app\modules\adminSettingsConfig\components;

use yii;
use yii\base\Component;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\web\UploadedFile;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use app\modules\adminSettingsConfig\models\AdminSettingsConfig;
use yii\helpers\BaseFileHelper;

/**
 * Class AdminGlobalFunctions is used for global functions of admin settings
 * @package app\modules\adminSettingsConfig\components
 */
class AdminGlobalFunctions extends Component
{

    /**
     * Retuns the active records for the admin settings
     * @return array|yii\db\ActiveRecord[]
     */
    public static function getAdminSettings()
    {
        return AdminSettingsConfig::find()->where(['status' => AdminSettingsConfig::ACTIVE])->asArray()->all();
    }

    /**
     * Returns admin settings information in key value pairs
     * @param null $group
     * @param null $name
     * @return array
     */
    public static function getAdminSettingsInfo($group = null, $name = null)
    {
        $return = self::getAdminSettings();
        if ($group != null) {
            $return = array_filter($return, function ($k) use ($group) {
                return $k['group'] == $group;
            });
        }
        if ($name != null) {
            $return = array_filter($return, function ($k) use ($name) {
                return $k['name'] == $name;
            });
        }

        return ArrayHelper::map($return, 'name', 'values');
    }

    /**
     * Returns the validation for the admin setting fields
     * @param null $adminSettings
     * @return yii\base\DynamicModel
     */
    public function getAdminSettingsModel($adminSettings = null)
    {
        foreach ($adminSettings as $adminSettingsValue) {
            //converting the validations into an array
            $validations = explode(',', $adminSettingsValue['validations']);

            $validationResponse =  self::addValidation($validations,$adminSettingsValue);

            $requiredFields[] = $validationResponse['requiredFields']; //saving required name fields into an array
            $integerFields[] = $validationResponse['integerFields']; //saving integer name fields into an array
            $emailFields[] = $validationResponse['emailFields']; //saving email name fields into an array
            $file[] =$validationResponse['file'];

            $labels[$adminSettingsValue->name] = Yii::t('app', $adminSettingsValue->label);

            $allFields[$adminSettingsValue->name] = (empty($adminSettingsValue->values)) ? $adminSettingsValue->default_values : $adminSettingsValue->values;

        }
        $requiredFields = empty($requiredFields) ? array() : $requiredFields;
        $integerFields = empty($integerFields) ? array() : $integerFields;
        $emailFields = empty($emailFields) ? array() : $emailFields;
        //To create the adminsettings model with the help of DynamicModel
        $adminSettingsModel = new \yii\base\DynamicModel($allFields, $labels);
        $adminSettingsModel->addRule($requiredFields, 'required');
        $adminSettingsModel->addRule($integerFields, 'integer');
        $adminSettingsModel->addRule($emailFields, 'email');
        $adminSettingsModel->addRule($file, 'file', ['extensions' => 'jpg, gif, png']);
        return $adminSettingsModel;
    }

    /**
     * Adding validations to the attributes
     * @param $validations
     * @param $adminSettingsValue
     * @return mixed
     */
    public static function addValidation($validations,$adminSettingsValue)
    {
        $requiredFields = (in_array('required', $validations)) ? $adminSettingsValue->name : []; //saving required name fields into an array
        $integerFields = (in_array('integer', $validations)) ? $adminSettingsValue->name : []; //saving integer name fields into an array
        $emailFields = (in_array('email', $validations)) ? $adminSettingsValue->name : []; //saving email name fields into an array
        $file = ($adminSettingsValue->type == 'image') ? $adminSettingsValue->name : [];

        $return['requiredFields'] = $requiredFields;
        $return['integerFields'] = $integerFields;
        $return['emailFields'] = $emailFields;
        $return['file'] = $file;

        return $return;
    }

    /**
     * Return the image path after moving image to folder
     * @param $adminSettingsModel
     * @param $imageValue
     * @return string
     */
    public function imageSaving($adminSettingsModel, $imageValue)
    {
        $image = UploadedFile::getInstance($adminSettingsModel, $imageValue);

        $location = Yii::$app->params['uploadPath'] . '/admin_settings_images';
        BaseFileHelper::removeDirectory($location);
        $fileName = $image->name;
        //checking the existence of admin_settings_images directory if directory does not exit then creating the directory
        if ($fileName) {
            BaseFileHelper::createDirectory($location);
            $imagePath = 'admin_settings_images/' . $fileName;
            //saving the image in admin_settings_images folder
            $image->saveAs($location . '/' . $fileName);
        }
        return $imagePath;
    }


}
