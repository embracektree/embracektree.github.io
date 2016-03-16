<?php

namespace app\controllers;

use app\models\QuestionsInfo;
use Yii;
use app\models\Questions;
use app\models\QuestionsSearch;
use yii\base\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\AccessControl;
use app\vendor\KTComponents\Admin;
use yii\helpers\Url;
use yii\helpers\BaseFileHelper;
use app\models\TopicsInfo;
use app\models\Topics;
use app\models\Media;
use app\vendor\sphinxlib\SphinxClient;


/**
 * QuestionsController implements the CRUD actions for Questions model.
 */
class QuestionsController extends Controller
{
    /**
     * List of actions in the Controller and provides access based on the assigned role of the user
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'update', 'quick-create-questions', 'create-new-question', 'update-question-content', 'update-node', 'move-image-to-folder', 'update-question-sort-order', 'render-image-widget'],
                'rules' => [
                    [
                        'actions' => ['index', 'update', 'quick-create-questions', 'create-new-question', 'update-question-content', 'update-node', 'move-image-to-folder', 'update-question-sort-order', 'render-image-widget'],
                        'allow' => true,
                        'roles' => ['admin'], //will be accesseble only for admin role which we set while saving user in the auth table not based on the role which we saved in the user table kt173
                    ],
                    [
                        'actions' => ['get-doc-info'],
                        'allow' => true,
                    ],

                ],
            ],
        ];
    }

    /**
     * Function to lists all Questions models information inthe grid structure.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new QuestionsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination->pageSize = Admin::PAGINATION_LIMIT;

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Updates an existing Questions model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param $question_id
     * @param $language
     * @return string|\yii\web\Response
     */

    public function actionUpdate($question_id)
    {

        $model = $this->findModel($question_id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Questions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param $question_id
     * @param $language
     * @return \yii\web\Response
     */

    public function actionDelete($question_id, $language)
    {
        $model = $this->findModel($question_id);

        $infoModel = self::getLanguageBasedQuestionInfo($question_id, $language);
        if ($infoModel) {
            $infoModel->scenario = 'quick_create';
            $infoModel->question_status = Admin::RECORD_DELETE;

            if ($infoModel->save()) {
                self::deleteChildQuestion($model);
            }
        }

        $key = Yii::t('app', Admin::QUESTIONS_CACHE_KEY) . '_' . $infoModel->language . '_' . $model->topic->topic_id;
        Admin::clearCache($key);

        $key = Yii::t('app', Admin::QUESTIONS_CACHE_KEY) . '_' . Yii::$app->language . '_' . $model->topic->topic_id;
        Admin::clearCache($key);

        $key = Yii::t('app', Admin::TOP_QUESTIONS) . '_' . $language . '_' . $model->topic->topic_id;
        Admin::clearCache($key);


        Yii::$app->session->setFlash('success', 'Record\'s Deleted Successfully ');
        return $this->redirect(['index']);
    }

    /**
     * Delete's child questions mdoel when a questions model is deleted
     * @param $model
     * @return bool
     */

    public static function deleteChildQuestion($model)
    {
        try {
            $child = $model->questionChildRelation;

            if ($child->parent_question_id != 0) {
                $childInformation = $child->questionsInfo[0];

                $childInformation->question_status = Admin::RECORD_DELETE;
                $childInformation->scenario = 'quick_create';
                if (!$childInformation->save()) {
                    $return['status'] = false;
                    $return['message'] = $childInformation->getErrors();

                }
                if ($child->parent_question_id != 0) {
                    $result = self::deleteChildQuestion($child);
                    return $result;
                } else {
                    return $return['status'] = true;
                }
            } else {
                return $return['status'] = true;
            }
        } catch (Exception $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();


        }

    }

    /**
     * Return's QuestionsInfo model based on the question id and language
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param $id
     * @param null $language
     * @return array|null|\yii\db\ActiveRecord
     * @throws \yii\web\NotFoundHttpException
     */
    public function getLanguageBasedQuestionInfo($id, $language = null)
    {
        $model = QuestionsInfo::find()->where(['question_id' => $id, 'language' => $language])->andWhere(['!=', 'question_status', Admin::RECORD_DELETE])->one();

        if ($model == null){
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        else{
            return $model;
        }

    }

    /**
     * Finds the Questions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Questions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Questions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     *Create's question child/sibling question
     */
    public function actionQuickCreateQuestions()
    {
        if (Yii::$app->request->isAjax) {
            $postedData = Yii::$app->request->post();
            if ($postedData) {

                $model = new Questions();
                $questionInfoModel = new QuestionsInfo();

                $selectedQuestion = $postedData['selectedQuestion'];
                $action = $postedData['action'];
                $model->parent_question_id = $selectedQuestion;

                if ($action == Questions::SIBLING) {
                    $siblingModel = Questions::findOne($selectedQuestion);
                    $model->parent_question_id = $siblingModel->parent_question_id;
                }

                $model->topic_id = $postedData['topicId'];
                $model->question_name = 'Question Name' . time();

                $lastRecordWithParent = Admin::getSortOrderValue($model, 'parent_question_id', 'topic_id');
                $sortOrder = ($lastRecordWithParent) ? $lastRecordWithParent->sort_order + 1 : 1;
                $model->sort_order = $sortOrder;

                $questionInfoModel->scenario = 'quick_create';
                $questionInfoModel->question_name = $model->question_name;
                $questionInfoModel->sort_order = $model->sort_order;

                $questionInfoModel->language = Yii::$app->language ? Yii::$app->language : Yii::t('app', Admin::DEFAULT_LANGUAGE);
                $questionInfoModel->question_description = '';
                $questionInfoModel->question_status = Admin::DRAFT_VALUE;

                if ($model->save()) {
                    $questionInfoModel->question_id = $model->question_id;
                    $questionInfoModel->save();

                    $language = Yii::$app->language;
                    $key = Yii::t('app', ADMIN::QUESTIONS_CACHE_KEY) . '_' . $language . '_' . $model->topic_id;
                    Admin::clearCache($key);

                    $return['status'] = true;
                    $return['message'] = $model->attributes;
                    $return['topicSlug'] = $model->topic->slug;

                } else {
                    $return['status'] = false;
                    $return['message'] = $model->getErrors();
                }
            } else {
                $return['status'] = false;
                $return['message'] = Yii::t('', 'Please post the data');
            }
        } else {
            $return['status'] = false;
            $return['message'] = Yii::t('', 'Method allows only Ajax Request');
        }
        echo json_encode($return);
    }

    /**
     * Updates information of an existing Questions model,based on the questoin id
     */

    public function actionUpdateQuestionContent()
    {
        try {
            if (Yii::$app->request->isAjax) {
                $postedData = Yii::$app->request->post();

                if ($postedData) {

                    $questionId = $postedData['questionId'];
                    $topicTitle = $postedData['topicTitle'];
                    $model = Questions::findOne($questionId);

                    $topicModel = $model->topic;
                    $topicInfoModel = $model->topic->topicsInfo[0];
                    $languageSelected = Yii::$app->language;

                    $topicModel->topic_name = ($languageSelected == Admin::DEFAULT_LANGUAGE) ? $topicTitle : $topicModel->topic_name;

                    $topicInfoModel->topic_name = $topicTitle;

                    if ($topicModel->save() && $topicInfoModel->save()) {
                        $return = self::updateQuestionContent($postedData, $model);
                    } else {
                        $return['status'] = false;
                        $return['message'] = Yii::t('app', 'Error while saving Topic name');
                    }
                } else {
                    $return['status'] = false;
                    $return['message'] = Yii::t('app', 'Please post the data');
                }
            } else {
                $return['status'] = false;
                $return['message'] = Yii::t('app', 'Method allows only Ajax Request');
            }
        } catch (Exception $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();
        }
        echo json_encode($return);
    }

    /**
     * Updates the Questions model and QuestionsInfo model while content is updated
     * @param $postedData
     * @param $model
     * @return mixed
     */
    public static function updateQuestionContent($postedData, $model)
    {

        $questionId = $postedData['questionId'];
        $questionTitle = $postedData['questionTitle'];
        $language = $postedData['topicLanguage'];
        $topicStatus = $postedData['topicStatus'];


        $infoModel = $model->questionsInfo[0];


        if ($model && $infoModel) {

            $infoModel->scenario = 'quick_create';
            $infoModel->question_description = $postedData['content'];
            $model->question_name = $questionTitle;
            $infoModel->question_name = $questionTitle;
            $infoModel->question_status = $topicStatus;

            if ($model->save()) {

                $infoModel->save();

                $language = Yii::$app->language;
                $key = Yii::t('app', ADMIN::QUESTIONS_CACHE_KEY) . '_' . $language . '_' . $model->topic_id;
                Admin::clearCache($key);

                $language = Yii::$app->language;
                $key = Yii::t('app', Admin::TOP_QUESTIONS) . '_' . $language . '_' . $model->topic_id;
                Admin::clearCache($key);

                $return['status'] = true;
                $return['message'] = $model->attributes;
                $return['topicName'] = $model->topic->slug;
                $return['selectedLanguage'] = Yii::$app->language;
            } else {
                $return['status'] = false;
                $return['message'] = Yii::t('app', 'Topic Name is unique for each level');
            }
        } else {
            $return['status'] = false;
            $return['message'] = Yii::t('app', 'Selected Topic not exists');
        }

        return $return;
    }

    /**
     *Update's the sort order of the Questions model on drag and drop of the questions
     */
    public function actionUpdateNode()
    {
        try {
            if (Yii::$app->request->isAjax) {
                $postedData = Yii::$app->request->post();

                if ($postedData) {
                    $draggedNodeId = $postedData['draggedNodeId'];
                    $droppedOnNodeId = $postedData['droppedOnNodeId'];
                    $draggedScenario = $postedData['draggedScenario'];

                    $droppedOnModel = Questions::findOne($droppedOnNodeId);
                    $draggedModel = Questions::findOne($draggedNodeId);
                    switch ($draggedScenario) {

                        case Questions::DRAGGED_BEFORE:
                        case Questions::DRAGGED_AFTER:

                            $topicUpdateStatus = self::updateQuestionNode($droppedOnModel, $draggedModel, $draggedScenario);

                            break;

                        case Questions::DRAGGED_OVER:

                            $topicUpdateStatus =  self::questionDragOver($droppedOnNodeId,$droppedOnModel,$draggedModel);

                            break;
                    }

                    $return['status'] = ($topicUpdateStatus) ? true : false;
                    $return['message'] = ($topicUpdateStatus) ? Yii::t('app', 'Successfully updated') : Yii::t('app', 'updation Failed');

                    $language = Yii::$app->language;
                    $key = Yii::t('app', ADMIN::QUESTIONS_CACHE_KEY) . '_' . $language . '_' . $droppedOnModel->topic_id;
                    Admin::clearCache($key);
                }

            } else {
                $return['status'] = false;
                $return['message'] = Yii::t('app', 'Method allows only Ajax Request');
            }
        } catch (Exception $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();
        }

        echo json_encode($return);
    }

    /**
     * Function which updates the nodes of the question models
     * @param $droppedOnModel
     * @param $draggedModel
     * @param $draggedScenario
     * @return bool
     */
    public static function updateQuestionNode($droppedOnModel, $draggedModel, $draggedScenario)
    {

        if ($droppedOnModel->parent_question_id != $draggedModel->parent_question_id && ($draggedScenario == Questions::DRAGGED_AFTER)) {

            $allChildTopics = Questions::find()
                ->andWhere(['parent_question_id' => $droppedOnModel->parent_question_id, 'topic_id' => $droppedOnModel->topic_id])
                ->orderBy('sort_order DESC')
                ->one();

            $draggedModel->sort_order = ($allChildTopics) ? $allChildTopics->sort_order + 1 : 1;


            $draggedModel->parent_question_id = $droppedOnModel->parent_question_id;

            $topicUpdateStatus = (!$draggedModel->save()) ? false : true;

        } else {

            $draggedModel->parent_question_id = $droppedOnModel->parent_question_id;
            $draggedModel->sort_order = $droppedOnModel->sort_order;
            $topicUpdateStatus = false;

            if ($draggedModel->save()) {

                self::updateAllChildNodes($droppedOnModel, $draggedModel, $draggedScenario);

                $topicUpdateStatus = true;
            }
        }

        return $topicUpdateStatus;
    }

    /**
     *Function to update alla the child nodes when drag and drop activity is done
     * @param $droppedOnModel
     * @param $draggedModel
     * @param $draggedScenario
     */
    public static function updateAllChildNodes($droppedOnModel, $draggedModel, $draggedScenario)
    {

        $sortCompareSymbol = ($draggedScenario == Questions::DRAGGED_BEFORE) ? '>' : '<';
        $sortOrder = ($draggedScenario == Questions::DRAGGED_BEFORE) ? 'ASC' : 'DESC';

        $allTopicOfParent = Questions::find()
            ->where(['parent_question_id' => $droppedOnModel->parent_question_id, 'topic_id' => $droppedOnModel->topic_id])
            ->andWhere(['!=', 'question_id', $draggedModel->question_id])
            ->andWhere([$sortCompareSymbol, 'sort_order', $draggedModel->sort_order])
            ->orWhere(['=', 'question_id', $droppedOnModel->question_id])
            ->orderBy('sort_order ' . $sortOrder)
            ->all();

        $i = $draggedModel->sort_order;

        foreach ($allTopicOfParent as $child) {

            $child->sort_order = ($draggedScenario == Questions::DRAGGED_BEFORE) ? $i + 1 : $i - 1;
            $child->save();

            ($draggedScenario == Questions::DRAGGED_BEFORE) ? $i++ : $i--;

        }
    }

    /**
     * Function to update Question model when Question Drag and drop activity is done.
     * @param $droppedOnNodeId
     * @param $droppedOnModel
     * @param $draggedModel
     * @return bool
     */
    public static function questionDragOver($droppedOnNodeId,$droppedOnModel,$draggedModel)
    {
        $allChildTopics = Questions::find()
            ->where(['parent_question_id' => $droppedOnNodeId, 'topic_id' => $droppedOnModel->topic_id])
            ->orderBy('sort_order DESC')
            ->one();

        $draggedModel->sort_order = ($allChildTopics) ? $allChildTopics->sort_order + 1 : 1;

        $draggedModel->parent_question_id = $droppedOnNodeId;

        $topicUpdateStatus = (!$draggedModel->save()) ? false : true;

        return $topicUpdateStatus;
    }
    /**
     *Return's the image path after moving image to the specific folder
     */
    public function actionMoveImageToFolder()
    {

        try {
            if ($_POST['url']) {
                $url = $_POST['url'];
                $contents = file_get_contents($url);
                $save_path = Yii::$app->params['uploadPath'] . '/' . Yii::t('app', Questions::TEMP_FOLDER) . '/';
                if (BaseFileHelper::createDirectory($save_path)) {
                    $imageName = explode('/', $url);
                    $name = $imageName[count($imageName) - 1];
                    $save_path = $save_path . $name;
                    if (file_put_contents($save_path, $contents)) {
                        $return['imagePath'] = Url::base(true) . '/upload/' . Yii::t('app', Questions::TEMP_FOLDER) . '/' . $name;
                        $return['status'] = true;
                    } else {
                        $return['status'] = false;
                        $return['message'] = 'Image not moved to specified folder';
                    }
                }
            } else {
                $return['status'] = false;
                $return['message'] = 'Please provide url';

            }

        } catch (Exception $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();
        }

        echo json_encode($return);
    }

    /**
     *Render the image widget
     * @return string
     */
    public function actionRenderImageWidget()
    {
        return $this->renderAjax('image_widget');
    }

    /**
     *
     * SELECT @n:=@n+1 as ROW,  topicinfo.topic_status as TOPIC_STATUS,questioninfo.question_status as QUESTION_STATUS, question.slug as QUESTION_SLUG,questioninfo.question_description as QUESTION_DESCRIPTION,
     * questioninfo.question_name as QUESTION_NAME,topic.slug as TOPIC_SLUG,topicinfo.topic_name as TOPIC_NAME,topicinfo.topic_description as TOPIC_DESCRIPTION,questioninfo.question_id as QUESTION_ID,questioninfo.language as LANGUAGE FROM `faq_questions_info` `questioninfo`
     * INNER JOIN `faq_questions` `question` ON question.question_id = questioninfo.question_id   INNER JOIN `faq_topics` `topic`
     * ON question.topic_id = topic.topic_id INNER JOIN `faq_topics_info` `topicinfo` ON topicinfo.topic_id = topic.topic_id
     * and topicinfo.topic_status != 2 and questioninfo.question_status != 2

     */


    /**
     * Search function based on the posted string, and returns the matched infomation
     * @param null $searchString
     */
    public function actionGetDocInfo($searchString = null)
    {

           try {

                $adminSettingsInfo = Yii::$app->getSettings();
                $response = [];
                switch ($adminSettingsInfo['enable_sphnix_search']) {

                    case true:

                        $s = new SphinxClient();
                        $s->open();
                        $s->setServer("localhost", 9312);
                        $s->setMatchMode(SPH_MATCH_ANY);
                        $s->setMaxQueryTime(0);
                        $searchResult = $s->query($searchString);
                        $topicIds = [];
                        $response = self::buildSphinxSearchResult($searchResult, $topicIds);

                        $response = ($response) ? $response : ['value' => 'Sphinx Connection Failed. Error Description : ' . $s->getLastError()];

                        break;

                    case false:

                        $topicMainTableName = Topics::tableName();
                        $topicInfoTableName = TopicsInfo::tableName();
                        $questionMainTableName = Questions::tableName();
                        $questionInfoTableName = QuestionsInfo::tableName();

                        $status = (Yii::$app->user->id && Yii::$app->user->id == Admin::ADMIN_USER_ID) ? Admin::RECORD_DELETE : Admin::ACTIVE;
                        $whereCondition = (Yii::$app->user->id && Yii::$app->user->id == Admin::ADMIN_USER_ID) ? '!=' : '=';

                        $query = new \yii\db\Query;
                        $query->select('topic.slug as TOPIC_SLUG,question.question_id as QUESTION_ID,question.slug as QUESTION_SLUG,questioninfo.question_description as DESCRIPTION,questioninfo.question_name as Question_NAME')
                            ->from($questionInfoTableName . ' questioninfo')
                            ->innerJoin($questionMainTableName . ' question', 'question.question_id = questioninfo.question_id')
                            ->innerJoin($topicMainTableName . ' topic', 'question.topic_id = topic.topic_id')
                            ->innerJoin($topicInfoTableName . ' topicInfo', 'topicInfo.topic_id =  topic.topic_id')
                            ->orFilterWhere(['like', 'questioninfo.question_description', $searchString])
                            ->orFilterWhere(['like', 'questioninfo.question_name', $searchString])
                            ->orFilterWhere(['like', 'question.slug', $searchString])
                            ->orFilterWhere(['like', 'topicInfo.topic_description', $searchString])
                            ->orFilterWhere(['like', 'topicInfo.topic_name', $searchString])
                            ->orFilterWhere(['like', 'topic.slug', $searchString])
                            ->andWhere([$whereCondition, 'topicInfo.topic_status', $status])
                            ->andWhere([$whereCondition, 'questioninfo.question_status', $status])
                            ->andWhere('IF(questioninfo.language = "' . Yii::$app->language . '",questioninfo.language = "' . Yii::$app->language . '",questioninfo.language = "EN")')
                            ->groupBy('questioninfo.question_name');

                        $command = $query->createCommand();
                        $queryResult = $command->queryAll();
                        $response = ($queryResult) ?
                            self::buildSearchResultNormalSearch($queryResult) : '' ;

                        break;
                }
            } catch (Exception $e) {
                $response[] = ['value' => $e->getMessage()];
            }

        echo json_encode($response);
    }

    /**
     * Function returns the search result for sphinx search.
     * @param $searchResult
     * @return array
     */
    public static function buildSphinxSearchResult($searchResult,$topicIds)
    {

        foreach ($searchResult['matches'] as $match) {

            $matchesInfo = $match['attrs'];
            $topic_status = $matchesInfo['topic_status'];
            $question_status = $matchesInfo['question_status'];
            $language = $matchesInfo['language'];

            $statusArray = (Yii::$app->user->isGuest) ? [Admin::ACTIVE] : [Admin::DRAFT_VALUE, Admin::ACTIVE];
            $languageOption = [Yii::$app->language, Admin::DEFAULT_LANGUAGE];

            if (in_array($topic_status, $statusArray) && in_array($question_status, $statusArray) && in_array($language, $languageOption)) {

                if ($language == Yii::$app->language && !in_array($matchesInfo['question_id'], $topicIds)) {

                    $topicIds[] = $matchesInfo['question_id'];
                    $totalList[$matchesInfo['question_id']] = $matchesInfo['question_id'];

                } else if (!in_array($matchesInfo['question_id'], $topicIds)) {

                    $totalList[$matchesInfo['question_id']] = $matchesInfo['question_id'];

                }

                $topicListInfo['topic_slug'] = $matchesInfo['topic_slug'];
                $topicListInfo['topicId'] = $matchesInfo['question_id'];
                $topicListInfo['question_name'] = $matchesInfo['question_name'];
                $topicListInfo['question_slug'] = $matchesInfo['question_slug'];

                $totalList[$matchesInfo['question_id']] = $topicListInfo;

            }
        }

        $trueResult = self::buildSearchResult($totalList);

        return $trueResult;
    }

    /**
     * Builds search result by taking array as input
     * @param $totalList
     */
    public static function buildSearchResult($totalList)
    {
        foreach ($totalList as $key => $value) {
            $url = Url::toRoute(['topics/get-question-info', 'topicslug' => $value['topic_slug'], 'id' => $key, 'slug' => $value['question_slug']]);
            $trueResult[] = ['value' => $value['question_name'], 'url' => $url];
        }

        return $trueResult;
    }
    /**
     * Returns result in array for normal search.
     * @param $queryResult
     * @return array
     */
    public static function buildSearchResultNormalSearch($queryResult)
    {

        foreach ($queryResult as $data) {
            $url = Url::toRoute(['topics/get-question-info', 'topicslug' => $data['TOPIC_SLUG'], 'id' => $data['QUESTION_ID'], 'slug' => $data['QUESTION_SLUG']]);
            $model = Questions::findOne($data['QUESTION_ID']);
            $parentTopicStatus = true;

            if ($model->parent_question_id != 0 && Yii::$app->user->isGuest) {
                $parentRelation = $model->questionParentRelation;
                $parentTopicStatus = $parentRelation->questionsInfo[0]->question_status;

            }

            if ($parentTopicStatus) {
                $response[] = ['value' => $data['Question_NAME'], 'url' => $url];
            }

        }

        return $response;
    }

    /**
     *Creates new Questions model when no Questinos exist for the Topics
     */
    public function actionCreateNewQuestion()
    {

        try {
            $topicId = $_POST['topicId'];
            $topicModel = Topics::findOne($topicId);
            $topicInfoModel = $topicModel->topicsInfo[0];

            if ($topicInfoModel) {

                $questionModel = new Questions();
                $questionInfoModel = new QuestionsInfo();
                $questionModel->question_name = $topicInfoModel->topic_name;
                $questionModel->topic_id = $topicInfoModel->topic_id;
                $questionModel->parent_question_id = 0; //Need to change after adding option to create a parent topic .
                $lastRecordWithParent = Admin::getSortOrderValue($questionModel, 'parent_question_id', 'topic_id');
                $sortOrder = ($lastRecordWithParent) ? $lastRecordWithParent->sort_order + 1 : 1;
                $questionModel->sort_order = $sortOrder;

                if ($questionModel->save()) {
                    $questionInfoModel->scenario = 'quick_create';
                    $questionInfoModel->language = $topicInfoModel->language;
                    $questionInfoModel->question_id = $questionModel->question_id;
                    $questionInfoModel->question_name = $questionModel->question_name;
                    $questionInfoModel->question_status = Admin::DRAFT_VALUE;
                    $questionInfoModel->question_description = '';
                    if ($questionInfoModel->save()) {
                        $return['status'] = true;
                        $return['topicslug'] = $topicModel->slug;
                        $return['questionId'] = $questionModel->question_id;
                        $return['questionSlug'] = $questionModel->slug;

                    } else {
                        $return['status'] = false;
                        $return['message'] = $questionInfoModel->getErrors();
                    }
                } else {
                    $return['status'] = false;
                    $return['message'] = $questionModel->getErrors();
                }

            } else {
                $return['status'] = false;
                $return['message'] = 'Information not found';

            }
        } catch (Exception $e) {
            $return['status'] = false;
            $return['message'] = $e->getMessage();

        }
        echo json_encode($return);
    }

}
