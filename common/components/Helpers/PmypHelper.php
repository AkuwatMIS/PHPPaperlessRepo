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

    public static function parseRequestBody($id,$loan)
    {
        $array = [];
        $application = Applications::find()->where(['id' => $loan->application_id])->one();
        if (!empty($application) && $application != null) {
            $member = Members::find()->where(['id' => $application->member_id])->one();
            if (!empty($member) && $member != null) {
                $in_process_date = date('Y-m-d', $loan->created_at);
                $rejected_date   = ($loan->status == 'rejected') ? date('Y-m-d', $loan->updated_at) : '';
                $disbursed_date  = ($loan->disbursed_amount > 0) ? date('Y-m-d', $loan->date_disbursed) : '';
                $ApprovedDate    = date('Y-m-d', $loan->date_approved);

                $disbursed_amount = ($loan->disbursed_amount > 0) ? $loan->disbursed_amount : 0;
                $approved_amount  = $loan->loan_amount;

                if ($loan->status == 'pending' || $loan->status == 'not collected') {
                    $details = 'Charge Documents Completion and Signing In Process';
                    $statusReason = 9;
                    $status = 1;
                } elseif ($loan->status == 'rejected') {
                    $details = 'Rejected during Front End Screening';
                    $statusReason = 16;
                    $status = 4;
                } elseif ($loan->disbursed_amount > 0 && $loan->status == 'collected') {
                    $details = 'Disbursed';
                    $statusReason = 14;
                    $status = 3;
                } elseif ($loan->status == 'loan completed') {
                    $details = 'Repaid';
                    $statusReason = 26;
                    $status = 5;
                }

                $array = [
                    "id" => $id,
                    "CNIC" => (!empty($member->cnic) && $member->cnic!=null) ? str_replace('-', '', $member->cnic) : '',
                    "Status" => "$status",
                    "Details" => "$details",
                    "ApprovedAmount" => $approved_amount,
                    "IsAmountDsibursed" => ($disbursed_amount > 0) ? 1 : 0,
                    "StatusReasonId" => "$statusReason",
                    "DisbursedAmount" => $disbursed_amount,
                    "ApprovedDate" => "$ApprovedDate", // date format  "2021-07-31"
                    "DisbursedDate" => "$disbursed_date",
                    "InProcessDate" => "$in_process_date",
                    "RejectedDate" => "$rejected_date",
                    "NoOfJobs" => 1
                ];
            }
        }

        return $array;
    }

}