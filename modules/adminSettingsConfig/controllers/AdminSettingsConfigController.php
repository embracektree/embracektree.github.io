<?php

namespace app\modules\adminSettingsConfig\controllers;

use Yii;
use app\modules\adminSettingsConfig\models\AdminSettingsConfig;
use yii\web\Controller;
use app\modules\adminSettingsConfig\components\AdminGlobalFunctions;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use yii\web\Response;
use yii\widgets\ActiveForm;
use yii\helpers\BaseFileHelper;
use app\vendor\KTComponents\Admin;

/**
 * AdminSettingsConfigController implements the CRUD actions for AdminSettingsConfig model.
 */
class AdminSettingsConfigController extends Controller
{

    public function behaviors() //Authentication of the users
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'adminindex'],
                'rules' => [
                    [
                        'actions' => ['index', 'adminindex'],
                        'allow' => true,
                        'roles' => ['admin'], //will be accesseble only for admin role which we set while saving user in the auth table not based on the role which we saved in the user table
                    ],
                ],
            ],
        ];
    }

    /**
     * Returns the admin settings model and save the admin settings information.
     * @return array|string|Response
     */
    public function actionAdminindex()
    {
        $model = new AdminSettingsConfig();
        $flag = 0;
        //To get all active status fields from AdminSettingsConfig model
        $adminSettings = AdminSettingsConfig::find()->where(['status' => AdminSettingsConfig::ACTIVE])->all();
        //To get the AdminSettings model
        $adminSettingsModel = AdminGlobalFunctions::getAdminSettingsModel($adminSettings);

        if (Yii::$app->request->isAjax && $adminSettingsModel->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($adminSettingsModel);
        }
        //saving the group names into an array
        $groupName = self::returnGroupNames($adminSettings);

        if ($adminSettingsModel->load(Yii::$app->request->post())) {

            $postData = Yii::$app->request->post()['DynamicModel'];

            $flag = self::saveAdminSettingsInfo($adminSettings,$postData,$adminSettingsModel);

            $message = ($flag == 0) ? 'Admin Settings successfully updated' : 'Admin Settings failed to update';
            $status = ($flag == 0) ? 'success' : 'error';
            $key = Admin::ADMIN_SETTINGS_CACHE_KEY;
            $cache = Yii::$app->generalCache;
            $existResult = $cache->exists($key);
            ($existResult) ? $cache->delete($key) : '';

            Yii::$app->session->setFlash($status, Yii::t('app', $message));

            $return = ($flag == 0) ? $this->redirect(['adminindex']) : $this->render('adminindex', [
                'adminSettingsModel' => $adminSettingsModel, 'groupName' => $groupName, 'model' => $model,
            ]);

            return $return;
        }
        return $this->render('adminindex', [
            'adminSettingsModel' => $adminSettingsModel, 'groupName' => $groupName, 'model' => $model,
        ]);

    }

    /**
     * Builds group names into array and returns
     * @param $adminSettings
     * @return array
     */
    public static function returnGroupNames($adminSettings)
    {
        foreach ($adminSettings as $adminSettingsValue) {

            $groupName[] = (!in_array($adminSettingsValue->group, $groupName)) ? $adminSettingsValue->group : '';

        }
        return $groupName;
    }
    /**
     * Saving the admin settings information after posting data
     * @param $adminSettings
     * @param $postData
     * @param $adminSettingsModel
     * @return int
     */
    public static function saveAdminSettingsInfo($adminSettings,$postData,$adminSettingsModel)
    {
        foreach ($adminSettings as $adminSettingsValue) {

            $fieldType = $adminSettingsValue->type;

            switch ($fieldType) {

                case "image":

                    $returnValue =  self::saveImage($postData,$adminSettingsModel, $adminSettingsValue);
                    $adminSettingsValue->values = $returnValue;

                    break;

                case "multiselect":

                    $adminSettingsValue->values = implode(",", $postData[$adminSettingsValue->name]);
                    break;

                default:

                    $adminSettingsValue->values = $postData[$adminSettingsValue->name];
                    break;
            }

            $flag = ($adminSettingsValue->save(false)) ? 0 : $flag++;
        }

        return $flag;
    }
    /**
     * Function to save and update the images for admin settings
     * @param $postData
     * @param $adminSettingsModel
     * @param $adminSettingsValue
     * @return null|string
     */
    public static function saveImage($postData,$adminSettingsModel, $adminSettingsValue){

     if (UploadedFile::getInstance($adminSettingsModel, $adminSettingsValue->name)) {
             $imageValue = $adminSettingsValue->name;
             $imagePath = AdminGlobalFunctions::imageSaving($adminSettingsModel, $imageValue);
             $return = $imagePath;
      }elseif($postData['file_input'] == 1) {
            $location = Yii::$app->params['uploadPath'] . '/admin_settings_images';
            BaseFileHelper::removeDirectory($location);
            $return = NULL;
        } else {
            $return = $adminSettingsValue->values;
        }
        return $return;
    }
}
