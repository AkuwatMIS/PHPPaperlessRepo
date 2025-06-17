<?php
/**
 * Created by PhpStorm.
 * User: Akhuwat
 * Date: 2/14/2018
 * Time: 12:14 PM
 */

namespace console\controllers;


use common\components\Helpers\FixesHelper;
use common\models\Loans;
use yii\console\Controller;
use Yii;

class FixesController extends Controller
{
    public function actionFixes()
    {
        $loan_ids = array('2541084'
    );
        foreach ($loan_ids as $loan_id){
            $loan = Loans::findOne(['id'=>$loan_id]);
            FixesHelper::fix_schedules_update($loan);
        }

    }

    public function actionLedgerGenerate()
    {
        $loan_ids = array(2437043
        );
        foreach ($loan_ids as $loan_id){
            $loan = Loans::findOne(['id'=>$loan_id]);
            FixesHelper::ledger_regenerate($loan);
        }

    }
    public function actionUpdateLoanExpiry()
    {
        $loan_ids = array();
        $loans = Loans::find()->where(['in','sanction_no',$loan_ids])->andFilterWhere(['deleted'=>0])->andWhere(['!=','disbursement_id','0'])->all();
        foreach ($loans as $loan){
            FixesHelper::update_loan_expiry($loan);
        }
    }

    public static function actionJs()
    {
        //$j = 'cscript odatajs-4.0.0.min.js';
        $js = 'cscript odata_request.js';

        //$output1 = shell_exec($j);
        $output = shell_exec($js);
        //print_r($output1);
        print_r($output);
        die();
    }

}


