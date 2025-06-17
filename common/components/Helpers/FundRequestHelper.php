<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


use common\components\Parsers\ApiParser;
use common\models\Areas;
use common\models\Branches;
use common\models\ConfigRules;
use common\models\Fields;
use common\models\FundRequestsDetails;
use common\models\Lists;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\LoanTranchesActions;
use common\models\Projects;
use common\models\Teams;

class FundRequestHelper
{
    public static function getFundRequestDetails($id){
        $fund_request_details = FundRequestsDetails::find()->where(['fund_request_id' => $id])->all();
        return ApiParser::parseFundRequestDetails($fund_request_details);
    }

    public static function getFundRequest($branch_id){
        $data = [];
        $loan_tranches = LoanTranches::find()->select(['loan_id, sum(tranch_amount) as total_amount, count(loan_tranches.id) as total_loans, loans.project_id,loans.branch_id'])
            ->joinWith('loan')
            //->join('inner join','loan_tranches_actions','loan_tranches_actions.parent_id=loan_tranches.id')
            //->where(['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>1])
            //->andWhere(['>=','loan_tranches_actions.expiry_date', date('Y-m-d H:i:s')])
            ->where(['in','loans.status' ,["pending","collected"]])
            ->andWhere(['loan_tranches.status' => 4, 'loans.branch_id' => $branch_id,'loan_tranches.fund_request_id' => 0,'loan_tranches.disbursement_id' => 0])
            ->groupBy(['project_id'])->asArray()->all();
        /*$loans = LoanTranches::find()->select(['sum(loan_amount) as total_amount, count(loans.id) as total_loans, project_id,branch_id'])
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->where(['loan_actions.action'=>'cheque_printing','loan_actions.status'=>1])
            ->andWhere(['>=','loan_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['loans.status' => "approved", 'branch_id' => $branch_id,'fund_request_id' => 0,'disbursement_id' => 0])->groupBy(['project_id'])->asArray()->all();*/
        /*print_r($loan_tranches);
        die();*/
        foreach ($loan_tranches as $loan_tranch) {
            $details['project_id'] = $loan_tranch['project_id'];
            $project = Projects::find()->select(['name'])->where(['id' => $loan_tranch['project_id']])->one();
            $details['project_name'] = $project['name'];
            $details['branch_id'] = $loan_tranch['branch_id'];
            $details['total_requested_amount'] = $loan_tranch['total_amount'];
            $details['total_loans'] = $loan_tranch['total_loans'];
            $data[] = $details;
        }

        return $data;
    }
    public static function getFundRequestProject($branch_id){
        $data = [];
        $loan_tranches = LoanTranches::find()->select(['loan_id, sum(tranch_amount) as total_amount, count(loan_tranches.id) as total_loans, loans.project_id,loans.branch_id'])
            ->joinWith('loan')
            //->join('inner join','loan_tranches_actions','loan_tranches_actions.parent_id=loan_tranches.id')
            //->where(['loan_tranches_actions.action'=>'cheque_printing','loan_tranches_actions.status'=>1])
            //->andWhere(['>=','loan_tranches_actions.expiry_date', date('Y-m-d H:i:s')])
            ->where(['in','loans.status' ,["pending","collected"]])
            ->andWhere(['in','loan_tranches.platform' ,[0,1]])
            ->andWhere(['loan_tranches.status' => 4, 'loans.branch_id' => $branch_id,'loan_tranches.fund_request_id' => 0,'loan_tranches.disbursement_id' => 0])
            ->andWhere(['in','loans.project_id',StructureHelper::trancheProjects()])
            ->orWhere(['and',['=','loans.platform',2],['in','loans.status' ,["pending","collected"]],['loan_tranches.status' => 4, 'loans.branch_id' => $branch_id,'loan_tranches.fund_request_id' => 0,'loan_tranches.disbursement_id' => 0]])
            ->groupBy(['project_id'])->asArray()->all();
        /*$loans = LoanTranches::find()->select(['sum(loan_amount) as total_amount, count(loans.id) as total_loans, project_id,branch_id'])
            ->join('inner join','loan_actions','loan_actions.parent_id=loans.id')
            ->where(['loan_actions.action'=>'cheque_printing','loan_actions.status'=>1])
            ->andWhere(['>=','loan_actions.expiry_date', date('Y-m-d H:i:s')])
            ->andWhere(['loans.status' => "approved", 'branch_id' => $branch_id,'fund_request_id' => 0,'disbursement_id' => 0])->groupBy(['project_id'])->asArray()->all();*/
        /*print_r($loan_tranches);
        die();*/
        foreach ($loan_tranches as $loan_tranch) {
            $details['project_id'] = $loan_tranch['project_id'];
            $project = Projects::find()->select(['name'])->where(['id' => $loan_tranch['project_id']])->one();
            $details['project_name'] = $project['name'];
            $details['branch_id'] = $loan_tranch['branch_id'];
            $details['total_requested_amount'] = $loan_tranch['total_amount'];
            $details['total_loans'] = $loan_tranch['total_loans'];
            $data[] = $details;
        }

        return $data;
    }
    public static function getFundRequestWeb($branch_id){
        $data = [];
        $loans = Loans::find()->select(['sum(loan_amount) as total_amount, count(loans.id) as total_loans, project_id,branch_id'])
            ->andWhere(['loans.status' => "approved", 'branch_id' => $branch_id,'fund_request_id' => 0,'disbursement_id' => 0])->groupBy(['project_id'])->asArray()->all();
        foreach ($loans as $loan) {
            $details['project_id'] = $loan['project_id'];
            $project = Projects::find()->select(['name'])->where(['id' => $loan['project_id']])->one();
            $details['project_name'] = $project['name'];
            $details['branch_id'] = $loan['branch_id'];
            $details['total_requested_amount'] = $loan['total_amount'];
            $details['total_loans'] = $loan['total_loans'];
            $data[] = $details;
        }

        return $data;
    }
    public static function rejectFundRequest($model){
        $tranches=LoanTranches::find()
            ->join('inner join','loans','loans.id=loan_tranches.loan_id')
            ->andFilterWhere(['loan_tranches.fund_request_id'=>$model->id])->all();
        foreach ($tranches as $t){
            $t->fund_request_id=0;
            $t->status=3;
            $t->save();
            $actions=LoanTranchesActions::find()->where(['parent_id'=>$t->id])->andWhere(['in','action',['fund_request','cheque_printing']])->all();
            foreach ($actions as $act){
                $act->status=0;
                $act->save();
            }
        }
    }
}