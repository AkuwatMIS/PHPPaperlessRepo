<?php
namespace frontend\controllers;

use common\components\Helpers\AppraisalsHelper;
use common\components\Helpers\FireBaseHelper;
use common\components\Helpers\PushHelper;
use common\models\ApplicationsCib;
use common\models\LoanTranches;
use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\LoginForm;
use common\models\search\GlobalsSearch;
use common\models\Applications;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'welcome', 'nacta-verification'],
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'welcome', 'nacta-verification'],
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
     * {@inheritdoc}
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
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionList()
    {
        $geocodeFromLatLong = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyAEAJ38v9Fn1TDAGotAPWCkepjjIkByB5U&latlng='.trim(31.4477809).','.trim(74.3151324).'&sensor=false');
        $output = json_decode($geocodeFromLatLong);
        $status = $output->status;
        $address = ($status=="OK")?$output->results[0]->formatted_address:'';
        //$address = AppraisalsHelper::getAppraisalAddress('31.4477809','74.3151324');
//        print_r($address);
//        die();
//        print_r(Yii::$app->cache->get('list'));
//        die();
    }

    public function actionCache()
    {
        ini_set('memory_limit', '1024M');
        Yii::$app->db->createCommand("update applications_cib set response = '' where created_at < 1617251986")->execute();
//        $cib = ApplicationsCib::find()->where(['<', 'created_at', 1617251986])->all();
//        foreach ($cib as $c){
//            $c->response = '';
//            if($c->save(false)){
//                echo '<>';
//                echo $c->id;
//                echo '<>';
//            }
//
//        }
        die();

//        Yii::$app->cache->flush('structure_branches');
//        Yii::$app->cache->flush('structure_districts');
//        $list = ['abc','xyz',date('Y-m-d H:i:s')];
//        Yii::$app->cache->set('list',$list,60*60);
//        print_r(Yii::$app->cache->get('list'));
    }

    public function actionDashboard()
    {
        return $this->render('dashboard');
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionOffline()
    {
        $this->layout = 'main_simple';
        return $this->render('offline');
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        return $this->redirect(['site/welcome']);
//        return $this->redirect(['site/offline']);
        //return $this->redirect(['branch-requests/index']);
    }

    public function actionWelcome()
    {
//       return $this->redirect('offline');
        $types = array('sanction_no'=>' Sanction No','borrower_cnic'=>' CNIC','grpno'=>' Group No');
        if(!empty(Yii::$app->request->queryParams)){
            $params = Yii::$app->request->queryParams;
            if(isset($params['GlobalsSearch']['sanction_no']) || isset($params['GlobalsSearch']['borrower_cnic']) || isset($params['GlobalsSearch']['grpno'])){
                $searchModel = new GlobalsSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                return $this->render('welcome', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'types' => $types
                ]);
            }else{
                $searchModel = new GlobalsSearch();
                return $this->render('welcome', [
                    'searchModel' => $searchModel,
                    'types' => $types
                ]);
            }
        }else{
            $searchModel = new GlobalsSearch();
            return $this->render('welcome', [
                'searchModel' => $searchModel,
                'types' => $types
            ]);
        }
        //return $this->render('welcome');
        //return $this->render('welcome');
       // return $this->redirect(['/members/index']);
    }



    public function actionNactaVerification()
    {
//       return $this->redirect('offline');
        if(!empty(Yii::$app->request->queryParams)){
            $params = Yii::$app->request->queryParams;

            if(isset($params['GlobalsSearch']['cnic'])){

                $searchModel = new GlobalsSearch();
                $dataProvider = $searchModel->searchNactaVerification(Yii::$app->request->queryParams);

                return $this->render('nacta-verification/index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                ]);
            }else{
                $searchModel = new GlobalsSearch();
                return $this->render('nacta-verification/index', [
                    'searchModel' => $searchModel,
                ]);
            }
        }else{
            $searchModel = new GlobalsSearch();
            return $this->render('nacta-verification/index', [
                'searchModel' => $searchModel,
            ]);
        }

    }






    /**
     * Logs in a user.
     *
     * @return mixed
     */
    public function actionLogin()
    {
        $this->layout = 'main_simple';
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            $auth = Yii::$app->authManager;
            $permissionslist = ($auth->getPermissionsByUser(Yii::$app->user->getId()));
            $permissions = [];
            foreach ($permissionslist as $key => $value) {
                $permissions[] = $key;
            }
            Yii::$app->session->set('permissions',$permissions);
            return $this->goBack();
        } else {
            $model->password = '';

            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logs out the current user.
     *
     * @return mixed
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        Yii::$app->session->remove('permissions');
        Yii::$app->session->remove('permissions_with_rule');
        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return mixed
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending your message.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Displays about page.
     *
     * @return mixed
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Signs user up.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        $this->layout = 'main_simple';
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }

        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $this->layout = 'main_simple';
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendResetPasswordEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        $this->layout = 'main_simple';
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
    public function actionTest()
    {
        $application=Applications::find()->where(['id'=>7])->one();

        PushHelper::newApplicationNotification($application);

    }
}
