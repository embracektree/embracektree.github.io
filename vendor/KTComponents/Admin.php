<?php
namespace app\vendor\KTComponents;

use app\models\Questions;
use yii;

use yii\base\Component;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\Languages;
use app\models\Topics;
use app\models\TopicsInfo;
use app\models\QuestionsInfo;
use app\modules\users\models\Users;


/**
 * These component is for the global functions which are used around the application
 * Class Admin extends Base Component.
 */
class Admin extends Component
{
    const ACTIVE = 1;
    const DRAFT_VALUE = 0;
    const RECORD_DELETE = 2;
    const PUBLISH_VALUE = 1;
    const DRAFT_TEXT = 'Draft';
    const PUBLISH_TEXT = 'Publish';
    const BOX_INFO = 'box-info';
    const BOX_SUCCESS = 'box-success';
    const BOX_WARNING = 'box-warning';
    const BOX_DANGER = 'box-danger';
    const BOX_PRIMARY = 'box-primary';


    const TOPICS_CACHE_KEY = 'topics_list';
    const QUESTIONS_CACHE_KEY = 'questions_list';
    const LATEST_TOPICS = 'latest_topics';
    const TOP_QUESTIONS = 'top_questions';
    const ADMIN_SETTINGS_CACHE_KEY = 'faq_settings';

    const DEFAULT_LANGUAGE = 'EN';
    const PAGINATION_LIMIT = 10;
    const ADMIN_USER_ID = 1;

    const QUESTION_LIMIT_COUNT = 3;
    const DEFAULT_PARENT_ID = 0;


    /**
     * Function which returns the status array
     */
    public static function getStatusList()
    {

        $statusList = [self::DRAFT_VALUE => Yii::t('app', self::DRAFT_TEXT), self::PUBLISH_VALUE => Yii::t('app', self::PUBLISH_TEXT)];
        return json_encode($statusList);
    }

    /**
     * Function which returns the Languages array
     */
    public static function getLanguagesList()
    {

        $languageList = Languages::find()
            ->where(['status' => self::ACTIVE])
            ->asArray()
            ->all();
        $languageList = ArrayHelper::map($languageList, 'short_name', 'language_name');
        return json_encode($languageList);
    }


    /**
     * Function which returns the TopicsList array
     */
    public static function getTopicsInformation()
    {

        $topicData = json_decode(self::getTopicsListInfo(), true);

        $topicData = ArrayHelper::map($topicData, 'topic_id', 'topic_name');

        return json_encode($topicData);
    }


    /**
     * Function which returns the TopicsList
     * @return string
     */
    public static function getTopicsListInfo()
    {
        $cache = Yii::$app->generalCache;
        $language = Yii::$app->language;
        $key = Yii::t('app', self::TOPICS_CACHE_KEY) . '_' . $language;

        if (Yii::$app->user->id) {
            $key = $key . '_' . Yii::$app->user->id;
        }

        $topicData = $cache->get($key);
        if (!$topicData) {
            $status = (Yii::$app->user->id && Yii::$app->user->id == self::ADMIN_USER_ID) ? self::RECORD_DELETE : self::ACTIVE;
            $whereCondition = (Yii::$app->user->id && Yii::$app->user->id == self::ADMIN_USER_ID) ? '!=' : '=';

            $topicsInfoTableName = TopicsInfo::tableName();
            $topicTableName = Topics::tableName();
            $languageOption = Yii::$app->language;
            $languageOption = ($languageOption) ? $languageOption : self::DEFAULT_LANGUAGE;
            $languageOrder = [$languageOption, self::DEFAULT_LANGUAGE];

            $topicData = Topics::find()
                ->joinWith('topicsInfo')
                ->andFilterWhere([$whereCondition, '' . $topicsInfoTableName . '.topic_status', $status])
                ->andOnCondition([$topicsInfoTableName . '.language' => $languageOrder])
                ->groupBy($topicsInfoTableName . '.topic_id')
                ->orderBy('' . $topicTableName . '.created_date ASC')
                ->asArray()
                ->all();
            $cache->set($key, $topicData, Yii::$app->params['cache']);
        }

        return json_encode($topicData);
    }

    /**
     * Function which returns the Questions model list  based on topic id
     * @param $topicId
     * @return string
     */
    public static function getTopicsQuestionList($topicId)
    {
        $cache = Yii::$app->generalCache;
        $language = Yii::$app->language;
        $key = Yii::t('app', self::QUESTIONS_CACHE_KEY) . '_' . $language . '_' . $topicId;

        if (Yii::$app->user->id) {
            $key = $key . '_' . Yii::$app->user->id;
        }

        $topicsData = $cache->get($key);
        if (!$topicsData) {

            $topicInfoTableName = QuestionsInfo::tableName();
            $topicTableName = Questions::tableName();

            $status = (Yii::$app->user->id && Yii::$app->user->id == self::ADMIN_USER_ID) ? self::RECORD_DELETE : self::ACTIVE;
            $whereCondition = (Yii::$app->user->id && Yii::$app->user->id == self::ADMIN_USER_ID) ? '!=' : '=';
            $topicsData = Questions::find()
                ->joinWith('questionsInfo')
                ->andFilterWhere([$whereCondition, '' . $topicInfoTableName . '.question_status', $status])
                ->andwhere(['' . $topicTableName . '.topic_id' => $topicId])
                ->groupBy($topicInfoTableName . '.question_id')
                ->asArray()
                ->orderBy('sort_order ASC')
                ->all();
            $cache->set($key, $topicsData, Yii::$app->params['cache']);
        }


        return json_encode($topicsData);
    }


    /**
     * Function to returns the Questions model list
     * @return string
     */
    public static function getQuestionsListInfo()
    {

        $cache = Yii::$app->generalCache;
        $key = Yii::t('app', self::QUESTIONS_CACHE_KEY);

        if (Yii::$app->user->id) {
            $key = $key . '_' . Yii::$app->user->id;
        }

        $topicsList = $cache->get($key);

        if (!$topicsList) {
            $topicInfoTableName = QuestionsInfo::tableName();
            $status = (Yii::$app->user->id && Yii::$app->user->id == self::ADMIN_USER_ID) ? self::RECORD_DELETE : self::ACTIVE;
            $whereCondition = (Yii::$app->user->id && Yii::$app->user->id == self::ADMIN_USER_ID) ? '!=' : '=';

            $topicsList = Questions::find()
                ->joinWith('topicsInfos')
                ->andFilterWhere([$whereCondition, '' . $topicInfoTableName . '.question_status', $status])
                ->asArray()
                ->all();

            $cache->set($key, $topicsList, Yii::$app->params['cache']);
        }
        return json_encode($topicsList);
    }

    /**
     * Function which returns the color for the boxes in index page
     * @return array
     */
    public static function getBoxClass()
    {
        return [self::BOX_INFO, self::BOX_SUCCESS, self::BOX_WARNING, self::BOX_DANGER, self::BOX_PRIMARY];
    }

    /**
     * Function retuns the box class name for the TopicsList.
     * @param $key
     * @return int
     */
    public static function getClassName($key)
    {
        if ($key > 4) {
            $value = $key % 4;
            if ($value > 4) {
                self::getClassName($value);
            } else {
                return $value;
            }
        } else{
            return $key;
        }

    }

    /**
     * Function used to fetch the Questions model based on the parent id and topic id
     * @param int $parent
     * @param $topicId
     * @return string
     */
    public static function getRecursiveQuestions($topicId,$parent = 0)
    {
        $topicsDataList = json_decode(self::getTopicsQuestionList($topicId), true);

        $items = array_filter($topicsDataList, function ($item) use (&$topicId, &$parent) {
            return $item['topic_id'] == $topicId && $item['parent_question_id'] == $parent;
        });
        return json_encode($items);

    }
//ends here

    /**
     *Function which is used for sort order fot Questions
     * @param $model
     * @param null $parentAttribute
     * @param null $conditionAttribute
     * @return mixed
     */
    public static function getSortOrderValue($model, $parentAttribute = null, $conditionAttribute = null)
    {
        $lastRecord = $model->find()->where([$parentAttribute => $model->$parentAttribute, $conditionAttribute => $model->$conditionAttribute])->groupBy('sort_order')->orderBy('sort_order DESC')->one();
        return $lastRecord;
    }

    /**
     * Function which returns the questions tree structure
     * @param $topicId
     * @param $topicSlug
     * @param null $previewParameter
     * @return array
     */
    public static function getQuestionsMenu($topicId, $topicSlug, $previewParameter = null)
    {

        $parentId = 0;
        $result = self::getRecursiveQuestionsMenu($parentId, $topicId, $topicSlug, $previewParameter);
        return $result;
    }

    /**
     *Function to recursively execute to build questions tree structure
     * @param $parent
     * @param $topicId
     * @param $topicSlug
     * @param null $previewParameter
     * @return array
     */
    public static function getRecursiveQuestionsMenu($parent, $topicId, $topicSlug, $previewParameter = null)
    {

        $completeInfo = json_decode(self::getTopicsQuestionList($topicId), true);

        $items = array_filter($completeInfo, function ($item) use (&$topicId, &$parent) {
            return $item['parent_question_id'] == $parent;
        });
        $result = [];
        if ($items) {
            foreach ($items as $item) {

                $infoClass = ($previewParameter) ? '' : ' <i class ="fa fa-info-circle topic_info" title="' . Yii::t('app', 'Single click to active. Double click to switch question') . '"></i>';
                $url = Url::toRoute(['topics/get-question-info', 'topicslug' => $topicSlug, 'id' => $item['question_id'], 'slug' => $item['slug'], 'preview' => $previewParameter]);

                $result[] = [
                    'title' => $item['questionsInfo'][0]['question_name'] . ' ' . $infoClass,
                    'id' => $item['question_id'],
                    'folder' => false,
                    'icon' => false,
                    'url' => $url,
                    'language' => $item['questionsInfo'][0]['language'],
                    'key' => $item['question_id'],
                    'preview' => $previewParameter,
                    'autoFocus' => true,
                    'children' => self::getRecursiveQuestionsMenu($item['question_id'], $item['topic_id'], $topicSlug, $previewParameter),
                ];
            }
        }
        return $result;
    }


    /**
     * Function which clears all the cached elements
     * @param $key
     * @return string
     */
    public static function clearCache($key)
    {
        $result = '';
        $cache = Yii::$app->generalCache;

        if (Yii::$app->user->id) {
            $key = $key . '_' . Yii::$app->user->id;
        }

        $existResult = $cache->exists($key);

        if ($existResult) {
            $result = $cache->delete($key);
        }
        return $result;
    }

    /**
     * Function to returns all the top question based on the topic id
     * @param $topicId
     * @return string
     */

    public static function getTopQuestions($topicId)
    {
        $cache = Yii::$app->generalCache;
        $language = Yii::$app->language;
        $key = Yii::t('app', self::TOP_QUESTIONS) . '_' . $language . '_' . $topicId;

        if (Yii::$app->user->id) {
            $key = $key . '_' . Yii::$app->user->id;
        }

        $questionInfoTableName = QuestionsInfo::tableName();
        $questionTableName = Questions::tableName();

        $topicsData = $cache->get($key);

        if (!$topicsData) {

            $status = (Yii::$app->user->id && Yii::$app->user->id == self::ADMIN_USER_ID) ? self::RECORD_DELETE : self::ACTIVE;
            $whereCondition = (Yii::$app->user->id && Yii::$app->user->id == self::ADMIN_USER_ID) ? '!=' : '=';

            $topicsData = Questions::find()
                ->joinWith('questionsInfo')
                ->andFilterWhere([$whereCondition, '' . $questionInfoTableName . '.question_status', $status])
                ->andwhere(['' . $questionTableName . '.topic_id' => $topicId])
                ->andwhere(['' . $questionTableName . '.parent_question_id' => self::DEFAULT_PARENT_ID])
                ->groupBy($questionInfoTableName . '.question_id')
                ->asArray()
                ->orderBy('' . $questionTableName . '.created_date DESC')
                ->limit(self::QUESTION_LIMIT_COUNT)
                ->all();

            $cache->set($key, $topicsData, Yii::$app->params['cache']);
        }
        return json_encode($topicsData);
    }

    /**
     * Function to save the user details posted from the installation screen
     * @param $installationForm
     */
    public static function updateSettings($installationForm)
    {

        try {

            $userModel = Users::findOne(self::ADMIN_USER_ID);

            if ($userModel) {

                $userModel->user_username = $installationForm['user_name'];
                $userModel->user_email = $installationForm['user_email'];
                $userModel->setPassword($installationForm['user_password']);
                $userModel->generatePasswordResetToken(); // code to generate password_reset_token
                $userModel->generateAuthKey(); // code to generate auth_key
                $userModel->user_password = md5($installationForm['user_password']);

                if (!$userModel->save()) {
                    $return['status'] = false;
                    $return['message'] = $userModel->getErrors();
                } else {
                    $return['status'] = true;
                    $return['message'] = $userModel->attributes;
                }

            } else {
                $return['status'] = false;
                $return['message'] = Yii::t('app', 'User model not exists to update the information');
            }
        } catch (Exception $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();
        }

    }
}

?>
