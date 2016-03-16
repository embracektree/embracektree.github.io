<?php
namespace app\vendor\KTComponents;

use Yii;
use app\modules\adminSettingsConfig\components\AdminGlobalFunctions;
use app\vendor\KTComponents\Admin;

/**
 * Class KTApplication is extension of \yii\web\Application in nested module KTComponents
 * @package KT\components
 */
class KTApplication extends \yii\web\Application
{
    /**
     * Global variable to set the default language for whole application
     * @var string
     */
      public $language = 'EN';
      public $name = 'KTreeFAQ';

    /**
     * Constructor is to overdide the default components , url management in nested module extended application
     * @param array $config
     */
    public function __construct($config = [])
    {
        $config['components']['assetManager'] = [
            'bundles' => [
                'ktreefaq\theme\AdminLteAsset' => [

                ],
            ],
        ];
        $config['components']['urlManager'] = [
            'class' => 'yii\web\UrlManager',
            'enablePrettyUrl' => true,
            'showScriptName' => false,

            'rules' => [
                'user/<action:\w+>' => 'user/users/<action>',
                'adminSettings/<action:\w+>' => 'adminSettings/admin-settings-config/<action>',
                '<controller>' => '<controller>/index',
                'topics/<id:\d+>/<slug>' => 'topics/get-course-info',
                'topics/<topicslug>/<id:\d+>/<slug>' => 'topics/get-question-info',
                'questions/delete/<topic_id:\d+>/<language>' => 'questions/delete',
                'questions/update/<topic_id:\d+>/<language>' => 'questions/update',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ],

        ];
        parent::__construct($config);
    }

    /**
     * Init function is to set the theme and to set the default values priorly
     */
    public function init()
    {
        parent::init();

        Yii::setAlias('@bgtheme', $this->getBasePath() . '/themes');
        Yii::setAlias('@poweredby', Yii::t('app', 'Powered By') . ' <a href="http://ktree.com">KTreeFAQ</a> | ' . Yii::t('app', 'Version') . ' 1.0');
        if (Yii::$app->getModule('admin')->installed) {
            $this->_setTheme();
        }
    }

    /**
     * Function to set the theme for application by getting value from the admin settings
     */
    private function _setTheme()
    {

        $adminSettings = Yii::$app->getSettings();
        $theme = $adminSettings['theme'];

        $languageOption = Yii::$app->session->get('language');
        $this->language  = ($languageOption) ? $languageOption : $adminSettings['default_language'];
        $this->name  = $adminSettings['site_name'];
        if (is_dir(\Yii::getAlias('@app') . '/themes/' . $theme) && $theme) {
            Yii::setAlias('@theme', Yii::getAlias('@app') . '/themes/' . $theme);
            $this->view->theme = new \yii\base\Theme([
                'pathMap' => ['@app/views' => '@theme/views'],
                'baseUrl' => '@web/themes/' . $theme,

            ]);
        }

     }

    /**
     * Function to fetch the admin settings information to use through out the application
     */
    public function getSettings($group = null, $name = null)
    {
        $key = Admin::ADMIN_SETTINGS_CACHE_KEY;
        $cache = Yii::$app->generalCache;
        $settings = $cache->get($key);
        if (!$settings) {
            $settings = AdminGlobalFunctions::getAdminSettingsInfo($group, $name);
            $cache->set($key, $settings, Yii::$app->params['cache']);
        }
        return $settings;
    }
}
