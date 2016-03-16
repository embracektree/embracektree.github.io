<?php

namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
/**
 * This is the model class for table "faq_media".
 *
 * @property integer $id
 * @property string $media_name
 * @property integer $media_id
 * @property integer $image_path
 * @property integer $status
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 */
class Media extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%media}}';
    }
    /**
     *Attaching multiple behaviours to the model class which fills the specified attributes specified value
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'created_date',
                'updatedAtAttribute' => 'modified_date',
                'value' => new Expression('NOW()'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'created_by',
                'updatedByAttribute' => 'modified_by',
            ],
        ];
    }

    /**
     * Validation Rules to the attributes
     * @return array
     */
    public function rules()
    {
        return [
            [['media_name', 'media_id', 'image_path', 'status'], 'required'],
            [['media_id', 'status', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['media_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * Returns attribute Labels
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'media_name' => Yii::t('app','Media Name'),
            'media_id' => Yii::t('app','Category ID'),
            'image_path' => Yii::t('app','Image Path'),
            'status' => Yii::t('app','Status'),
            'created_by' => Yii::t('app','Created By'),
            'created_date' => Yii::t('app','Created Date'),
            'modified_by' => Yii::t('app','Modified By'),
            'modified_date' => Yii::t('app','Modified Date'),
        ];
    }
}
