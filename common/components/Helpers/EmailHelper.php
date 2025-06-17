<?php

/**
 * Created by PhpStorm.
 * User: Junaid Fayyaz
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;

use Yii;
use common\models\SmsLogs;

class EmailHelper
{
    public static function SendEmail($from,$to,$message=NULL){

        $sendGrid = Yii::$app->sendGrid;
        $message = $sendGrid->compose('branch-request', ['message' => $message]);

        return $message->setFrom($from)
            ->setTo($to)
            ->setSubject('Akhuwat MIS :: Reset Password :: '.rand(1000,2000))
            ->send($sendGrid);
    }

    public static function getBranchRequestEmailMsg($model){
        $projects = array();
        foreach ($model->projects as $branch_projects){
            $projects[] = isset($branch_projects->project->name) ? $branch_projects->project->name : '';
        }
        $projects = implode(',', $projects);
        $branch_detail = array(
            'name' => isset($model->name) ? $model->name : '',
            'code' => isset($model->code) ? $model->code : '',
            'division' => isset($model->crdivision->name) ? $model->crdivision->name : '',
            'region' => isset($model->region->name) ? $model->region->name : '',
            'area' => isset($model->area->name) ? $model->area->name : '',
            'projects' => $projects,
        );
        return $branch_detail;
    }

}