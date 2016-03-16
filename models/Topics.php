<?php

namespace app\models;

use Yii;
use app\vendor\KTComponents\Admin;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\SluggableBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;


/**
 * This is the model class for table "faq_topics".
 *
 * @property integer $topic_id
 * @property string $topic_name
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 *
 * @property TopicsInfo $coursesInfo
 * @property Topics[] $topics
 */
class Topics extends \app\vendor\KTComponents\KTActiveRecord
{
    const TOPIC_SAVE_IMAGE = '/topic_images/';
    const TOPIC_MEDIA_NAME = 'Topic Images';

    /**
     * Returns the table name for the Topics model
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%topics}}';
    }

    /**
     *Attaching multiple behaviours to the model class which fills the specified attributes specified value
     * @return array
     */

    public function behaviors()
    {
        $immutable = (Yii::$app->language == Admin::DEFAULT_LANGUAGE) ? false : true;
        return [
            'sluggable' => [
                'class' => SluggableBehavior::className(),
                'attribute' => 'topic_name',
                'immutable' => $immutable,
                'slugAttribute' => 'slug'
            ],
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
     * Returns the validation rules to the attributes
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['topic_name'], 'required'],
            [['created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['topic_name'], 'string', 'max' => 255],
            [['topic_name'], 'unique',],

        ];
    }

    /**
     * Returns the attribute labels
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'topic_id' => Yii::t('app', 'Topic ID'),
            'topic_name' => Yii::t('app', 'Topic Name'),
            'topic_image' => Yii::t('app', 'Topic Image'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_by' => Yii::t('app', 'Modified By'),
            'modified_date' => Yii::t('app', 'Modified Date'),
        ];
    }

    /**
     * Relation returns the related TopicsInfo of Topic model
     * @return \yii\db\ActiveQuery
     */
    public function getTopicsInfo()
    {
        $infoTableName = TopicsInfo::tableName();
        $languageOption = Yii::$app->language;
        $languageOption = ($languageOption) ? $languageOption : Admin::DEFAULT_LANGUAGE;
        $languageOrder = [$languageOption, Admin::DEFAULT_LANGUAGE];

        $status = (Yii::$app->user->id && Yii::$app->user->id == Admin::ADMIN_USER_ID) ? Admin::RECORD_DELETE : Admin::ACTIVE;
        $whereCondition = (Yii::$app->user->id && Yii::$app->user->id == Admin::ADMIN_USER_ID) ? '!=' : '=';
        $newObject = new \yii\db\Expression('FIND_IN_SET(' . $infoTableName . '.language, "' . implode(',', $languageOrder) . '")');
        $return = $this->hasMany(TopicsInfo::className(), ['topic_id' => 'topic_id'])
            ->andOnCondition([$infoTableName . '.language' => $languageOrder])
            ->andWhere([$whereCondition, '' . $infoTableName . '.topic_status', $status])
            ->orderBy([$newObject]);

        return $return;
    }

    /**
     * Relation returns the related Question model information of Topic model
     * @return \yii\db\ActiveQuery
     */
    public function getQuestion()
    {
        return $this->hasMany(Questions::className(), ['topic_id' => 'topic_id']);
    }

    /**
     * Relation returns the related Media model of Topic model
     * @return $this
     */
    public function getMediaData()
    {
        $return = $this->hasOne(Media::className(), ['media_id' => 'topic_id'])->where(['status' => Admin::ACTIVE, 'media_name' => Yii::t('app', self::TOPIC_MEDIA_NAME)]);
        return $return;
    }

    /**
     * Create's new Topics model when the topic not exists in the selected language
     * @param null $topicModel
     * @param null $info
     * @return mixed
     */
    public static function createLanguageBasedTopics($topicModel = null, $info = null)
    {
        try {
            $sampleInfo = $info[Admin::DEFAULT_LANGUAGE];
            if (!$sampleInfo) {
                $sampleInfo = $info[current(array_keys($info))];
                if (!$sampleInfo) {
                    $sampleInfo = TopicsInfo::find()->where(['!=', 'topic_status', Admin::RECORD_DELETE])->andWhere(['topic_id' => $topicModel->topic_id])->one();
                    $sampleInfo = $sampleInfo->attributes;
                }
            }
            if ($sampleInfo) {
                $topicInfoModel = new TopicsInfo();
                $topicInfoModel->attributes = $sampleInfo;
                $topicInfoModel->topic_id = $sampleInfo['topic_id'];
                $topicInfoModel->language = Yii::$app->language;
                $topicInfoModel->topic_status = Admin::DRAFT_VALUE;
                if ($topicInfoModel->save()) {

                    $language = Yii::$app->language;
                    $key = Yii::t('app', Admin::TOPICS_CACHE_KEY) . '_' . $language;
                    Admin::clearCache($key);

                    $key = Yii::t('app', Admin::QUESTIONS_CACHE_KEY) . '_' . $language . '_' . $topicModel->topic_id;
                    Admin::clearCache($key);

                    $key = Yii::t('app', Admin::TOP_QUESTIONS) . '_' . $language . '_' . $topicModel->topic_id;
                    Admin::clearCache($key);

                    $return['status'] = true;

                } else {
                    $return['status'] = false;
                    $return['message'] = $topicInfoModel->getErrors();
                }
            }

        } catch (Exception $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();
        }
        return $return;
    }

    /**
     * Returns all the available languages for the Topics model
     * @param $topicId
     * @return string
     */
    public static function getTopicsLanguagesInfo($topicId)
    {
        try {
            $allModels = TopicsInfo::find()
                ->where(['topic_id' => $topicId])
                ->andWhere(['!=', 'topic_status', Admin::RECORD_DELETE])
                ->all();
            $listOfLanguages = ArrayHelper::getColumn($allModels, function ($element) {
                return $element['language'];
            });

            $languageList = json_decode(Admin::getLanguagesList(), true);
            foreach ($listOfLanguages as $value) {
                $languagesArray[] = $languageList[$value];
            }
            $retrun = implode(',', $languagesArray);
        } catch (Exception $e) {

            $retrun = $e->getMessage();
        }

        return $retrun;
    }
}
