<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use app\vendor\KTComponents\Admin;

/**
 * This is the model class for table "faq_topics_info".
 *
 * @property integer $topic_id
 * @property string $topic_name
 * @property string $slug
 * @property string $language
 * @property string $topic_description
 * @property string $topic_short_desc
 * @property string $topic_image
 * @property integer $topic_status
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 *
 * @property Topics $topic
 */
class TopicsInfo extends \app\vendor\KTComponents\KTActiveRecord
{
    /**
     * Returns the table name of TopicsInfo model
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%topics_info}}';
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
     * Returns the validation rules
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['topic_name', 'topic_description', 'topic_short_desc'], 'required'],
            [['topic_status', 'created_by', 'modified_by'], 'integer'],
            [['topic_description', 'topic_short_desc'], 'string'],
            [['created_date', 'modified_date'], 'safe'],

            [['topic_name', 'language'], 'unique', 'targetAttribute' => ['topic_name', 'language'], 'message' => 'The combination of Topic Name and Language has already been taken.',
                'filter' => function ($query) {
                        $query->andWhere(['!=', 'topic_status', 2]);
                    }],

            [['language'], 'unique', 'targetAttribute' => ['language', 'topic_id'], 'message' => 'The combination of Topic and Language has already been taken.',
                'filter' => function ($query) {
                        $query->andWhere(['!=', 'topic_status', 2]);
                    }],
            [['topic_name',], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 55]
        ];
    }

    /**
     * Returns the attributes labels
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'topic_id' => Yii::t('app', 'Topic ID'),
            'topic_name' => Yii::t('app', 'Topic Name'),
            'language' => Yii::t('app', 'Language'),
            'topic_description' => Yii::t('app', 'Topic Description'),
            'topic_short_desc' => Yii::t('app', 'Topic Short Desc'),
            'topic_status' => Yii::t('app', 'Topic Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_by' => Yii::t('app', 'Modified By'),
            'modified_date' => Yii::t('app', 'Modified Date'),
        ];
    }

    /**
     * Relation returns the related Topics model
     * @return \yii\db\ActiveQuery
     */
    public function getTopic()
    {
        return $this->hasOne(Topics::className(), ['topic_id' => 'topic_id']);
    }

    /**
     * Returns the primary key of TopicsInfo model
     */
    public static function primaryKey()
    {
        return ['topic_id', 'language'];

    }
}
