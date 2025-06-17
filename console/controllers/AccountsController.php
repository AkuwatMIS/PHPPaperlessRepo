<?php


namespace console\controllers;


use common\components\Helpers\BankaccountsHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\models\DisbursementDetails;
use common\models\DynamicReports;
use common\models\FilesAccounts;
use common\models\Lists;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\Members;
use common\models\MembersAccount;
use common\models\MembersPhone;
use Yii;
use common\models\LoanActions;
use yii\helpers\ArrayHelper;
use yii\console\Controller;

class AccountsController extends Controller
{
// php /var/www/paperless_web/yii accounts/update-account-no

    public function actionUpdateAccountNo()
    {
        $banks = [];
        $banksModel = Lists::find()->where(['list_name' => 'bank_accounts'])->select(['value'])->all();
        foreach ($banksModel as $key => $bank) {
            $banks[$key] = $bank->value;
        }

        ini_set('memory_limit', '204878M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $dynamic_reports = DynamicReports::find()->where(['status' => 0, 'deleted' => 0])->andWhere(['in', 'report_defination_id', [16, 23]])->all();
        foreach ($dynamic_reports as $report) {
            $errors = [];
            $file_path = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'account' . '/' . $report->uploaded_file;
            $myfile = fopen($file_path, "r");
            $flag = false;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    $member = Members::find()->where(['cnic' => $fileop[1]])->one();
                    if (!empty($member)) {
                        $loan_check = LoanTranches::find()
                            ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                            ->join('inner join', 'applications', 'applications.id=loans.application_id')
                            ->join('inner join', 'members', 'members.id=applications.member_id')
                            ->filterWhere(['=', 'members.cnic', $member->cnic])
                            ->andFilterWhere(['=', 'applications.status', 'approved'])
                            ->andFilterWhere(['applications.deleted' => 0])
                            ->andFilterWhere(['not in', 'loans.status', ['loan completed', 'not collected']])
                            //->andFilterWhere(['!=','loan_tranches.status',6])
                            ->andFilterWhere(['loans.deleted' => 0])
                            ->one();
                        $update = true;
                        if (!empty($loan_check)) {
                            if (in_array($loan_check->loan->status, ['collected'])) {
                                $errors[$member->cnic]['cnic'] = $member->cnic;
                                $errors[$member->cnic]['reason'] = 'loan is already disbursed';
                                $update = false;
                            } elseif ($loan_check->status == 6) {
                                $errors[$member->cnic]['cnic'] = $member->cnic;
                                $errors[$member->cnic]['reason'] = 'loan is already disbursed';
                                $update = false;
                            } elseif ($loan_check->status == 8) {
                                $errors[$member->cnic]['cnic'] = $member->cnic;
                                $errors[$member->cnic]['reason'] = 'loan is already published';
                                $update = false;
                            }
//                            elseif (isset($loan_check->publish) && !empty($loan_check->publish)) {
//                                $errors[$member->cnic]['cnic'] = $member->cnic;
//                                $errors[$member->cnic]['reason'] = 'loan is already published';
//                                $update=false;
//                            }
                        }
                        if (!in_array($fileop[5], $banks)) {
                            $errors[$member->cnic]['cnic'] = $member->cnic;
                            $errors[$member->cnic]['reason'] = 'Bank Name is invalid';
                            $update = false;
                        }
                        if ($update) {
                            //$mobile = MembersPhone::find()->where(['member_id' => $member->id, 'is_current' => 1, 'phone_type' => 'mobile'])->one();
                            $accounts = MembersAccount::find()->where(['member_id' => $member->id])->all();
                            foreach ($accounts as $acc) {
                                $acc->is_current = 0;
                                $acc->updated_by = $report->created_by;
                                if (!$acc->save(false)) {
                                    print_r($acc->getErrors());
                                    die();
                                }
                            }
                            $account = new MembersAccount();
                            $account->member_id = $member->id;
                            if (isset($fileop[5]) && !empty($fileop[5])) {
                                $account->bank_name = $fileop[5];
                                $account->account_type = $fileop[4];
                            } else {
                                // $account->bank_name='Mobile';
                            }
                            $account->title = trim($fileop[2]);
                            $account->account_no = trim(str_replace("'", "", $fileop[3]));
                            $account->is_current = 1;
                            $account->created_by = $report->created_by;
                            $account->assigned_to = $report->created_by;
                            if (!$account->save()) {
                                $errors[$member->cnic]['cnic'] = $member->cnic;
                                $errors[$member->cnic]['reason'] = $account->getErrors();
                            } else {
                                $member->full_name = $fileop[2];
                                if ($member->save(false)) {
                                    $user_id = $report->created_by;
                                    $verification_action = LoanActions::find()->where(['parent_id' => $loan_check->loan_id, 'action' => 'account_verification'])->one();

                                    if (isset($verification_action) && !empty($verification_action)) {
                                        $verification_action->status = 0;
                                        $verification_action->user_id = $user_id;
                                        $verification_action->updated_by = $user_id;
                                        $verification_action->expiry_date = 0;
                                        if (!$verification_action->save()) {
                                            print_r($verification_action->getErrors());
                                            die();
                                        }
                                    }
                                } else {
                                    $errors[$member->cnic]['cnic'] = $member->cnic;
                                    $errors[$member->cnic]['reason'] = $member->getErrors();
                                }
                            }
                        }
                    }
                }
                $flag = true;
            }
            if (!empty($errors)) {
                $file_name = 'accounts_errors_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . 'account' . '/' . $file_name;
                $fopen = fopen($file_path, 'w');
                fputcsv($fopen, ['cnic', 'reason']);
                foreach ($errors as $row) {
                    fputcsv($fopen, $row);
                }
                fclose($fopen);
                $report->file_path = $file_name;
            }
            $report->status = 1;
            $report->save();
        }
    }

    public function actionUpdateAccountNoPublished()
    {
        $banks = [];
        $banksModel = Lists::find()->where(['list_name' => 'bank_accounts'])->select(['value'])->all();
        foreach ($banksModel as $key => $bank) {
            $banks[$key] = $bank->value;
        }

        ini_set('memory_limit', '204878M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $dynamic_reports = DynamicReports::find()->where(['status' => 0, 'deleted' => 0])->andWhere(['in', 'report_defination_id', [18]])->all();
        foreach ($dynamic_reports as $report) {
            $errors = [];
            $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . 'account' . '/' . $report->uploaded_file;
            $myfile = fopen($file_path, "r");
            $flag = false;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if ($flag) {
                    $member = Members::find()->where(['cnic' => $fileop[1]])->one();
                    if (!empty($member)) {
                        if (!in_array($fileop[4], $banks)) {
                            $errors[$member->cnic]['cnic'] = $member->cnic;
                            $errors[$member->cnic]['reason'] = 'Bank Name is invalid';
                            $update = false;
                        } else {
                            $loan_check = LoanTranches::find()
                                ->join('inner join', 'loans', 'loans.id=loan_tranches.loan_id')
                                ->join('inner join', 'applications', 'applications.id=loans.application_id')
                                ->join('inner join', 'members', 'members.id=applications.member_id')
                                ->andFilterWhere(['=', 'members.cnic', $member->cnic])
                                ->andFilterWhere(['=', 'applications.status', 'approved'])
                                ->andFilterWhere(['applications.deleted' => 0])
                                ->andFilterWhere(['not in', 'loans.status', ['collected', 'loan completed', 'not collected']])
                                ->andFilterWhere(['!=', 'loan_tranches.status', 6])
                                ->andFilterWhere(['loans.deleted' => 0])
                                ->one();
                            if (!empty($loan_check)) {
                                $publish = DisbursementDetails::find()->where(['tranche_id' => $loan_check->id, 'deleted' => 0])->andWhere(['!=', 'status', 3])->one();
                                if (!empty($publish)) {
                                    $accounts = MembersAccount::find()->where(['member_id' => $member->id])->all();
                                    foreach ($accounts as $acc) {
                                        $acc->is_current = 0;
                                        $acc->updated_by = 1;
                                        if (!$acc->save()) {
                                            print_r($acc->getErrors());
                                            die();
                                        }
                                    }
                                    $account = new MembersAccount();
                                    $account->member_id = $member->id;
                                    if (isset($fileop[4]) && !empty($fileop[4])) {
                                        $account->bank_name = $fileop[4];
                                    } else {
                                        $account->bank_name = 'Mobile';
                                    }
                                    $account->title = trim($fileop[2]);
                                    $account->account_no = trim(str_replace("'", "", $fileop[3]));
                                    $account->is_current = 1;
                                    $account->created_by = 1;
                                    $account->assigned_to = 1;
                                    if (!$account->save()) {
                                        print_r($account->getErrors());
                                        die();
                                    }
                                    $publish->bank_name = $account->bank_name;
                                    $publish->account_no = $account->account_no;
                                    $publish->updated_by = 1;
                                    $publish->save();
                                }
                            } else {
                                $errors[$member->cnic]['cnic'] = $member->cnic;
                                $errors[$member->cnic]['reason'] = 'Loan Not Found';

                            }
                        }
                    }
                }
                $flag = true;
            }
            if (!empty($errors)) {
                $file_name = 'accounts_errors_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . 'account' . '/' . $file_name;
                $fopen = fopen($file_path, 'w');
                fputcsv($fopen, ['cnic', 'reason']);
                foreach ($errors as $row) {
                    fputcsv($fopen, $row);
                }
                fclose($fopen);
                $report->file_path = $file_name;
            }
            $report->status = 1;
            $report->save();
        }
    }
}