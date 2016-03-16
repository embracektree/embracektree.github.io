<?php
/*User module KT173*/
namespace app\modules\users;

use Yii;

class UsersModule extends \yii\base\Module
{
    public $controllerNamespace = 'app\modules\users\controllers';
    public $defaultRoute = 'users';//setting default controller


    /**
     * Returns the admin validation role of the user
     */
   public static function requireAdmin() {
	$status = (Yii::$app->user->identity->roles == 'admin') ? true : false;
    return $status
    }
}
