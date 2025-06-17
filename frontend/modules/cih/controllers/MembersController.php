<?php

namespace frontend\modules\cih\controllers;


use common\components\Helpers\JsonHelper;
use common\models\Applications;
use common\models\Members;

use common\models\MembersPhone;
use Yii;


class MembersController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

//                'apiauth' => [
//                    'class' => Apiauth::className(),
//                    'exclude' => [],
//                    'callback'=>[]
//                ],
//                'access' => [
//                    'class' => AccessControl::className(),
//                    'denyCallback' => function ($rule, $action) {
//                        JsonHelper::asJson($this->sendFailedResponse('401','You are not allowed to perform this action.'));
//                    },
//                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
//                ],
//                'verbs' => [
//                    'class' => Verbcheck::className(),
//                    'actions' => [
//                        'index' => ['GET', 'POST'],
//                        'track' => ['GET'],
//                        'create' => ['POST'],
//                        'syncmembers' => ['POST'],
//                        'update' => ['PUT'],
//                        'bulkupdate' => ['PUT'],
//                        'view' => ['GET'],
//                        'details' => ['GET'],
//                        'delete' => ['DELETE'],
//                        'memberverification' => ['GET']
//                    ],
//                ],

            ];
    }


    public function actionSearchMember()
    {
        $paramsCnic  = isset($this->request['cnic']) ? $this->request['cnic'] : 0;
        if($paramsCnic!=0)
        {
            $member = Members::find()->where(['cnic' => $paramsCnic])->andWhere(['deleted' => 0])
                ->select(['id','full_name','parentage','cnic','status'])
                ->one();
            if(!isset($member) && empty($member) && $member==null)
            {
                $response['meta'] = [
                    'error' => true,
                    'message' => 'Member Not Found against CNIC '.$paramsCnic,
                    'status_code' => 201
                ];
                return JsonHelper::asJson($response);
            }
            else {
                $application = Applications::find()->where(['member_id' => $member->id])
                    ->andWhere(['status' => 'pending'])
                    ->andWhere(['deleted' => 0])
                    ->orderBy(['id'=>'SORT_DESC'])
                    ->one();

                if (!isset($application) && empty($application) && $application==null) {
                    $membersPhone = MembersPhone::find()->where(['member_id'=>$member->id])
                        ->andWhere(['is_current'=>1])
                        ->select(['id','phone'])
                        ->orderBy(['id'=>'SORT_DESC'])
                        ->one();

                        $response['meta'] = [
                            'error' => false,
                            'message' => 'success!',
                            'status_code' => 200
                        ];
                        $response['data']['id']        = $member->id;
                        $response['data']['name']      = $member->full_name;
                        $response['data']['parentage'] = $member->parentage;
                        $response['data']['cnic']      = $member->cnic;
                        $response['data']['status']    = $member->status;
                    if(!empty($membersPhone) && $membersPhone != null){
                        $phone = str_replace("92", "0", $membersPhone->phone);
                        $response['data']['phone'] = $phone;
                    }else{
                        $response['data']['phone'] = 00000000000;
                    }

                    return JsonHelper::asJson($response);
                } else {
                    $response['meta'] = [
                        'error' => true,
                        'message' => 'Application against this CNIC is already in-process!',
                        'status_code' => 201
                    ];
                    return JsonHelper::asJson($response);
                }
            }
        }else{
            $response['meta'] = [
                'error' => true,
                'message' => 'CNIC Input required!',
                'status_code' => 201
            ];
            return JsonHelper::asJson($response);
        }

    }


    protected function findModel($id)
    {
        if (($model = Members::findOne(['id' => $id,'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204,"Invalid Record requested");
        }
    }

}