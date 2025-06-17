<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 15/08/17
 * Time: 11:36 PM
 */

namespace common\components\Helpers\ReportsHelper;

use common\models\Branches;
use common\models\Designations;
use common\models\Projects;
use common\models\Users;
use Yii;

class UserHelper {

    private static $user = null;

    static public function getMyBranches(){
        $user = Yii::$app->user->identity;

        if(!empty($user->area_id)){
            return Branches::find()->where(['area_id'=>$user->area_id])->all();
        }

        return false;
    }

    static public function getMyProjects(){
        //$user = Yii::$app->user->identity;
        $project = Projects::find()->all();
        $projects = array();
        foreach ($project as $p){
            $projects[$p->funding_line.'_'.$p->name] = $p->name;
        }
        return $projects;
    }

    static public function verifyAccessToken($access_token){
        $exists = '';
        $designation_ids = array();
        $designations = Designations::find()->where(['mobile'=>1])->all();
        foreach ($designations as $d){
            $designation_ids[] = $d->id;
        }
        $user = Users::find()->where(['last_login_token' => $access_token])->andWhere(['in','designation_id', $designation_ids])->one();
        if($user){
            $exists = $user;
        }
        return $exists;
    }

} 