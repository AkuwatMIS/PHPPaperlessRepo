<?php

namespace console\controllers;

use common\models\Loans;
use common\models\Takafuldue;
use yii\console\Controller;
use \common\models\Operations;
use Yii;

class TakafulDueController extends Controller
{

//php yii takaful-due/takaf-due
    public function actionTakafDue()
    {
        $data = Takafuldue::getTakafulData();
        $from_date = strtotime(date('Y-01-01'));
        $to_date = strtotime(date('Y-12-31'));

        $from_month = strtotime(date('Y-m-01'));
        $to_month = strtotime(date('Y-m-t'));

      

        foreach ($data as $a) {
            $disb_date = strtotime($a['disburse_date']);
            $due_date = strtotime("+36 months", $a['disburse_date']);

            if ($disb_date >= $from_date && $disb_date <= $to_date) {
            } else {  
                if ($due_date >= $from_month && $due_date <= $to_month) {
                    $takaful = Takafuldue::find()->where(['loan_id' => $a['loan_id']])
                        ->andWhere(['takaful_year' => date("Y")])
                        ->one();
                              
                    if (empty($takaful)) {
                        $model = new Takafuldue();
                        $model->AddDueTakaf($a);

                    }
                }
            }
        }

    }

    public function actionInsertTakaful()
    {
        $loans = Loans::find()->where(['disbursed_amount' => 0])
            ->andWhere(['date_disbursed' => 0])
            ->andWhere(['status' => 'pending'])
            ->andWhere(['in', 'project_id', [77, 78, 79]])->all();
        foreach ($loans as $loan) {
            $operation = Operations::find()->where(['loan_id' => $loan->id])->one();
            if (empty($operation)) {
                $takaf = new Takafuldue();
                $takaf->loan_id = $loan->id;
                $takaf->branch_id = $loan->branch_id;
                $takaf->region_id = $loan->region_id;
                $takaf->area_id = $loan->area_id;
                $takaf->olp = $loan->loan_amount;
                $takaf->takaful_year = date("Y");
                $takaf->takaful_amnt = ($loan->loan_amount * 0.385) / 100;
                $takaf->save();
            }

        }
    }
}