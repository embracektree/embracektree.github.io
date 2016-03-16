<?php

namespace app\controllers;

use app\vendor\KTComponents\Admin;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\Topics;
use app\models\TopicsInfo;
use app\models\ContactForm;
use yii\data\ArrayDataProvider;

/**
 * SiteController for basic user actions and site default action..
 */
class SiteController extends Controller
{
    /**
     * List of actions in the Controller and provides access based on the assigned role of the user and user basic actions
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * List actions Error , captcha actions and more
     * @return array
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Site Default action which renders home page for the users
     * @return string
     */

    public function actionIndex()
    {

        if (!Yii::$app->getModule('admin')->installed) {
            return $this->redirect(['/install/step1']);
        } else {
            $cache = Yii::$app->generalCache;
            $cache->flush();
            $adminSettings = Yii::$app->getSettings();
            if ($adminSettings['site_name'] == '') {
                return $this->redirect('/adminSettings/adminindex');
            } else {
                $topicData = json_decode(Admin::getTopicsListInfo(), true);
                $provider = new ArrayDataProvider([
                    'allModels' => $topicData,

                ]);
                $provider->pagination->pageSize = Admin::PAGINATION_LIMIT;

                return $this->render('/topics/_latest_topics', [
                    'dataProvider' => $provider,
                    'mainModel' => new Topics(),
                    'infoModel' => new TopicsInfo(),
                ]);
            }
        }
    }


    /**
     * User login and authentication
     * @return string|\yii\web\Response
     */
    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        $model->rememberMe = 0;
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', ['model' => $model]);
    }

    /**
     * User logout and redirects to home page
     * @return string|\yii\web\Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        //clearing the all cache while logout the user.

        $cache = Yii::$app->generalCache;

        $result = $cache->flush();
        Yii::$app->session->set('language','');
        if ($result){
            return $this->goHome();
        }

    }

    /**
     * Contact information
     * @return string|\yii\web\Response
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');
            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * About the site
     * @return string|\yii\web\Response
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
