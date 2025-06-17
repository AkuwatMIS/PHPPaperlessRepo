<?php

namespace frontend\controllers;



use common\components\Helpers\ImageHelper;
use common\components\Helpers\MemberHelper;
use common\models\AccessTokens;
use Guzzle\Http\Exception\BadResponseException;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;
use yii\db\Query;


class ImageController extends Controller
{
    public $rbac_type = 'frontend';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type)
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        /*print_r(Yii::$app->request->csrfToken);
        die();*/
        $this->layout = false;
        $request = Yii::$app->request->get();
        if(!array_key_exists('access_token',$request))
        {
            $this->throwException('Access Token not set.');
        }

        $access_token = AccessTokens::findOne(['token' => $request['access_token']]);
        if ($access_token) {
            if ($access_token->expires_at < time()) {
                $this->throwException('Access Token Expires.');
            }
            if($access_token->user_id != Yii::$app->user->getId())
            {
                $this->throwException('Invalid Access Token.');
            }
        } else {
            $this->throwException('Invalid Access Token.');
        }

        if(!array_key_exists('id',$request))
        {
            $this->throwException('Id not set.');
        }
        if(!array_key_exists('type',$request))
        {
            $this->throwException('Type not set.');
        }
        if(!array_key_exists('parent_type',$request))
        {
            $this->throwException('Parent Type not set.');
        }

        Yii::$app->Permission->getImageRbac(Yii::$app->controller->id, Yii::$app->controller->action->id,$request['id'],$request['parent_type']);
        $image = ImageHelper::getImageUrl($request['id'],$request['type'],$request['parent_type']);
        if(!empty($image)) {
           return $this->render('index', ['url' => $image]);
        } else {
            throw new NotFoundHttpException('Image Not Exist.');
        }
    }

    protected function throwException($message)
    {
        throw new BadRequestHttpException($message);
    }

}
