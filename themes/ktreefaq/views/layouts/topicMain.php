<?php
//require(__DIR__ . '/../../ThemeAssets.php');
/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\themes\ktreefaq\ThemeAssets;
use yii\helpers\Url;
use app\modules\adminSettingsConfig\components\AdminGlobalFunctions;


ThemeAssets::register($this);
$adminSettings = Yii::$app->getSettings();
$this->title = ($this->title) ? $this->title : Yii::t('app',$adminSettings['site_name']);
if (Yii::$app->controller->action->id === 'login') {
    /**
     * Do not use this code in your template. Remove it.
     * Instead, use the code  $this->layout = '//main-login'; in your controller.
     */
    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    if (class_exists('backend\assets\AppAsset')) {
        backend\assets\AppAsset::register($this);
    } else {
        app\assets\AppAsset::register($this);
    }


    $directoryAsset = Yii::$app->assetManager->getPublishedUrl('@bgtheme/weadminlte');
    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <body class="hold-transition skin-blue layout-top-nav">
    <?php $this->beginBody() ?>
    <div class="wrapper">
        <?=
        $this->render(
            'header.php',
            ['directoryAsset' => $directoryAsset]
        ) ?>
        <?=
        $this->blocks['topiclist'] ?>
        <?=
        $this->render(
            'coursecontent.php',
            ['content' => $content, 'directoryAsset' => $directoryAsset]
        )
        ?>
        <footer class="main-footer">
            <div class="pull-right hidden-xs">
                <?= Yii::getAlias('@poweredby'); ?>
            </div>
            <?= Yii::t('app', $adminSettings['footer_content']) ?>
        </footer>
    </div>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>
<script type="text/javascript">//Url to use globally
    var baseUrl = "<?php echo Url::base(true); ?>";//alert(baseUrl);
    var ajaxUrl = "<?php echo Url::base(true).'/index.php/'; ?>"; //alert(ajaxUrl);
    var current_user = "<?php echo Yii::$app->user->id; ?>"; //alert(current_user);
    var loginUrl = "<?php echo Url::to(['site/login']); ?>";//alert(loginUrl);
    var language = "<?php echo Yii::$app->language; ?>";
    //var loaderImageUrl = "<?php echo Url::base(true)."/themes/images/loader.gif"; ?>";
</script>
