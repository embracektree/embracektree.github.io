<?php

namespace app\controllers;

use app\models\TopicsInfo;
use app\models\Media;
use app\models\QuestionsInfo;
use Yii;
use app\models\Topics;
use app\models\Questions;
use app\models\TopicsSearch;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\vendor\KTComponents\Admin;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * TopicsController implements the CRUD actions for Topics model.
 */
class TopicsController extends Controller
{

    /**
     * List of actions in the Controller and provides access based on the assigned role of the user
     * @return array
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update', 'create-topic'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update', 'create-topic'],
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                    [
                        'actions' => ['set-language', 'flush-cache'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Topics models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TopicsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Yii::$app->request->post());
        $dataProvider->pagination->pageSize = Admin::PAGINATION_LIMIT;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Topics model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param $topic_id
     * @param $language
     * @return string|Response
     */
    public function actionUpdate($topic_id, $language)
    {
        $mainModel = $this->findModel($topic_id);
        $mediaModel = ($mainModel->mediaData) ? $mainModel->mediaData : new Media();
        $model =$mainModel->topicsInfo[0];

        if (Yii::$app->request->post()) {

            $postInfo = Yii::$app->request->post();

            $mainModel->attributes = $postInfo['Topics'];
            $mainModel->topic_image = $postInfo['Topics']['topic_image'];
            $model->attributes = $postInfo['TopicsInfo'];
            $languageSelected = Yii::$app->language;

            $mainModel->topic_name = ($language == Admin::DEFAULT_LANGUAGE) ? $model->topic_name : $mainModel->topic_name;

            if ($mainModel->save() && $model->save()) {

                    self::saveTopicImage($mainModel,$mediaModel,$postInfo);

                    self::updateQuestionModel($mainModel,$model);

                    $language = Yii::$app->language;
                    $key = Yii::t('app', Admin::TOPICS_CACHE_KEY);
                    Admin::clearCache($key);

                    $key = Yii::t('app', Admin::QUESTIONS_CACHE_KEY) . '_' . $language . '_' . $model->topic_id;
                    Admin::clearCache($key);

                    $key = Yii::t('app', Admin::TOP_QUESTIONS) . '_' . $language . '_' . $topic_id;
                    Admin::clearCache($key);
                    Yii::$app->language = $languageSelected;
                    return $this->redirect(['index']);
            } else {
                Yii::$app->language = $languageSelected;
                return $this->render('update', [
                    'model' => $model,
                    'mainModel' => $mainModel
                ]);
            }

        } else {

            return $this->render('update', [
                'model' => $model,
                'mainModel' => $mainModel,
                'mediaModel' => $mediaModel
            ]);
        }
    }

    /**
     * Saving image of the topics model
     * @param $mainModel
     */
    public static function saveTopicImage($mainModel,$mediaModel,$postInfo)
    {

        if($mediaModel && (($mainModel->topic_image == '') || ($postInfo['topicImageHidden'] == ''))){
            $mediaModel->status = Admin::RECORD_DELETE;
            $mediaModel->save();
        }
        if($mainModel->topic_image != ''){
            $mediaModel->media_name = Yii::t('app', Topics::TOPIC_MEDIA_NAME);
            $mediaModel->media_id = $mainModel->topic_id;
            $mediaModel->image_path = $mainModel->topic_image;
            $mediaModel->status = Admin::ACTIVE;
            $mediaModel->save();
        }

    }

    /**
     * Search the Questions model based on the slug and id and redirects to the Questions view page
     * If model not found throws NotFoundHttpException
     * @param $id
     * @param $slug
     * @param null $preview
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionGetQuestionInfo($id, $slug, $preview = null)
    {

        $this->layout = 'topicMain';
        $model = Questions::findOne($id);
        $topicModel = $model->topic;

        $topicInfoModel = $topicModel->topicsInfo;

        foreach ($topicInfoModel as $topicInfo) {
            $languages[] = $topicInfo->language;
            $info[$topicInfo->language] = $topicInfo->attributes;
        }

        $questionInfoModel = $model->questionsInfo[0];

        $adminSettings = Yii::$app->getSettings();

        if ($adminSettings['site_name'] == '') {

            return $this->redirect('/adminSettings/adminindex');

        }

        $questionInfoModel = ($questionInfoModel) ? $questionInfoModel : QuestionsInfo::find()->where(['!=', 'question_status', 2])->andWhere(['question_id' => $id])->one();

        if ((($model == null) || $model->slug != $slug) || ((Yii::$app->user->isGuest && $questionInfoModel->question_status != Admin::PUBLISH_VALUE))) {

            throw new NotFoundHttpException('The requested page does not exist.');
        }

        ($model->parent_question_id != 0) ? Questions::getParentRelationStatus($model) : '';

        $languageOption = Yii::$app->language;
        $languageOption = ($languageOption) ? $languageOption : Admin::DEFAULT_LANGUAGE;

        $listOfLanguages = ArrayHelper::getColumn($model->questionsInfo, function ($element) {
            return $element['language'];
        });

        self::createTopicAndQuestion($topicModel, $preview, $languages, $model, $questionInfoModel, $listOfLanguages, $info);

        $previewParameter = Yii::$app->request->get('preview');

        $firstNode = Yii::$app->session->get('firstNode_' . $model->topic_id);

        Yii::$app->session->set('firstNode_' . $model->topic_id, '');


        return $this->render('question_info', [
            'infoModel' => $questionInfoModel,
            'model' => $model,
            'firstNode' => $firstNode,
            'previewParameter' => $previewParameter
        ]);

    }
    //ends here
    /**
     * Creates Topics and Questinod if they were not exists;
     * @param $topicModel
     * @param $preview
     * @param $languages
     * @param $model
     * @param $questionInfoModel
     * @param $listOfLanguages
     */
    public static function createTopicAndQuestion($topicModel, $preview, $languages, $model, $questionInfoModel, $listOfLanguages, $info)
    {
        $languageOption = Yii::$app->language;
        $previewStatus = (!Yii::$app->user->isGuest && !$preview) ? true : false;

        ($previewStatus && $topicModel  && !in_array($languageOption, $languages))
                ?
                Topics::createLanguageBasedTopics($topicModel, $info)
              : '' ;

        ($previewStatus && $model &&  !in_array($languageOption, $listOfLanguages))
                ?
                Questions::createLanguageBasedQuestion($model, $questionInfoModel)
              : '';

        self::setFlashMessage($questionInfoModel);

    }

    /**
     * Set the flash message for guest user if question not exists
     * @param $questionInfoModel
     */
    public static function setFlashMessage($questionInfoModel)
    {

        (Yii::$app->user->isGuest && $questionInfoModel->language != Yii::$app->language)
            ?
            Yii::$app->session->setFlash('warning', 'Question not exists in requested language.Default English language displayed')
            : '';

    }
    /**
     * Deletes an existing Topics model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param $topic_id
     * @param $language
     * @return Response
     */
    public function actionDelete($topic_id, $language)
    {
        $model = $this->findModel($topic_id);
        $infoModel = self::getLanguageBasedTopic($topic_id, $language);

        if ($infoModel) {
            $infoModel->topic_status = Admin::RECORD_DELETE;

            if ($infoModel->save()) {

                self::updateQuestionModel($model,$infoModel);

            }
        }

        $language = Yii::$app->language;
        $key = Yii::t('app', Admin::TOPICS_CACHE_KEY) . '_' . $language;

        Admin::clearCache($key);

        $key = Yii::t('app', Admin::TOP_QUESTIONS) . '_' . $language . '_' . $model->topic_id;
        Admin::clearCache($key);

        Yii::$app->session->setFlash('success', 'Record\'s Deleted Successfully ');

        return $this->redirect(['index']);
    }

    /**
     * Retuns the TopicsInfo model based on id and language
     *  if model not exists 404 HTTP exception will be thrown
     */
    public function getLanguageBasedTopic($id, $language = null)
    {
        $model = TopicsInfo::find()->where(['topic_id' => $id, 'language' => $language])
            ->andWhere(['!=', 'topic_status', Admin::RECORD_DELETE])->one();

        if ($model == null || ($model->topic_status == Admin::RECORD_DELETE)){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
         else{
             return $model;
         }

    }

    /**
     * Finds the Topics model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Topics the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Topics::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }


    /**
     *Updates the Questions model and QuestionsInfo if topic model is updated.
     * @param $mainModel
     */
    public static function updateQuestionModel($mainModel,$informationModel)
    {
        try {
            $inActiveQuestion = $mainModel->question;
            if ($inActiveQuestion) {
                foreach ($inActiveQuestion as $question) {
                    $infoModel = $question->questionsInfo;
                    foreach ($infoModel as $model) {
                        if ($model->question_status != Admin::RECORD_DELETE && $model->language == $informationModel->language) {
                            $model->scenario = 'quick_create';
                            $model->question_status = $informationModel->topic_status;
                            $model->save();
                        }
                    }
                }
                $return['status'] = true;
            } else {
                $return['status'] = false;
                $return['message'] = 'No Questions Present for Topics';
            }
        } catch (Exception $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();
        }
    }

    /**
     * Set's the selected language as default language
     */
    public function actionSetLanguage()
    {
        $language = $_POST['language'];
        Yii::$app->session->set('language', $language);
        Yii::$app->language = $language;
        $return = (Yii::$app->language == $language) ? 1 : 0;
        echo $return;

    }

    /**
     * Removes all the cached data .
     * @return Response
     */
    public function actionFlushCache()
    {
        $cache = Yii::$app->generalCache;

        $result = $cache->flush();
        if ($result) {
            Yii::$app->session->setFlash('success', 'Cache Removed Successfully ');
            return $this->goHome();
        }

    }

    /**
     * Create's a new Topics model.
     * @return array|string
     */
    public function actionCreateTopic()
    {
        try {
            $model = new Topics();
            $infoModel = new TopicsInfo();

            if (Yii::$app->request->post()) {

                $topicData = Yii::$app->request->post('Topics');

                $topicInfoData = Yii::$app->request->post('TopicsInfo');
                $model->topic_name = $topicInfoData['topic_name'];
                $model->topic_image = $topicData['topic_image'];

                $infoModel->topic_name = $model->topic_name;
                $infoModel->topic_short_desc = $topicInfoData['topic_short_desc'];
                $infoModel->topic_description = $topicInfoData['topic_short_desc'];

                $infoModel->language = Yii::$app->language ? Yii::$app->language : Yii::t('app', Admin::DEFAULT_LANGUAGE);

                if (Yii::$app->request->isAjax && Yii::$app->request->post()) {

                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ActiveForm::validate($infoModel);

                }

                $response = $this->saveTopicInformation($model, $infoModel);

                if ($response['status']) {
                    $url = Url::toRoute(['topics/get-question-info', 'topicslug' => $response['topicModel']['slug'], 'id' => $response['questionModel']['question_id'], 'slug' => $response['questionModel']['slug']]);
                    header("Location: " . $url);
                } else {
                    return json_encode($response);
                }
            }
        } catch (Exception $e) {

            $return['message'] = ($e->getMessage());
            $return['line'] = ($e->getLine());
            $return['file'] = ($e->getFile());

            return json_encode($return);
        }
    }

    /**
     * Updated the Topics Model on update the information based .
     * @param $model
     * @param $topicInfoModel
     * @return mixed
     **/
    public static function saveTopicInformation($model, $topicInfoModel)
    {

        $language = Yii::$app->language ? Yii::$app->language : Yii::t('app', Admin::DEFAULT_LANGUAGE);
        $topicInfoModel->language = $language;
        $topicInfoModel->topic_status = Admin::DRAFT_VALUE;

        if ($model->save()) {

            $topicInfoModel->topic_id = $model->topic_id;

            $topicInfoModel->save();

            if ($model->topic_image) {

                $mediaModel = new Media;
                $mediaModel->media_name = Yii::t('app', Topics::TOPIC_MEDIA_NAME);
                $mediaModel->media_id = $model->topic_id;
                $mediaModel->image_path = $model->topic_image;
                $mediaModel->status = Admin::ACTIVE;
                $mediaModel->save();

            }

            $questionModel = new Questions();
            $questionInfoModel = new QuestionsInfo();

            $questionModel->question_name = $model->topic_name;

            $questionModel->topic_id = $model->topic_id;

            $questionModel->parent_question_id = 0; //Need to change after adding option to create a parent topic .

            $lastRecordWithParent = Admin::getSortOrderValue($questionModel, 'parent_question_id', 'topic_id');
            $sortOrder = ($lastRecordWithParent) ? $lastRecordWithParent->sort_order + 1 : 1;

            $questionModel->sort_order = $sortOrder;

            $questionModel->save();

            $questionInfoModel->scenario = 'quick_create';
            $questionInfoModel->language = $topicInfoModel->language;
            $questionInfoModel->question_id = $questionModel->question_id;
            $questionInfoModel->question_name = $questionModel->question_name;
            $questionInfoModel->question_status = $topicInfoModel->topic_status;
            $questionInfoModel->question_description = '';

            $questionInfoModel->save();

            //clears cache data for topics

            $language = Yii::$app->language;
            $key = Yii::t('app', Admin::TOPICS_CACHE_KEY) . '_' . $language;
            Admin::clearCache($key);


            $key = Yii::t('app', Admin::QUESTIONS_CACHE_KEY) . '_' . $language . '_' . $model->topic_id;
            Admin::clearCache($key);


            $key = Yii::t('app', Admin::TOP_QUESTIONS) . '_' . $language . '_' . $model->topic_id;
            Admin::clearCache($key);

            $return['status'] = true;
            $return['topicModel'] = $model->attributes;
            $return['questionModel'] = $questionModel->attributes;
            $return['topicInfoModel'] = $topicInfoModel->attributes;
            $return['questionInfoModel'] = $questionInfoModel->attributes;

        } else {
            $return['status'] = false;
            $return['topicModel'] = $model->getErrors();
        }
        return $return;
    }


}
