<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\db\Expression;
use app\vendor\KTComponents\Admin;


/**
 * This is the model class for table "faq_questions_info".
 *
 * @property integer $question_id
 * @property string $question_name
 * @property string $question_description
 * @property string $language
 * @property integer $question_status
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 *
 * @property Questions $topic
 */
class QuestionsInfo extends \app\vendor\KTComponents\KTActiveRecord
{
    /**
     * Returns the table name of the QuestionsInfo model
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%questions_info}}';
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
     * Validation rules for the attributes
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_id', 'question_name',  'question_status',], 'required'],
            [['question_description'], 'required', 'except' => 'quick_create'],
            [['question_id', 'sort_order', 'question_status', 'created_by', 'modified_by'], 'integer'],
            [['question_description'], 'string'],
            [['created_date', 'modified_date'], 'safe'],
            [['question_name',], 'string', 'max' => 255],
            [['language'], 'string', 'max' => 55]
        ];
    }

    /**
     * Returns the attribute labels
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => Yii::t('app', 'Question ID'),
            'question_name' => Yii::t('app', 'Question Name'),
            'sort_order' => Yii::t('app', 'Sort Order'),
            'question_description' => Yii::t('app', 'Question Description'),
            'language' => Yii::t('app', 'Language'),
            'question_status' => Yii::t('app', 'Question Status'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_by' => Yii::t('app', 'Modified By'),
            'modified_date' => Yii::t('app', 'Modified Date'),
        ];
    }

    /**
     * Returns the Questions model related to  QuestionsInfo model
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        $return = $this->hasOne(Questions::className(), ['question_id' => 'question_id']);
        return $return;
    }

    /**
     * Returns the primarykey for the QuestionsInfo model
    */

    public static function primaryKey()
    {
        return ['question_id', 'language'];
     }
}
