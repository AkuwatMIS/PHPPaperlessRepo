<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 5/30/2018
 * Time: 1:01 PM
 */
namespace common\components\Helpers;


use yii\data\SqlDataProvider;
use Yii;

class DonationHelper
{
    static function donationSummary($params)
    {
        $cond = '';
        $group_by_cond = 'donations.region_id';
        $columns_name = 'donations.id, donations.region_id as region_name,';
        //$columns_name = 'donations.id, regions.name as region_name,';
        if (!empty($params['DonationsSearch']['receive_date'])) {

            //$date = explode(' - ', $params['DonationsSearch']['receive_date']);
            $cond .= " && receive_date = '" . strtotime($params['DonationsSearch']['receive_date'])."'";

        }
        if (!empty($params['DonationsSearch']['region_id'])) {
            $cond .= " && donations.region_id = '" . $params['DonationsSearch']['region_id'] . "'";
            $cond .= Yii::$app->Permission->searchReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
            if (isset($params['DonationsSearch']['area_id']) && !empty($params['DonationsSearch']['area_id'])) {
                $group_by_cond = 'donations.branch_id';
                $columns_name = 'donations.id, donations.region_id as region_name, donations.area_id as area_name, donations.branch_id as branch_name,';
                //$columns_name = 'donations.id, regions.name as region_name, areas.name as area_name, branches.code as branch_name,';
            } else {
                $group_by_cond = 'donations.area_id';
                $columns_name = 'donations.id, donations.region_id as region_name, donations.area_id as area_name,';
                //$columns_name = 'donations.id, regions.name as region_name, areas.name as area_name,';
            }
        }
        if (isset($params['DonationsSearch']['area_id']) && !empty($params['DonationsSearch']['area_id'])) {
            $cond .= " && donations.area_id = '" . $params['DonationsSearch']['area_id'] . "'";
        }
        if (isset($params['DonationsSearch']['branch_id']) && !empty($params['DonationsSearch']['branch_id'])) {
            $cond .= " && donations.branch_id = '" . $params['DonationsSearch']['branch_id'] . "'";
        }
        if (!empty($params['DonationsSearch']['project_ids'])) {
            $project_ids = '';
            foreach ($params['DonationsSearch']['project_ids'] as $p) {
                $project_ids .= $p . ',';
            }
            $cond .= " && donations.project_id in (" . trim($project_ids, ',') . ")";
        }
        /*if(!empty($params['DonationsSearch']['crop_type'])){
            $cond .= " && borrowers.cropType = '".$params['DonationsSearch']['crop_type']."'";
        }*/
        if (empty($params['DonationsSearch']['region_id'])) {
            $cond .= Yii::$app->Permission->searchReportsFilters($params['controller'],$params['method'],$params['rbac_type']);
        }
      //  , COALESCE(sum(mdp),0) as mdp
        $sql = "SELECT " . $columns_name . " COALESCE(count(loan_id),0) as no_of_loans, COALESCE(sum(amount),0) as amount from donations 
               where 1 and donations.deleted=0 " . $cond . "   group by " . $group_by_cond . "";
        /*$sql = "SELECT " . $columns_name . " COALESCE(count(loan_id),0) as no_of_loans, COALESCE(sum(amount),0) as amount from donations
                inner join branches on branches.id = donations.branch_id
                inner join areas on areas.id = donations.area_id
                inner join regions on regions.id = donations.region_id
               where 1 and donations.deleted=0 " . $cond . "   group by " . $group_by_cond . "";*/

        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        return $dataProvider;
    }
}