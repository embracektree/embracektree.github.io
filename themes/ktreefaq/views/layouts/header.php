<?php
use yii\helpers\Html;
use app\vendor\KTComponents\Admin;
use yii\helpers\Url;


/* @var $this \yii\web\View */
/* @var $content string */
?>

<header class="main-header">


    <nav class="navbar navbar-static-top" role="navigation">

        <div class="navbar-custom-menu">

            <?php $languages = json_decode(Admin::getLanguagesList(), true);
            $adminSettings = Yii::$app->getSettings();
            ?>
            <ul class="nav navbar-nav">


                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                        <?php  $selectedLanguage =  Yii::$app->session->get('language');

                        $defualtLanguage = ($adminSettings['default_language']) ? $adminSettings['default_language'] : Admin::DEFAULT_LANGUAGE;
                        $language = ($selectedLanguage) ? $selectedLanguage : $defualtLanguage;

                        echo $languages[$language];
                        ?>
                        <span class="caret"></span></a>
                    <ul role="menu" class="dropdown-menu">
                        <?php foreach ($languages as $short_code => $language) { ?>
                            <li id="<?php echo $short_code; ?>"
                                class="language_selected"><?php echo Html::a($language, 'javascript::void(0)'); ?></li>
                        <?php } ?>


                    </ul>
                </li>
            </ul>
            <ul class="nav navbar-nav">

                <?php if (!Yii::$app->user->isGuest) { ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle"
                           href="#"><?php echo Yii::$app->user->identity->user_username; ?> <span class="caret"></span></a>
                        <ul role="menu" class="dropdown-menu">

                            <li><?php echo Html::a(Yii::t('app', 'Manage Topics'), ['/topics/index']); ?></li>
                            <li><?php echo Html::a(Yii::t('app', 'Manage Questions'), ['/questions/index']); ?></li>
                            <li><?php echo Html::a(Yii::t('app', 'Admin Settings'), ['/adminSettings/adminindex']); ?></li>
                            <li><?php echo Html::a(Yii::t('app', 'Flush Cache'), ['/topics/flush-cache'], ['class' => 'flush-cache']); ?></li>
                            <li class="dropdown user user-menu">

                                <?php $signOut = Html::beginForm(['/site/logout'], 'post');
                                $signOut .= Html::submitButton(
                                    Yii::t('app', 'Logout'),
                                    ['class' => 'btn btn-link glyphicon glyphicon-log-out']
                                );
                                $signOut .= Html::endForm();
                                echo $signOut;
                                ?>
                            </li>

                        </ul>
                    </li>
                <?php } else { ?>

                    <li class="dropdown user user-menu">
                        <a href="/site/login" data-toggle="control-login"><i class="glyphicon glyphicon-log-in"></i>Login</a>
                    </li>

                <?php } ?>
                <!-- User Account: style can be found in dropdown.less -->

            </ul>
        </div>
        <?php

        echo "<i class='fa fa-bars menu-responsive-icon' style='display:none'></i>";
        if ($adminSettings['logo'])
            echo Html::a(Html::img(Url::base(true) . '/upload/' . $adminSettings['logo'], ['alt' => $adminSettings['site_name']]), [Yii::$app->homeUrl], ['class' => 'logo', 'alt' => $adminSettings['site_name']]);
        if (Yii::$app->controller->id != 'site') {
            echo '<div class="knx-auto-search-header">';
            echo '<i class="fa fa-search search-btn"></i>';
            echo $this->render('autocomplete');
            echo '</div>';

        }

        ?>
    </nav>
</header>
