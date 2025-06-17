<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 14/09/17
 * Time: 11:19 AM
 */

namespace common\components\Helpers;

use common\models\Analytics;
use common\models\Settings;
use Yii;
use common\models\Areas;
use common\models\Branches;
use common\models\BranchProjects;
use yii\helpers\Url;
use common\models\Users;
use common\components\NumberHelper;

class AnalyticsHelper
{
    public static function create($data){
        $model = Analytics::find()->where(['user_id'=>$data['user_id'],'api'=>$data['api']])->one();
        if($model){
            $model->count = $model->count +1;
            $model->save();
        }else{
            $model = new Analytics();
            $model->user_id = $data['user_id'];
            $model->api = $data['api'];
            $model->count = 1;
            $model->description = $data['api'];
            $model->type = $data['type'];
            $model->save();
        }
    }
}