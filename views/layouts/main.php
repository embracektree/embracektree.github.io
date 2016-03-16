<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php echo Yii::$app->name;
 echo Url::to();
    NavBar::begin([
        'brandLabel' => Yii::$app->name,
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-righ'],
        'items' => [
          //  ['label' => 'Home', 'url' => ['/site/index']],
           // ['label' => 'About', 'url' => ['/site/about']],
            //['label' => 'Contact', 'url' => ['/site/contact']],
            Yii::$app->user->isGuest ?
                ['label' => 'Login', 'url' => ['/site/login'],
		'options'=>['class'=>'login-classes']] :
                [
                    'label' => 'Logout (' . Yii::$app->user->identity->user_username . ')',
                    'url' => ['/site/logout'],
		    'visible'=>true,	
                    'linkOptions' => ['data-method' => 'post']
                ],
		['label' => 'Users', 'url' => ['/user/users/index'],
		'options'=>['class'=>'users-class'],
		'visible'=> !Yii::$app->user->isGuest ? true : false ,//Kt173 Visible only for admin user role only
		],
                ['label' => 'Admin Settings', 'url' => ['/site/index'],
		'options'=>['class'=>'admin-settings-class'],
		'visible'=> !Yii::$app->user->isGuest ? ((Yii::$app->user->identity->user_roles == 'admin') ? true : false) : false ,//Kt173 Visible only for admin user role only
		],
               
        ],
    ]);
    NavBar::end();
    ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?= $content ?>
    </div>
</div>

<footer class="footer">
    <div class="container">
       
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
  <script type="text/javascript">//Url to use globally
		var ajaxUrl = "<?php echo Url::base(true).'/index.php/'; ?>"; //alert(ajaxUrl);	
		var current_user = "<?php echo Yii::$app->user->id; ?>"; //alert(current_user);	
		var loginUrl = "<?php echo Url::to(['site/login']);; ?>";//alert(loginUrl);	
		//var loaderImageUrl = "<?php echo Url::base(true)."/themes/images/loader.gif"; ?>"; 	
	</script>
