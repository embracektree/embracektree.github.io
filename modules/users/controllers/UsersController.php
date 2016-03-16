<?php

namespace app\modules\users\controllers;

use yii\filters\AccessControl;
use Yii;
use app\modules\users\models\Users;
use app\modules\users\models\UsersSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\modules\users\UsersModule;
use yii\rbac\DbManager;

/**
 * UserController implements the CRUD actions for Users model.
 */
class UsersController extends Controller
{

    /**
     * List of actions in the Controller and provides access based on the assigned role of the user
     */
    public function behaviors() //Authentication of the users
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['index', 'create', 'update'],
                'rules' => [
                    [
                        'actions' => ['index', 'create', 'update'],
                        'allow' => true,
                        'roles' => ['admin'], //will be accesseble only for admin role which we set while saving user in the auth table not based on the role which we saved in the user table kt173
                    ],
                ],
            ],
        ];
    }


    /**
     * Lists all Users models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsersSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Creates a new Users model.
     * If creation is successful, the browser will be redirected to the 'index' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Users();

        $model->user_status = 1;
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            $password = $model->user_password;
            $model->setPassword($password); // code to generate password_hash
            $model->generatePasswordResetToken(); // code to generate password_reset_token
            $model->generateAuthKey(); // code to generate auth_key
            $model->user_password = md5($password);
            $model->save();

            return $this->redirect(['index']);

        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Users model.
     * If update is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $password = $model->user_password;
        $model->user_password = '';
        $model->scenario = 'update';
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->user_password) {
                $password = $model->user_password;
                $model->setPassword($password); // code to generate password_hash
                $model->generatePasswordResetToken(); // code to generate password_reset_token
                $model->generateAuthKey(); // code to generate auth_key
            }
            $model->user_password = ($model->user_password) ? md5($model->user_password) : $password; //encrypting the password
            $model->save();
            $auth = Yii::$app->authManager;
            $authorRole = $auth->getRole('admin');
            $auth->assign($authorRole, $model->id);
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Users model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->user_status = 2;
        $model->save(false);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Users model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Users the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Users::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
