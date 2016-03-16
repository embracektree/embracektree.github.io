<?php

namespace app\modules\users\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\helpers\Security;
use yii\base\NotSupportedException;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "faq_users".
 *
 * @property integer $user_id
 * @property string $user_username
 * @property string $user_firstname
 * @property string $user_lastname
 * @property string $user_password
 * @property string $user_email
 * @property string $roles
 * @property string $groups
 * @property string $user_type
 * @property integer $user_status
 * @property integer $user_created_by
 * @property string $user_created_date
 * @property integer $user_modified_by
 * @property string $user_modified_date
 */
class Users extends ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    const ACTIVE = 1;
    const STATUS_DELETED = 0;
    const STATUS_ACTIVE = 1;

    /**
     * Returns the table for the Users model
     * @return string
     */
    public static function tableName()
    {
        return '{{%users}}';
    }

    /**
     *Attaching multiple behaviours to the model class which fills the specified attributes specified value
     * @return array
     */

    public function behaviors()
    {
        return [
            'sluggable' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'user_username',
                'slugAttribute' => 'slug'
            ],
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'user_created_date',
                'updatedAtAttribute' => 'user_modified_date',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_created_by',
                'updatedByAttribute' => 'user_modified_by',
            ],

        ];
    }

    /**
     * Returns the valdation rules
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_username', 'user_firstname', 'user_lastname', 'user_email', 'roles',], 'required'],
            ['user_password', 'required', 'except' => ['update']],
            [['roles', 'groups'], 'string'],
            [['user_email'], 'email'],
            [['user_username'], 'unique'],
            [['user_status', 'user_created_by', 'user_modified_by'], 'integer'],
            [['user_created_date', 'user_modified_date'], 'safe'],
            [['user_username', 'user_firstname', 'user_lastname', 'user_password', 'user_email', 'user_type'], 'string', 'max' => 255]
        ];
    }

    /**
     * Returns the attribute labels
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app','User ID'),
            'user_username' => Yii::t('app','User Name'),
            'user_firstname' => Yii::t('app','First Name'),
            'user_lastname' => Yii::t('app','Last Name'),
            'user_password' => Yii::t('app','Password'),
            'user_email' => Yii::t('app','Email'),
            'roles' => Yii::t('app','Roles'),
            'groups' => Yii::t('app','Groups'),
            'user_type' => Yii::t('app','User Type'),
            'password_hash' => Yii::t('app','Password Hash'),
            'password_reset_token' => Yii::t('app','Password Reset Token'),
            'auth_key' => Yii::t('app','Auth key'),
            'created_at' => Yii::t('app','Created at'),
            'updated_at' => Yii::t('app','Updated at'),
            'user_status' => Yii::t('app','Status'),
            'user_created_by' => Yii::t('app','Created By'),
            'user_created_date' => Yii::t('app','Created Date'),
            'user_modified_by' => Yii::t('app','Modified By'),
            'user_modified_date' => Yii::t('app','Modified Date'),
        ];
    }

    /**
     * Returns the Users model based on user id
     * @param int|string $id
     * @return null|IdentityInterface|static
     */
    public static function findIdentity($id)
    {
        return static::findOne(['user_id' => $id, 'user_status' => self::ACTIVE]);
    }

    /**
     * Returns the Users model based on the validating access token
     * @param mixed $token
     * @param null $type
     * @return null|IdentityInterface|static
     */

    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (self::$users as $user) {
            if ($user['accessToken'] === $token && $user['type'] === $type) {
                return new static($user);
            }
        }

        return null;
    }

    /**
     * Returns the users model based on the user name
     * @param $username
     * @return null|static
     */

    public static function findByUsername($username)
    {
        $userDetails = self::findOne(['user_username' => $username, 'user_status' => self::ACTIVE]);
        return $userDetails;
    }

    /**
     * Finds users  model by password reset token
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }


    /**Return the status of the password reset token
     * @param $token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * Returns the user id of user model
     * @return int|string
     */
    public function getId()
    {
        return $this->user_id;
    }

    /**
     * Returns the auth key of user model
     * @return mixed|string
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Returns the validation reponse of auth key
     * @param string $authKey
     * @return bool
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * if password provided is valid for current user
     * @param  string $password password to validate
     * @return boolean
     */
    public function validatePassword($password)
    {

        return Yii::$app->security->validatePassword($password, $this->password_hash);

    }

    /**
     * Generates password hash from password and sets it to the model
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    /*Function which returns status of the users*/
    public static function getUserStatus()
    {
        $statusList = ['0' => 'Inactive', '1' => 'Active'];
        return $statusList;

    }
}
