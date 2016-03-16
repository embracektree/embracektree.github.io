<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\SluggableBehavior;
use yii\behaviors\BlameableBehavior;

use app\vendor\KTComponents\Admin;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;

/**
 * This is the model class for table "faq_questions".
 *
 * @property integer $question_id
 * @property string $question_name
 * @property string $slug
 * @property string $question_description
 * @property string $language
 * @property integer $topic_id
 * @property integer $question_status
 * @property integer $created_by
 * @property string $created_date
 * @property integer $modified_by
 * @property string $modified_date
 *
 * @property Topics $course
 */
class Questions extends \app\vendor\KTComponents\KTActiveRecord
{
    /**
     * @inheritdoc
     */

    const QUESTION_STATIC_CONTENT = 'Sample Content';
    const QUESTION_STATIC_NAME = 'Question Name';
    const DRAGGED_BEFORE = 'before';
    const DRAGGED_OVER = 'over';
    const DRAGGED_AFTER = 'after';
    const SIBLING = 'sibling';
    const CHILD = 'child';
    const TEMP_FOLDER = 'image_temp';

    /**
     * Returns the table name
     * @return string
     */
    public static function tableName()
    {
        return '{{%questions}}';
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
                'attribute' => 'question_name',
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
     * Validation rules for the attributes
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['question_name', 'topic_id'], 'required'],
            [['topic_id', 'created_by', 'modified_by'], 'integer'],
            [['created_date', 'modified_date'], 'safe'],
            [['question_name'], 'string', 'max' => 255],

        ];
    }

    /**
     * Retuns the attribute labels
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'question_id' => Yii::t('app', 'Question ID'),
            'question_name' => Yii::t('app', 'Question Name'),
            'parent_question_id' => Yii::t('app', 'Parent Question Name'),
            'topic_id' => Yii::t('app', 'Topic ID'),
            'created_by' => Yii::t('app', 'Created By'),
            'created_date' => Yii::t('app', 'Created Date'),
            'modified_by' => Yii::t('app', 'Modified By'),
            'modified_date' => Yii::t('app', 'Modified Date'),
        ];
    }

    /**
     * Returns the Related Topic model of the Questions model
     * @return \yii\db\ActiveQuery
     */
    public function getTopic()
    {

        return $this->hasOne(Topics::className(), ['topic_id' => 'topic_id']);

    }

    /**
     *Relation which returns the topic info model relatde to the Question model based on the language selected
     * @return $this
     */
    public function getTopicInfo()
    {
        $status = (Yii::$app->user->id && Yii::$app->user->id == Admin::ADMIN_USER_ID) ? Admin::RECORD_DELETE : Admin::ACTIVE;
        $whereCondition = (Yii::$app->user->id && Yii::$app->user->id == Admin::ADMIN_USER_ID) ? '!=' : '=';

        $infoTableName = TopicsInfo::tableName();
        $languageOption = Yii::$app->language;
        $languageOption = ($languageOption) ? $languageOption : Admin::DEFAULT_LANGUAGE;
        $languageOrder = [$languageOption, Admin::DEFAULT_LANGUAGE];
        $newObject = new \yii\db\Expression('FIND_IN_SET(' . $infoTableName . '.language, "' . implode(',', $languageOrder) . '")');
        return $this->hasMany(TopicsInfo::className(), ['topic_id' => 'topic_id'])
            ->andOnCondition([$infoTableName . '.language' => $languageOrder])
            ->andWhere([$whereCondition, 'topic_status', $status])
            ->orderBy([$newObject]);

    }

    /**
     * Relation returns the QuestionsInfo model which is related to the Questions model
     * @return \yii\db\ActiveQuery
     */
    public function getQuestionsInfo()
    {
        $infoTableName = QuestionsInfo::tableName();
        $languageOption = Yii::$app->language;
        $languageOption = ($languageOption) ? $languageOption : Admin::DEFAULT_LANGUAGE;
        $languageOrder = [$languageOption, Admin::DEFAULT_LANGUAGE];

        $status = (Yii::$app->user->id && Yii::$app->user->id == Admin::ADMIN_USER_ID) ? Admin::RECORD_DELETE : Admin::ACTIVE;
        $whereCondition = (Yii::$app->user->id && Yii::$app->user->id == Admin::ADMIN_USER_ID) ? '!=' : '=';

        $newObject = new \yii\db\Expression('FIND_IN_SET(' . $infoTableName . '.language, "' . implode(',', $languageOrder) . '")');
        return $this->hasMany(QuestionsInfo::className(), ['question_id' => 'question_id'])
            ->andOnCondition([$infoTableName . '.language' => $languageOrder])
            ->andWhere([$whereCondition, '' . $infoTableName . '.question_status', $status])
            ->orderBy([$newObject]);
    }

    /**
     * Create's Questions model based on the selected language if Questions model not exists with selected language
     * @param $model
     * @param null $infoModel
     * @return mixed
     * @throws \yii\web\NotFoundHttpException
     *
     */
    public static function createLanguageBasedQuestion($model, $infoModel = null)
    {
        $languageOption = Yii::$app->language;
        $languageOption = ($languageOption) ? $languageOption : Admin::DEFAULT_LANGUAGE;
        $baseModel = ($model->questionsInfo) ? $model->questionsInfo : $infoModel;
        $listOfLanguages = ArrayHelper::getColumn($baseModel, function ($element) {
            return $element['language'];
        });

        if (!in_array($languageOption, $listOfLanguages)) {

                $defaultLanguage = (in_array(Admin::DEFAULT_LANGUAGE, $listOfLanguages)) ? Admin::DEFAULT_LANGUAGE : $listOfLanguages[0];

                $englishLanguageContent = array_filter(ArrayHelper::getColumn($baseModel, function ($element) use (&$defaultLanguage) {
                    if ($element['language'] == $defaultLanguage){
                        return $element;
                    }

                }));

                $key = array_keys($englishLanguageContent);


                $englishContent = ($englishLanguageContent[$key[0]]->attributes) ? $englishLanguageContent[$key[0]]->attributes : $infoModel->attributes;

                if ($englishContent) {
                    self::saveQuestionInfo($model,$englishContent,$languageOption);
                } else {
                    $return['status'] = false;
                    $return['message'] = Yii::t('app', 'Parent attributes not present to create question');
                    return $return;
                }

        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Function to create the new Questions model info from base question model;
     */
    public static function saveQuestionInfo($model,$englishContent,$languageOption){

        $questionInfoModel = new QuestionsInfo();
        $questionInfoModel->scenario = 'quick_create';
        $questionInfoModel->attributes = $englishContent;
        $questionInfoModel->language = $languageOption;
        $questionInfoModel->question_status = Admin::DRAFT_VALUE;

        if ($questionInfoModel->save()) {

            $language = Yii::$app->language;
            $key = Yii::t('app', Admin::QUESTIONS_CACHE_KEY) . '_' . $language . '_' . $model->topic_id;

            Admin::clearCache($key);

            $language = Yii::$app->language;
            $key = Yii::t('app', Admin::TOP_QUESTIONS) . '_' . $language . '_' . $model->topic_id;
            Admin::clearCache($key);

            $url = Url::toRoute(['topics/get-question-info', 'topicslug' => $model->topic->slug, 'id' => $model->question_id, 'slug' => $model->slug]);

            header('Location: ' . $url);
            $return['status'] = true;
        } else {
            $return['status'] = false;
            $return['message'] = $questionInfoModel->getErrors();

            return $return;
        }

    }

    /**
     * Returns all the available languages for the Questions model
     * @param $questionID
     * @return string
     */
    public static function getLanguagesInfo($questionID)
    {
        try {
            $allModels = QuestionsInfo::find()
                ->where(['question_id' => $questionID])
                ->andWhere(['!=', 'question_status', Admin::RECORD_DELETE])
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

    /**
     * Relation which returns the parent Questions model
     * @return $this
     */

    public function getQuestionParentRelation()
    {
        return $this->hasOne(self::classname(), ['question_id' => 'parent_question_id'])->
            from(self::tableName() . ' AS parent');
    }

    /**
     * Relation which returns the child Questions model
     * @return $this
     */
    public function getQuestionChildRelation()
    {
        return $this->hasOne(self::classname(), ['parent_question_id' => 'question_id'])->
            from(self::tableName() . ' AS parent');
    }

    /**
     * Retuns the parent Question model status
     * @param $model
     * @return mixed
     */
    public static function getParentRelationStatus($model)
    {
        $parentRelation = $model->questionParentRelation;

        $status = $parentRelation->questionsInfo[0]->question_status;

        if (Yii::$app->user->isGuest && !$status) {
            $return['parentId'][] = $parentRelation->question_id;

            throw new NotFoundHttpException('The requested page does not exist.');

            $return['status'] = false;
            return $return;
        } else if ($parentRelation->parent_question_id != Admin::DEFAULT_PARENT_ID) {
            $return['parentId'][] = $parentRelation->question_id;
            $return = self::getParentRelationStatus($parentRelation);
            return $return;
        } else if ($parentRelation->parent_question_id == Admin::DEFAULT_PARENT_ID) {
            $return['parentId'][] = $parentRelation->question_id;
            $return['status'] = true;
            return $return;
        }
    }
}
