<?php
use yii\helpers\Url;

$asset = \yii\easyii\assets\EmptyAsset::register($this);;

$this->title = Yii::t('easyii/install', 'Installation completed');
?>
<div class="container">
    <div id="wrapper" class="col-md-6 col-md-offset-3 vertical-align-parent">
        <div class="vertical-align-child">
            <div class="panel">
                <div class="panel-heading text-center">
                    <?= Yii::t('easyii/install', 'Installation completed') ?>
                </div>
                <div class="panel-body text-center">
                    <a href="<?= Url::to(['/']) ?>">Go to Home Url</a>
                </div>
            </div>
            <div class="text-center">
                <a class="logo" href="http://ktreefaq.ktree.org" target="_blank" title="KTreeFAQ homepage">
                    <?php
                    $adminSettings = Yii::$app->getSettings();
                    $imageUrl = Url::base(true) . '/upload/' . $adminSettings['logo'];

                    ?>
                    <img src="<?= $imageUrl ?>">Go to Home Url
                </a>
            </div>
        </div>
    </div>
</div>
