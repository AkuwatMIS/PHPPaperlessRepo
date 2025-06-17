<?php

namespace common\components\Helpers;

use common\models\Applications;
use common\models\CibTypes;
use common\models\Loans;
use common\models\Members;
use common\models\MembersPhone;

class PmypHelper
{

    public function actionReasonStatus($ref)
    {
        $array = [
            '1' => "Front End Screening",
            '2' => "Credit History Check",
            '3' => "Physical Verification",
            '4' => "Additional Documentation Requirement",
            '5' => "Credit Evaluation",
            '6' => "Risk Approval",
            '7' => "Pending for Customer Acceptance",
            '8' => "FOL Issuance and Signing In Process",
            '9' => "Charge Documents Completion and Signing In Process",
            '10' => "Mortgage Formalities In Process",
            '11' => "DAC Issuance In Process",
            '12' => "Account Opening In Process",
            '13' => "Disbursement Under Process",
            '14' => "Disbursed",
            '15' => "Rejected due to Non-Submission of Required Documentation After 14 days Notice",
            '16' => "Rejected during Front End Screening",
            '17' => "Rejected at Credit History Check",
            '18' => "Rejected at Physical Verification",
            '19' => "Rejected at Credit Evaluation",
            '20' => "Rejected at Scorecard",
            '21' => "Rejected due to Non-Eligibility",
            '22' => "Rejected due to Non-Completetion of Pre Disbursement Formalities After Approval",
            '23' => "Rejected due to Refusal to accept offer after Approval",
            '24' => "Rejected at FATF/NADRA/CPA Screening",
            '25' => "Applicant Not Responding",
            '26' => "Repaid",
            '29' => "Closed due to revision in scheme features"
        ];
    }

    public static function parseRequestBody($nic)
    {
        $array = [];
        $member = Members::find()->where(['cnic' => $nic])->one();
        if (!empty($member) && $member != null) {
            $application = Applications::find()->where(['member_id' => $member->id])
                ->andWhere(['in', 'project_id', [105, 106]])
                ->one();
            if (!empty($application) && $application != null) {
                $loan = Loans::find()->where(['application_id' => $application->id])->one();
                if (!empty($loan) && $loan != null) {
                    $disbursed_amount = ($loan->disbursed_amount > 0) ? $loan->disbursed_amount : 0;
                    $disbursed_date = ($loan->disbursed_amount>0)?date('Y-m-d', $loan->date_disbursed):0;
                    if ($loan->status == 'pending') {
                        $statusReason = 9;
                    } elseif ($loan->status == 'rejected') {
                        $statusReason = 16;
                    } elseif ($loan->disbursed_amount > 0 && $loan->status == 'collected'){
                        $statusReason = 14;
                    }
                } else {
                    $in_process_date = ($application->status == 'pending') ? date('Y-m-d', $application->updated_at) : 0;
                    $rejected_date = ($application->status == 'rejected') ? date('Y-m-d', $application->updated_at) : 0;
                    $disbursed_amount = 0;
                    $disbursed_date = 0;
                    if ($application->status == 'pending') {
                        $statusReason = 1;
                    } elseif ($application->status == 'rejected') {
                        $statusReason = 16;
                    }
                }

                $array = [
                    "CNIC" => (isset($member->cnic) && !empty($member->cnic)) ? str_replace('-', '', $member->cnic) : $nic,
                    "Status" => $application->status,
                    "Details" => 'NA',
                    "ApprovedAmount" => (!empty($application->recommended_amount)) ? $application->recommended_amount : $application->req_amount,
                    "IsAmountDsibursed" => ($disbursed_amount > 0) ? "True" : "False",
                    "StatusReasonId" => $statusReason,
                    "DisbursedAmount" => $disbursed_amount,
                    "ApprovedDate" => ($application->status == 'approved') ? date('Y-m-d', $application->updated_at) : 0, // date format  "2021-07-31"
                    "DisbursedDate" => $disbursed_date,
                    "InProcessDate" => $in_process_date,
                    "RejectedDate" => $rejected_date,
                    "NoOfJobs" => 1
                ];
            }
        }
        return $array;
    }

}