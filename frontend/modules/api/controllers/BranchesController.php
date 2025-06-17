<?php

namespace frontend\modules\api\controllers;


use common\components\DataListHelper;
use common\components\DBHelper;
use common\components\Helpers\CacheHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\StructureHelper;
use common\components\Helpers\UsersHelper;
use common\models\Areas;
use common\models\Branches;
use common\models\BranchProjects;
use common\models\BranchProjectsMapping;
use common\models\Fields;
use common\models\Lists;
use common\models\Teams;
use common\models\Users;
use common\components\Parsers\ApiParser;
use common\models\search\BranchesSearch;
use yii\filters\AccessControl;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;

use Yii;
use yii\helpers\ArrayHelper;


class BranchesController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => [],
                    'callback'=>[]
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        JsonHelper::asJson($this->sendFailedResponse('401','You are not allowed to perform this action.'));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'index' => ['GET', 'POST'],
                        'create' => ['POST'],
                        'syncapplications' => ['POST'],
                        'update' => ['PUT'],
                        'bulkupdate' => ['PUT'],
                        'view' => ['GET'],
                        'delete' => ['DELETE']
                    ],
                ],

            ];
    }

    public function actionIndex()
    {
        $user = Users::findIdentityByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $key = 'branches';
        $branches_data = CacheHelper::getUserIdentity($user->id,$key);
        if(empty($branches_data))
        {
            $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id,$this->rbac_type,$user->id);
            if(!empty($branches)) {
                $keys = array_keys($branches);
                $branches_list = Branches::find()->where(['in', 'id', $keys])->andWhere(['deleted' => 0])->all();
                $branches_data = ApiParser::parseBranches($branches_list);
            }
            CacheHelper::setUserIdentity($user->id,$key,$branches_data);
        }
        $response['data'] = $branches_data;
        return $this->sendSuccessResponse(200, $response['data']);
    }

    public function actionProjects()
    {
        $user = Users::findIdentityByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        /*$user = Users::findIdentityByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $cache_key = 'api_projects';
        $response = CacheHelper::getUserIdentity($user->id,$cache_key);
        if(empty($response)) {*/
            $areas = Yii::$app->Permission->getAreaList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type,$user->id);
            if (!empty($areas)) {
                $area_keys = array_keys($areas);

                $areas_list = Areas::find()->where(['in', 'id', $area_keys])->all();

                $branches = Yii::$app->Permission->getBranchList(Yii::$app->controller->id, Yii::$app->controller->action->id, $this->rbac_type,$user->id);
                $keys = array_keys($branches);
                $branch_projects =  BranchProjectsMapping::find()->where(['in', 'branch_id', $keys])->all();

                $areas_response = [];
                foreach ($areas_list as $key => $area_list) {
                    $area = ApiParser::parseArea($area_list);
                    if (!empty($branches)) {

                        $branches_list = Branches::find()->where(['in', 'id', $keys])->andWhere(['area_id' => $area_list->id])->andWhere(['deleted' => 0])->all();
                        $branch_response = [];
                        foreach ($branches_list as $k => $branch_list) {
                            $branch = ApiParser::parseBranch($branch_list);
                            $branch_response[$k] = array_merge($branch, array('teams' => self::getTeams($branch_list->id)));
                        }
                    }
                    $branch_data = ['branches' => $branch_response];
                    $areas_response[] = array_merge($area, $branch_data);
                }
                $response['data']['projects'] = ApiParser::parseProjects($branch_projects);
                $response['data']['period'] = Lists::find()->where(['list_name' => 'loan_period'])->orderBy('sort_order')->asArray()->all();
                $response['data']['areas'] = $areas_response;
                $response['data']['application_columns'] = DBHelper::getTableColumnsForFilters('applications');
                $application_data = Lists::find()->where(['list_name' => 'applications_status'])->orderBy('sort_order')->asArray()->all();
                $application_status = DataListHelper::getListData($application_data);
                $response['data']['status'] = $application_status;
            }
            /*CacheHelper::setUserIdentity($user->id, $cache_key, $response);
        }*/
        return $this->sendSuccessResponse(200, $response['data']);
    }

    protected function getTeams($branch_id){

        $teams = Teams::find()->where(['branch_id'=>$branch_id])->all();
        $team_response = [];

        foreach ($teams as $key => $team){
            $team_list = ApiParser::parseTeam($team);
            $team_response[$key] = array_merge($team_list, array('fields'=>self::getFields($team->id)));
        }
        return $team_response;
    }

    protected function getFields($team_id){
        $fields = Fields::find()->where(['team_id'=>$team_id])->all();
        $field_response = [];
        foreach ($fields as $key => $field){
            $field_response[$key] = ApiParser::parseField($field);
        }
        return $field_response;
    }

    protected function findModel($id)
    {
        if (($model = Branches::findOne(['id' => $id,'deleted' => 0])) !== null) {
            return $model;
        } else {
            return $this->sendFailedResponse(204, "Invalid Record requested");
        }
    }
}