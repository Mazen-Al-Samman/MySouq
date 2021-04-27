<?php
namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\Post;
use common\models\User;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
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

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionIndex()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
    
        $post = new Post();
        $posts = $post->get_all_posts();
        return $this->render('index', ['posts' => $posts]);
    }

    public function actionAccept($id) {
        $post = new Post();
        $post->accept_post(Yii::$app->user->identity->user_role, $id);
        return $this->redirect(['site/index']);
    }

    public function actionBlock($id) {
        $post = new Post();
        $post->block_post(Yii::$app->user->identity->user_role, $id);
        return $this->redirect(['site/index']);
    }

    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $this->layout = 'blank';

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post())) {
            $email = $_POST['LoginForm']['email'];
            $user = new User();
            $is_admin = $user->check_is_admin($email);
            if ($is_admin) {
                $model->login();
                return $this->goBack();
            }
            return $this->redirect(['site/login']);
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
