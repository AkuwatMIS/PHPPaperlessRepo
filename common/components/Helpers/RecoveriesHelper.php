<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 5/30/2018
 * Time: 1:01 PM
 */

namespace common\components\Helpers;

use common\models\Branches;
use common\models\Members;
use common\models\Recoveries;
use common\models\Schedules;
use yii\data\SqlDataProvider;
use Yii;
use yii\db\Query;

class RecoveriesHelper
{
    public static function getInstallmentNo($loan)
    {
        $recovery = new Recoveries();
        $recovery->receive_date = strtotime('now');
        $schedule_info = $recovery->GetScheduleInfo($loan);
        return Schedules::find()->where(['loan_id' => $loan->id])->andWhere(['<=','due_date',$schedule_info->due_date])->count();
    }

    static function recoverySummary($params)
    {
        $cond = '';
        $group_by_cond = 'recoveries.region_id';
        $columns_name = 'recoveries.id, recoveries.region_id  as region_name,';
        //$columns_name = 'recoveries.id, regions.name as region_name,';
        if (!empty($params['RecoveriesSearch']['receive_date'])) {

            //$date = explode(' - ', $params['RecoveriesSearch']['receive_date']);
            $cond .= " && receive_date = '" . strtotime($params['RecoveriesSearch']['receive_date'])."'";
        }
        if (!empty($params['RecoveriesSearch']['region_id'])) {
            $cond .= " && recoveries.region_id = '" . $params['RecoveriesSearch']['region_id'] . "'";
            $cond .= Yii::$app->Permission->searchReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
            if (isset($params['RecoveriesSearch']['area_id']) && !empty($params['RecoveriesSearch']['area_id'])) {
                $group_by_cond = 'recoveries.branch_id';
                $columns_name = 'recoveries.id, recoveries.region_id as region_name, recoveries.area_id as area_name, recoveries.branch_id as branch_name,';
                //$columns_name = 'recoveries.id, regions.name as region_name, areas.name as area_name, branches.code as branch_name,';
            } else {
                $group_by_cond = 'recoveries.area_id';
                $columns_name = 'recoveries.id, recoveries.region_id as region_name, recoveries.area_id as area_name,';
                //$columns_name = 'recoveries.id, regions.name as region_name, areas.name as area_name,';
            }
        }
        if (isset($params['RecoveriesSearch']['area_id']) && !empty($params['RecoveriesSearch']['area_id'])) {
            $cond .= " && recoveries.area_id = '" . $params['RecoveriesSearch']['area_id'] . "'";
        }
        if (isset($params['RecoveriesSearch']['branch_id']) && !empty($params['RecoveriesSearch']['branch_id'])) {
            $cond .= " && recoveries.branch_id = '" . $params['RecoveriesSearch']['branch_id'] . "'";
        }
        if (!empty($params['RecoveriesSearch']['project_ids'])) {
            $project_ids = '';
            foreach ($params['RecoveriesSearch']['project_ids'] as $p) {
                $project_ids .= $p . ',';
            }
            $cond .= " && recoveries.project_id in (" . trim($project_ids, ',') . ")";
        }
        /*if(!empty($params['RecoveriesSearch']['crop_type'])){
            $cond .= " && members.cropType = '".$params['RecoveriesSearch']['crop_type']."'";
        }*/
        if (empty($params['RecoveriesSearch']['region_id'])) {
            $cond .= Yii::$app->Permission->searchReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
        }
        $sql = "SELECT " . $columns_name . " COALESCE(count(loan_id),0) as no_of_loans, COALESCE(sum(amount),0) as amount from recoveries
                where 1 and recoveries.deleted=0 " . $cond . " group by " . $group_by_cond . " ";
        /*$sql = "SELECT " . $columns_name . " COALESCE(count(loan_id),0) as no_of_loans, COALESCE(sum(amount),0) as amount from recoveries
                inner join branches on branches.id = recoveries.branch_id
                inner join areas on areas.id = recoveries.area_id
                inner join regions on regions.id = recoveries.region_id
                where 1 and recoveries.deleted=0 " . $cond . " group by " . $group_by_cond . " ";*/



        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $dataProvider;
    }

    public static function getAreaofbranch($branch_id)
    {
        return Branches::find()->select(['area_id'])->where(['id' => $branch_id])->all();

    }

    public static function getRegionofbranch($branch_id)
    {
        return Branches::find()->select(['region_id'])->where(['id' => $branch_id])->all();

    }

    public static function getDueAmount($loan){
        $date = strtotime('Y-m-t');
        $due_amnt = 0;
        $select_query = "
            select
                    (select COALESCE(sum(s.schdl_amnt),0) from schedules s where s.loan_id = l.id and s.due_date <= '".$date."') as schdl_till_current_month,
                    (select COALESCE(sum(amount),0) from recoveries r where r.loan_id = l.id and r.receive_date <= '".$date."') as credit
                    from loans l 
                    inner join groups g on g.id = l.group_id
                    where l.status = 'collected' and l.id = ".$loan->id."
        ";
        $loan_data = Yii::$app->db->createCommand($select_query)->queryAll();
        if($loan->inst_amnt > $loan->balance){
            $due_amnt = $loan->balance;
        }else{
            $last_month_schdules = $loan_data[0]['schdl_till_current_month'] - $loan->inst_amnt;
            $adv_od = $last_month_schdules - $loan_data[0]['credit'];
            if($adv_od <= 0){
                $due_amnt = $loan->inst_amnt;
            }else{
                $due_amnt = $loan_data[0]['schdl_till_current_month'] - $loan_data[0]['credit'];
                $due_amnt += $loan->inst_amnt;
            }
        }
        return $due_amnt;
    }

    public static function getReceiveAmount($loan){
        $recv_amnt = 0;
        $select_query = "
            select
                    (select COALESCE(sum(amount),0) from recoveries r where r.loan_id = l.id) as credit
                    from loans l 
                    where l.id = ".$loan->id."
        ";
        $loan_data = Yii::$app->db->createCommand($select_query)->queryAll();
        $recv_amnt = $loan_data[0]['credit'];
        return $recv_amnt;
    }
    public static function searchSanctionnoByCnic($cnic)
    {

        $sanction_no = '';
        /*$member= Members::find()->joinWith('applications')->innerJoin('loans')->where(['members.cnic' => $cnic,'loans.status' => 'Collected'])->asArray()->one();
        if(isset($member))
        {
            $sanction_no = $member['loan']['sanction_no'];
        }*/
        $query = new Query();
        $query->select('loans.sanction_no')
            ->from('loans')
            ->join('inner join','applications','loans.application_id=applications.id')
            ->join('inner join','members','members.id=applications.member_id')
            ->andFilterWhere(['=', 'members.cnic',$cnic])
            ->andFilterWhere(['=', 'loans.status','Collected']);
        $command = $query->createCommand();
        $data = $command->queryOne();
        if(isset($data))
        {
            $sanction_no = $data['sanction_no'];
        }
        return $sanction_no;
    }
}