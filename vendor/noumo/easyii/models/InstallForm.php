<?php
namespace yii\easyii\models;

use Yii;
use yii\base\Model;

class InstallForm extends Model
{
    public $host;
    public $db_username;
    public $db_password;
    public $db_name;
    public $tbl_prefix;
    public $user_name;
    public $user_password;
    public $user_email;


    public function rules()
    {
        return [
            [['host', 'db_username', 'db_name', 'tbl_prefix','user_name', 'user_password', 'user_email'], 'required'],
            [['host', 'db_username', 'db_password', 'db_name', 'tbl_prefix'], 'trim'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'host' => "Host Name",
            'db_username' => "Database Username",
            'db_password' => "Database Password",
            'db_name' => "Database Name",
            'tbl_prefix' => "Table Prefix",
            'user_name' => Yii::t('app', "User Name"),
            'user_password' => Yii::t('app', "User Password"),
            'user_email' => Yii::t('app', "User Email"),
        ];
    }
}
