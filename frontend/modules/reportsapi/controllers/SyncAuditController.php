<?php
namespace frontend\modules\reportsapi\controllers;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Parsers\ApiParser;
use common\models\Applications;
use common\models\Areas;
use common\models\Blacklist;
use common\models\Branches;
use common\models\Loans;
use common\models\Members;
use common\models\Recoveries;
use frontend\modules\api\behaviours\Verbcheck;
use yii\rest\ActiveController;

use Yii;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

use yii\web\Response;

/**
 * Site controller
 */

// to sync the shuffling of region area branch in audit mis

class SyncAuditController extends ActiveController
{
    public $modelClass = 'common\models\User';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\ContentNegotiator',
                //'only' => ['view', 'index'],  // in a controller
                // if in a module, use the following IDs for user actions
                // 'only' => ['user/view', 'user/index']
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],

                'languages' => [
                    'en',
                    'de',
                ],
            ],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        //unset($actions['index']);
        return $actions;
    }

    public function actionBranches()
    {
        $response = [];


        $branch_data = Branches::find()->select(['region_id' , 'area_id' , 'name','code','id'])->all();

        foreach ($branch_data as $key => $d){

            $data[$key]['region_id'] = $d->region_id;
            $data[$key]['area_id'] = $d->area_id;
            $data[$key]['name'] = $d->name;
            $data[$key]['id'] = $d->id;
            $data[$key]['code'] = $d->code;
        }

        if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['meta']['message'] = "MIS Branches Data";
            $response['data']['branches_lists'] = $data;
        }
        else{
           $response['meta']['success'] = false;
           $response['meta']['code'] = 600;
           $response['meta']['message'] = "No Record";
       }
        return JsonHelper::asJson($response);
    }

    public function actionAreas()
    {
        $response = [];


        $area_data = Areas::find()->select(['region_id' , 'name','code','id'])->all();

        foreach ($area_data as $key => $d){

            $data[$key]['id'] = $d->id;
            $data[$key]['region_id'] = $d->region_id;
            $data[$key]['name'] = $d->name;
            $data[$key]['code'] = $d->code;
        }

        if(isset($data)){
            $response['meta']['success'] = true;
            $response['meta']['code'] = 200;
            $response['meta']['message'] = "MIS Areas Data";
            $response['data']['areas_lists'] = $data;
        }
        else{
            $response['meta']['success'] = false;
            $response['meta']['code'] = 600;
            $response['meta']['message'] = "No Record";
        }
        return JsonHelper::asJson($response);
    }


}
