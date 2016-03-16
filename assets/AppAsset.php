<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        //'css/site.css',
	    //'js/fancyapps-fancyBox/source/jquery.fancybox.css',
    ];
    public $js = [
		//'js/globalScript.js',
		//'js/core_js/jquery-ui.js',
		//'js/core_js/jquery.session.js',
		//'js/fancyapps-fancyBox/source/jquery.fancybox.js',

	 ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
	
    ];
}
