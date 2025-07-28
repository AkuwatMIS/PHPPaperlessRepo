<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

use common\models\ApplicationsCib;
use common\models\Branches;
use common\models\CibTypes;
use common\models\ConnectionBanks;
use common\models\LoanTranches;
use common\models\Visits;
use Yii;

class AcagHelper
{

    public static function actionPush($application, $Status, $Reason, $amount, $processDate, $visitId = 0, $loan)
    {
        $cnic = str_replace("-", "", $application->member->cnic);
        $Lat = null;
        $Long = null;
        $isCompleted = null;
        $isShifted = null;
        $AmountApproved = null;
        $AmountDisbursed = null;
        $recoveryAmount = null;
        $percent = 0;

        if (isset($loan) && $loan != null) {
            $AmountApproved = $loan->loan_amount;

            $loan_tranches = LoanTranches::find()
                ->where(['loan_id' => $loan->id])
                ->andWhere(['status' => 6])
                ->all();
            $amount_sum = 0;
            foreach ($loan_tranches as $key => $amount) {
                $amount_sum += $amount->tranch_amount;
            }

            $AmountDisbursed = $amount_sum;

            if ($Status == 'Visit') {
                $Status = null;
                $Reason = null;
                $visit = Visits::find()->where(['id' => $visitId])->one();
                $isShifted = ($visit->is_shifted > 0) ? true : false;
            }elseif ($Status =='VisitPercent'){
                $Status = null;
                $Reason = null;
                $percent = 100;
            }elseif ($Status =='Recovery'){
                $Status = null;
                $Reason = null;
                $recoveryAmount = $amount;
            }elseif ($Status =='Construction'){
                $Status = null;
                $Reason = null;
                $visit = Visits::find()->where(['id' => $visitId])->one();
                $recoveryAmount = $amount;
                $isCompleted = ($visit->percent == 100) ? true : false;
                $percent = $visit->percent;
            }elseif ($Status =='Loan Rejected'){
                $recoveryAmount = 0;
                $AmountDisbursed = 0;
            } else {
                $visit = Visits::find()->where(['parent_type' => 'application'])
                    ->andWhere(['parent_id' => $loan->application_id])
                    ->one();
            }

            $Long = $visit->longitude;
            $Lat = $visit->latitude;
            if (!empty($visit) && $visit != null) {
                $Long = $visit->longitude;
                $Lat = $visit->latitude;
                $percent = $visit->percent;
            }
        }
        $postFields = json_encode([
            "CNIC" => $cnic,
            "MISStatusDesc" => $Status,
            "MISStatusDateTime" => $processDate,
            "MISApprovedAmount" => $AmountApproved,
            "MISDisbAmount" => $AmountDisbursed,
            "MISReasonDesc" => $Reason,
            "MISCompletionPer" => "$percent",
            "IsCompleted" => $isCompleted,
            "MISRecoveryAmount" => $recoveryAmount,
            "IsShifted" => $isShifted,
            "Long" => strval($Long),
            "Lat" => strval($Lat)
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://mis.akhuwat.org.pk:4969/api/pitb/update',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'X-API-Key: 123456789',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

//        if ($response === false) {
//            echo 'Curl error: ' . curl_error($curl);
//        } else {
//            echo 'Response: ' . $response;
//        }

        curl_close($curl);
        return true;
    }

    public static function actionGet($cnic)
    {
        $cnic = str_replace("-", "", $cnic);


        $postFields = json_encode([
            "CNIC" => $cnic
        ]);

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://mis.akhuwat.org.pk:4969/api/pitb/applicantlookupviacnic',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'X-API-Key: 123456789',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $responseData = json_decode($response, true);
        if ($responseData) {
            if ($responseData['StatusCode'] === 200 && $responseData['Message'] === 'Applicant Successfully Fetched') {
                return true;
            } elseif ($responseData['StatusCode'] === 404 && $responseData['Message'] === 'Applicant Record Not Found') {
                return false;
            }
        }
    }

    public static function actionPushDisbursement($obj)
    {
        $postFields = json_encode([
            "CNIC" => $obj['CNIC'],
            "FirstDisbursementDate" => $obj['FirstDisbursementDate'],
            "NoOfInstallments" => $obj['NoOfInstallments'],
            "MonthlyInstallmentAmount" => $obj['MonthlyInstallmentAmount'],
            "FirstDueDate" => $obj['FirstDueDate'],
            "SecondDisbursementDate" => $obj['SecondDisbursementDate']
        ]);
        

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'http://mis.akhuwat.org.pk:4969/api/pitb/update-disbursement',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postFields,
            CURLOPT_HTTPHEADER => array(
                'X-API-Key: 123456789',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        if ($response === false) {
            echo 'Curl error: ' . curl_error($curl);
        } else {
            echo 'Response: ' . $response;
        }

        curl_close($curl);
        return true;
    }

}