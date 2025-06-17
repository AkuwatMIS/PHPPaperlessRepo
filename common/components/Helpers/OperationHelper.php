<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 5/30/2018
 * Time: 4:12 PM
 */

namespace common\components\Helpers;


//use common\components\RbacHelper;
use common\models\Applications;
use common\models\ApplicationsCib;
use common\models\Branches;
use common\models\Operations;
use common\models\Provinces;
use common\models\StructurePayments;
use yii\data\SqlDataProvider;

class OperationHelper
{
    static function operationSummary($params)
    {

        $cond = '';
        $group_by_cond = 'operations.region_id';
        $columns_name = 'operations.id, regions.name as region_name,';
        if (!empty($params['OperationsSearch']['receive_date'])) {

            $date = explode(' - ', $params['OperationsSearch']['receive_date']);
            $cond .= " && receive_date between '" . strtotime($date[0]) . "' and '" . strtotime($date[1]) . "'";
        }
        if (!empty($params['OperationsSearch']['region_id'])) {
            $cond .= " && operations.region_id = '" . $params['OperationsSearch']['region_id'] . "'";
            $cond .= RbacHelper::searchRecoverySummaryFilters();
            if (isset($params['OperationsSearch']['area_id']) && !empty($params['OperationsSearch']['area_id'])) {
                $group_by_cond = 'operations.branch_id';
                $columns_name = 'operations.id, regions.name as region_name, areas.name as area_name, branches.code as branch_name,';
            } else {
                $group_by_cond = 'operations.area_id';
                $columns_name = 'operations.id, regions.name as region_name, areas.name as area_name,';
            }
        }
        if (isset($params['OperationsSearch']['area_id']) && !empty($params['OperationsSearch']['area_id'])) {
            $cond .= " && operations.area_id = '" . $params['OperationsSearch']['area_id'] . "'";
        }
        if (isset($params['OperationsSearch']['branch_id']) && !empty($params['OperationsSearch']['branch_id'])) {
            $cond .= " && operations.branch_id = '" . $params['OperationsSearch']['branch_id'] . "'";
        }
        if (!empty($params['OperationsSearch']['project_ids'])) {
            $project_ids = '';
            foreach ($params['OperationsSearch']['project_ids'] as $p) {
                $project_ids .= $p . ',';
            }
            $cond .= " && operations.project_id in (" . trim($project_ids, ',') . ")";
        }
        /*if(!empty($params['OperationsSearch']['crop_type'])){
            $cond .= " && borrowers.cropType = '".$params['OperationsSearch']['crop_type']."'";
        }*/
        if (empty($params['OperationsSearch']['region_id'])) {
            $cond .= RbacHelper::searchRecoverySummaryFilters();
        }
        $sql = "SELECT " . $columns_name . " COALESCE(count(distinct loan_id),0) as no_of_loans,operation_type_id,COALESCE(sum(credit),0) as amount from operations
                inner join applications on applications.id = operations.application_id
                inner join members on members.id = applications.member_id
                inner join branches on branches.id = operations.branch_id 
                inner join areas on areas.id = operations.area_id 
                inner join regions on regions.id = operations.region_id 
                " . $cond . " group by " . $group_by_cond . ",operations.operation_type_id ";
//,operations.operation_type_id
        /*(select COALESCE(sum(credit),0)from operations where operation_type_id=1 ) as takaf,
                        (select COALESCE(sum(credit),0)from operations where operation_type_id=2 ) as fee*/
//COALESCE(sum(credit),0) as takaf
//(select COALESCE(sum(credit),0) where operation_type_id=1) as takaf,
        //  (select COALESCE(sum(credit),0) where operation_type_id=2)
        $dataProvider = new SqlDataProvider([
            'sql' => $sql,
            'pagination' => [
                'pageSize' => 50,
            ],
        ]);
        /* echo'<pre>';
         print_r($cond);
 print_r($dataProvider->getModels());
 die();*/
        return $dataProvider;
    }

    public static function saveOperations($model, $type, $amount, $receipt_no, $receive_date, $platform = 1)
    {
        if ($type == 'takaf') {
            $operation_model = Operations::findOne(['application_id' => $model->application_id, 'operation_type_id' => 2]);
        }
        if ($type == 'fee') {
            $operation_model = Operations::findOne(['application_id' => $model->id, 'operation_type_id' => 1]);
        }
        if ($type == 'cib') {
            $operation_model = Operations::findOne(['application_id' => $model->id, 'operation_type_id' => 3]);
        }
        if ($type == 'nadra') {
            $operation_model = Operations::findOne(['application_id' => $model->id, 'operation_type_id' => 4]);
        }


        if (!isset($operation_model)) {
            if ($amount > 0) {
                $branch = Branches::find()->where(['id' => $model->branch_id])->one();
                $payments = StructurePayments::find()
                    ->where(['project_id' => $model->project_id])
                    ->andWhere(['type' => $type])
                    ->andWhere(['province_id' => $branch->province_id])
                    ->one();

                $operation_model = new Operations();
                $operation_model->platform = $platform;
                $operation_model->branch_id = $model->branch_id;
                $operation_model->team_id = $model->team_id;
                $operation_model->field_id = $model->field_id;
                $operation_model->project_id = $model->project_id;
                $operation_model->region_id = $model->region_id;
                $operation_model->area_id = $model->area_id;
                if ($type == 'takaf') {
                    $operation_model->application_id = $model->application_id;
                    $operation_model->loan_id = $model->id;
                    $operation_model->operation_type_id = 2;
                    $operation_model->receipt_no = $receipt_no;
                } else if ($type == 'fee') {
                    $operation_model->application_id = $model->id;
                    $operation_model->operation_type_id = 1;
                    $operation_model->get_receipt_no();
                } else if ($type == 'cib') {

//                    if ($model->branch_id != 814) {
//                        $province = Provinces::find()->where(['id' => $branch->province_id])->one();
//                        $cibFeePercent = (!empty($province->cib_tax_percent)) ? $province->cib_tax_percent : 0;
//                        $cibFee = (((int)$amount) / 100) * $cibFeePercent;
//                        if ($model->application_date > 1614538799 || $platform == 2) {
//                            $amount = $amount + $cibFee;
//                        }
//                    }

                    $operation_model->application_id = $model->id;
                    $operation_model->operation_type_id = 3;
                    $operation_model->receipt_no = $receipt_no;
                    if ($platform == 2) {
                        $operation_model->get_receipt_no();
                    } else {
                        $operation_model->receipt_no = $receipt_no;
                    }
                } elseif ($type == 'nadra') {
                    $operation_model->application_id = $model->id;
                    $operation_model->operation_type_id = 4;
                    $operation_model->get_receipt_no();

//                    if ($model->branch_id != 814) {
//                        $province = Provinces::find()->where(['id' => $branch->province_id])->one();
//                        $nadraPercent = (!empty($province->nadra_tax_percent)) ? $province->nadra_tax_percent : 0;
//                        $nadraFee = (!empty($province->nadra_fee)) ? $province->nadra_fee : 0;
//                        $amountPercent = ($nadraFee != 0 && $nadraPercent != 0) ? ceil(($nadraFee / 100) * $nadraPercent) : 0;
//                        $amount = $amountPercent;
//                    }
                }
//                if ($model->branch_id != 814) {
//                    $operation_model->credit = (int)$amount;
//                } else {
                    $operation_model->credit = (int)$payments->total_amount;
//                }
                $operation_model->receive_date = $receive_date;

            } else {
                return true;
            }
        } else {
            if ($amount == 0 || !isset($amount) || empty($amount)) {
                $operation_model->deleted = 1;
                $operation_model->deleted_at = strtotime('now');
                $operation_model->deleted_by = \Yii::$app->user->getId();
            } else {
                $types = array("fee", "nadra", "cib");
                if (in_array($type, $types)) {
                    $branch = Branches::find()->where(['id' => $model->branch_id])->one();
                    $payments = StructurePayments::find()
                        ->where(['project_id' => $model->project_id])
                        ->andWhere(['type' => $type])
                        ->andWhere(['province_id' => $branch->province_id])
                        ->one();
                    $operation_model->credit = $payments->total_amount;
                } else {
                    $operation_model->credit = $amount;
                }

                $operation_model->receive_date = $receive_date;
                $operation_model->deleted = 0;
            }
        }

        if (!($operation_model->save())) {
            return $operation_model->getErrors();
        } else {
            return true;
        }

    }
//    public static function saveCib($model)
//    {
//       $cib_model = ApplicationsCib::findOne(['application_id' => $application_id]);
//        if(!isset($cib_model)) {
//            if ($fee > 0) {
//                $cib_model = new ApplicationsCib();
//                $cib_model->application_id = $application_id;
//                $cib_model->fee = $fee;
//                $cib_model->receipt_no = $receipt_no;
//            }
//            else{
//                return true;
//            }
//        } else {
//            if ($fee == 0 || !isset($fee) || empty($fee)) {
//                $cib_model->deleted = 1;
//            } else {
//                $cib_model->fee = $fee;
//                $cib_model->receipt_no = $receipt_no;
//            }
//        }
//
//        if (!($cib_model->save())) {
//            return $cib_model->getErrors();
//        } else {
//            return true;
//        }
//
//    }
}