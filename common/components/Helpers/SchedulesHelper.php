<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;
use common\models\Accounts;
use common\models\Activities;
use common\models\ApiKeys;
use common\models\Areas;
use common\models\AuthItem;
use common\models\BranchAccount;
use common\models\BranchAccountMapping;
use common\models\Branches;
use common\models\Cities;
use common\models\Countries;
use common\models\CreditDivisions;
use common\models\Districts;
use common\models\Divisions;
use common\models\Fields;
use common\models\Products;
use common\models\Projects;
use common\models\Provinces;
use common\models\Regions;
use common\models\Schedules;
use common\models\Teams;
use common\models\UserProjectsMapping;
use common\models\Users;
use common\models\BranchProjectsMapping;
use common\models\UserStructureMapping;
use common\models\Versions;
use Yii;

class SchedulesHelper
{

    public static function update_schdl($schedules)
    {
        $schedules->due_amnt = 0;
        $schedules->schdl_amnt = 0;
        $schedules->updated_by = 1;
        $schedules->save();
    }
    public static function adjust_last_schedule($l,$due_amount){
        $new_schedule = new Schedules();
        $new_schedule->loan_id = $l->id;
        $new_schedule->branch_id = $l->branch_id;
        $new_schedule->application_id = $l->application_id;
        $last_schdl = Schedules::find()->where(['loan_id' => $l->id])->orderBy('id desc')->one();
        $new_schedule->due_date = strtotime("+1 month", $last_schdl->due_date);
        $new_schedule->schdl_amnt = $due_amount;
        $new_schedule->assigned_to = '1';
        $new_schedule->created_by = '1';
        $new_schedule->save();
    }
}
