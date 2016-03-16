<?php
namespace app\themes\ktreefaq;
use yii;
use yii\base\Exception;
use yii\web\AssetBundle;

/**
 * AdminLte AssetBundle
 * @since 0.1
 */


class ThemeAssets extends AssetBundle
{
    public $sourcePath = '@bgtheme/ktreefaq';
    public $css = [
        'css/site.css',
        'css/AdminLTE.css',
        'css/styles.css',
        'css/skins/_all-skins.css',
        'fontawesome/css/font-awesome.min.css',
        'colorpicker/bootstrap-colorpicker.css',
    ];
    public $js = [
        'js/globalScript.js',
        'js/core_js/jquery-ui.js',
        'colorpicker/bootstrap-colorpicker.min.js',
        'js/app.js',

    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset'

    ];
    public function init(){
        parent::init();
        if((!Yii::$app->user->isGuest)){

            $this->js[]= 'js/ckeditor/ckeditor.js';
            $this->js[]= 'js/ckeditor/adapters/jquery.js';
            $this->js[]= 'js/advanceEditor.js';
        }

        //$this->publishOptions['forceCopy'] = true;
    }
}