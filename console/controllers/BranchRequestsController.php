<?php
namespace console\controllers;

use common\components\Helpers\AccountsReportHelper;
use common\models\ArcAccountReportDetails;
use common\models\ArcAccountReports;
use common\models\Branches;
use common\models\BranchRequests;
use common\models\ProgressReports;
use common\models\ProgressReportUpdate;
use common\models\Projects;
use common\models\Users;
use Mpdf\Tag\Progress;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\console\Controller;
use common\models\ProgressReportDetails;
use common\components\Helpers\ProgressReportHelper;
class BranchRequestsController extends Controller
{
    public function  actionBranchShuffle()
    {
        $connection = \Yii::$app->db;
        $branches = BranchRequests::find()->where(['status' => 'approved','action'=>'branch_shuffle'])->all();

        $region = "";
        foreach ($branches as $branch) {
            $branch_old=Branches::findOne($branch->branch_id);

            if (isset($branch->region_id) && $branch->region_id > 0 && ($branch_old->region_id!=$branch->region_id)) {
                $region = ", m.region_id = " . $branch->region_id;
                $branch_old->region_id=$branch->region_id;
            }
            $branch_old->area_id=$branch->area_id;
            $branch_old->save();
            $sql = "UPDATE members m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE applications m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE loans m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE groups m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE donations m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE fund_requests m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE operations m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE progress_report_details m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE awp m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE awp_branch_sustainability m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE awp_loans_um m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE awp_overdue m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE awp_targer_vs_achievement m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE arc_account_report_details  m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $sql = "UPDATE recoveries m SET m.area_id = " . $branch->area_id . $region . " where m.branch_id = '" . $branch->branch_id . "' ";
            $connection->createCommand($sql)->execute();

            $users = Users::find()->select(['id'])->join('inner join', 'user_structure_mapping', ' user_structure_mapping.user_id = users.id')
                ->join('inner join', 'auth_assignment', ' auth_assignment.user_id = users.id')
                ->where(['obj_type' => 'branch'])->andWhere(['obj_id' => $branch->branch_id])->andWhere(['in', 'item_name', ['LO', 'BM']])->all();

            foreach ($users as $user) {
                $sql = "UPDATE user_structure_mapping m SET m.obj_id = " . $branch->area_id . " where user_id = '" . $user->id . "' and obj_type = 'area' ";
                $connection->createCommand($sql)->execute();
                if (isset($branch->region_id) && $branch->region_id > 0) {
                    $sql = "UPDATE user_structure_mapping m SET m.obj_id = " . $branch->region_id . " where user_id = '" . $user->id . "' and obj_type = 'region' ";
                    $connection->createCommand($sql)->execute();
                }

            }
            $branch->status='shuffled';
        }
    }
}