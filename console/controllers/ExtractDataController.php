<?php

namespace console\controllers;

use common\components\Helpers\CalculationHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\KamyabPakistanHelper;
use common\components\Helpers\StructureHelper;
use common\models\Actions;
use common\models\ApplicationActions;
use common\models\ApplicationDetails;
use common\models\Applications;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Awp;
use common\models\AwpOverdue;
use common\models\Branches;
use common\models\BranchProjectsMapping;
use common\models\Donations;
use common\models\DynamicReports;
use common\models\Guarantors;
use common\models\LoanActions;
use common\models\Loans;
use common\models\MemberInfo;
use common\models\Members;
use common\models\Products;
use common\models\ProgressReports;
use common\models\ProjectsAgriculture;
use common\models\ProjectsAgricultureKpp;
use common\models\ProjectsKpp;
use common\models\Recoveries;
use common\models\Regions;
use common\models\Users;
use frontend\modules\branch\Branch;
use Ratchet\App;
use Yii;
use yii\web\NotFoundHttpException;
use yii\console\Controller;


class ExtractDataController extends Controller
{
// nohup php yii extract-data/member                done
// nohup php yii extract-data/applications          done
// nohup php yii extract-data/loans                 done
// nohup php yii extract-data/recoveries            done
// nohup php yii extract-data/schedules             done
// nohup php yii extract-data/donations             done
// nohup php yii extract-data/appraisals-business   done
// nohup php yii extract-data/appraisals-social     done
// nohup php yii extract-data/appraisals-agri       done
// nohup php yii extract-data/member-address        done
// nohup php yii extract-data/member-phone          done

    public function actionMember()
    {
        $loopArray = [1, 2];

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        foreach ($loopArray as $loop) {
            if ($loop == 1) {
                $fromDate = 1530403200; //$fromJul012018
                $toDate = 1593543600; //$toJun302020
                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/members-ul012018-Jun302020.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'members-ul012018-Jun302020.csv');

            } else {
                $fromDate = 1593561600;  //$fromJul012020
                $toDate = 1640977200;  //$toDec312021

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/members-Jul012020-Dec312021.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'members-Jul012020-Dec312021.csv');
            }

            $createColumn = array("id", "region_id", "area_id", "branch_id", "district_id", "full_name", "parentage", "cnic", "gender", "dob", "education", "marital_status", "family_no", "religion");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                b.id,
                b.region_id,
                b.area_id,
                b.branch_id,
                c.district_id,
                b.full_name,
                b.parentage,
                b.cnic,
                b.gender,
                b.dob,
                b.education,
                b.marital_status,
                b.religion
            FROM
                loans l
            INNER JOIN
                applications app
            ON
                app.id = l.application_id
            INNER JOIN
                members b
            ON
                b.id = app.member_id
            INNER JOIN
                branches c
            ON
                c.id = app.branch_id
            WHERE
                l.date_disbursed BETWEEN $fromDate AND $toDate AND l.status IN(
                    'loan completed',
                    'collected'
                ) AND l.project_id NOT IN (52,61,62,64,67,76,77,83,90) GROUP BY b.cnic";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount) && $recoveryCount != null) {
                foreach ($recoveryCount as $d) {
                    fputcsv($fopenW, $d);
                }


            }
        }

    }


    public function actionApplications()
    {
        $loopArray = [1, 2];

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        foreach ($loopArray as $loop) {
            if ($loop == 1) {
                $fromDate = 1530403200; //$fromJul012018
                $toDate = 1593543600; //$toJun302020

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/applications-Jul012018-Jun302020.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'applications-Jul012018-Jun302020.csv');

            } else {
                $fromDate = 1593561600;  //$fromJul012020
                $toDate = 1640977200;  //$toDec312021

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/applications-Jul012020-Dec312021.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'applications-Jul012020-Dec312021.csv');
            }
            $createColumn = array("id", "region_id", "area_id", "branch_id", "Member Id", "application_no", "fee", "application_date", "Project name", "activity_name", "req_amount");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                    a.id application_id,
                    a.region_id,
                    a.area_id,
                    a.branch_id,
                    b.id member_id,
                    a.application_no,
                    a.fee,
                    a.application_date,
                    p.name project_name,
                    ac.name activity_name,
                    a.req_amount
                FROM
                    loans l
                INNER JOIN
                    applications a
                ON
                    a.id = l.application_id
                INNER JOIN
                    members b
                ON
                    b.id = a.member_id
                INNER JOIN
                    branches c
                ON
                    c.id = a.branch_id
                INNER JOIN
                    activities ac
                ON
                    a.activity_id = ac.id
                INNER JOIN
                    projects p
                ON
                    a.project_id = p.id
                WHERE
                    l.date_disbursed BETWEEN $fromDate AND $toDate AND l.status IN(
                        'loan completed',
                        'collected'
                    ) AND l.project_id NOT IN (52,61,62,64,67,76,77,83,90)
                ";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount) && $recoveryCount != null) {
                foreach ($recoveryCount as $d) {
                    fputcsv($fopenW, $d);
                }

            }

        }


    }

    public function actionLoans()
    {
        $loopArray = [1, 2];

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        foreach ($loopArray as $loop) {
            if ($loop == 1) {
                $fromDate = 1530403200; //$fromJul012018
                $toDate = 1593543600; //$toJun302020

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/loans-Jul012018-Jun302020.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'loans-Jul012018-Jun302020.csv');

            } else {
                $fromDate = 1593561600;  //$fromJul012020
                $toDate = 1640977200;  //$toDec312021

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/loans-Jul012020-Dec312021.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'loans-Jul012020-Dec312021.csv');
            }

            $createColumn = array("loan_id", "application_id", "sanction_no", "project_name",
                "date_approved", "loan_amount", 'disbursed_amount', 'cheque_no', "inst_amnt", "inst_months", "inst_type"
            , "date_disbursed", "loan_expiry", "Loan status");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                a.id loan_id,
                a.application_id,
                a.sanction_no,
                p.name project_name,
                a.date_approved,
                a.loan_amount,
                a.disbursed_amount,
                a.cheque_no,
                a.inst_amnt,
                a.inst_months,
                a.inst_type,
                a.date_disbursed,
                a.loan_expiry,
                a.status
            FROM
                loans a
            INNER JOIN
                applications b
            ON
                b.id = a.application_id
            INNER JOIN
                members c
            ON
                c.id = b.member_id
            INNER JOIN
                branches d
            ON
                d.id = b.branch_id
            INNER JOIN
                projects p
            ON
                a.project_id = p.id
            WHERE
                a.date_disbursed BETWEEN $fromDate AND $toDate AND a.status IN(
                    'loan completed',
                    'collected'
                ) AND a.project_id NOT IN(52,61,62,64,67,76,77,83,90)
            GROUP BY
                a.sanction_no
            ";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount) && $recoveryCount != null) {
                foreach ($recoveryCount as $d) {
                    fputcsv($fopenW, $d);
                }

            }
        }


    }

    public function actionDonations($month)
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        $fromDate = date('Y-m-01', strtotime($month));
        $toDate = date('Y-m-t', strtotime($month));

        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/donations/donations-' . $fromDate . '-' . $toDate . '.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'complete_data_extract/donations/donations-' . $fromDate . '-' . $toDate . '.csv');

        $createColumn = array("schedule id", "loan id", "donation_amount", "Receive Date");
        fputcsv($fopenW, $createColumn);

        $fromDateStr = strtotime($fromDate);
        $toDateStr = strtotime($toDate);

        $db = Yii::$app->db;
        $loan_query = "SELECT
                don.schedule_id,
                don.loan_id,
                don.amount,
                don.receive_date
            FROM
                donations don
            INNER JOIN
                loans b
            ON
                b.id = don.loan_id
            WHERE
            (b.date_disbursed BETWEEN 1530403200 AND 1640933999) AND b.status IN('loan completed', 'collected') 
                AND b.project_id NOT IN(52,61,62,64,67,76,77,83,90) AND don.deleted=0 AND don.receive_date BETWEEN $fromDateStr AND $toDateStr
                ";
        $recoveryCount = $db->createCommand($loan_query)->queryAll();
        if (!empty($recoveryCount) && $recoveryCount != null) {
            foreach ($recoveryCount as $d) {
                fputcsv($fopenW, $d);
            }
        }
    }

    public function actionRecoveries($month)
    {
        $fromDate = date('Y-m-01', strtotime($month));
        $toDate = date('Y-m-t', strtotime($month));

        $fromDateStr = strtotime($fromDate);
        $toDateStr = strtotime($toDate);

        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/recoveries/recoveries-' . $fromDate . '-' . $toDate . '.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'recoveries-' . $fromDate . '-' . $toDate . '.csv');

        $createColumn = array("schedule id", "loan id", "amount", "receive_date");
        fputcsv($fopenW, $createColumn);

        $db = Yii::$app->db;
        $loan_query = "SELECT
                a.schedule_id,
                a.loan_id,
                a.amount,
                a.receive_date
            FROM
                recoveries a
            INNER JOIN
                loans b
            ON
                b.id = a.loan_id       
            WHERE
                (b.date_disbursed BETWEEN 1530403200 AND 1640933999) AND b.status IN('loan completed', 'collected') 
                AND b.project_id NOT IN(52,61,62,64,67,76,77,83,90) AND a.deleted=0 AND a.receive_date BETWEEN $fromDateStr AND $toDateStr
        ";
        $recoveryCount = $db->createCommand($loan_query)->queryAll();
        if (!empty($recoveryCount) && $recoveryCount != null) {
            foreach ($recoveryCount as $d) {
                fputcsv($fopenW, $d);
            }


        }


    }

    public function actionSchedules($month)
    {

        $fromDate = date('Y-m-01', strtotime($month));
        $toDate = date('Y-m-t', strtotime($month));
        $fromDateStr = strtotime($fromDate);
        $toDateStr = strtotime($toDate);

        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/schedules/schedules-' . $fromDate . '-' . $toDate . '.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'schedules-' . $fromDate . '-' . $toDate . '.csv');


        $createColumn = array("schedules id", "loan id", "due_date", "schdl_amnt", "overdue"
        , "advance", 'due_amnt', "schedule credit");
        fputcsv($fopenW, $createColumn);

        $db = Yii::$app->db;
        $loan_query = "SELECT
                schedules.id,
                schedules.loan_id,
                schedules.due_date,
                schedules.schdl_amnt,
                schedules.overdue,
                schedules.advance,
                schedules.due_amnt,
                schedules.credit
            FROM
                schedules
            INNER JOIN
                loans
            ON
                loans.id = schedules.loan_id          
            WHERE
                loans.date_disbursed BETWEEN $fromDateStr AND $toDateStr AND loans.status IN(
                    'loan completed',
                    'collected'
                ) AND loans.project_id NOT IN(52,61,62,64,67,76,77,83,90)
            ";
        $recoveryCount = $db->createCommand($loan_query)->queryAll();
        if (!empty($recoveryCount) && $recoveryCount != null) {
            foreach ($recoveryCount as $d) {
                fputcsv($fopenW, $d);
            }


        }

    }

    public function actionAppraisalsBusiness()
    {
        $loopArray = [1, 2];

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        foreach ($loopArray as $loop) {
            if ($loop == 1) {
                $fromDate = 1530403200; //$fromJul012018
                $toDate = 1593543600; //$toJun302020

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/appraisals_business-Jul012018-Jun302020.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'appraisals_business-Jul012018-Jun302020.csv');

            } else {
                $fromDate = 1593561600;  //$fromJul012020
                $toDate = 1640977200;  //$toDec312021

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/appraisals_business-Jul012020-Dec312021.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'appraisals_business-Jul012020-Dec312021.csv');
            }

            $createColumn = array("application_id", "place_of_business", "fixed_business_assets_amount",
                "running_capital", "running_capital_amount", "business_expenses",
                "business_expenses_amount", "new_required_assets", "new_required_assets_amount",
                "who_are_customers");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                appraisals_business.application_id,
                appraisals_business.place_of_business,
                appraisals_business.fixed_business_assets_amount,
                appraisals_business.running_capital,
                appraisals_business.running_capital_amount,
                appraisals_business.business_expenses,
                appraisals_business.business_expenses_amount,
                appraisals_business.new_required_assets,
                appraisals_business.new_required_assets_amount,
                appraisals_business.who_are_customers
            FROM
                appraisals_business
            INNER JOIN
                applications
            ON
                applications.id = appraisals_business.application_id
            INNER JOIN
                loans
            ON
                loans.application_id = applications.id
            INNER JOIN
                members
            ON
                members.id = applications.member_id
            INNER JOIN
                branches
            ON
                branches.id = applications.branch_id
            WHERE
                loans.date_disbursed BETWEEN $fromDate AND $toDate AND loans.status IN('loan completed', 'collected') 
                AND loans.project_id NOT IN(52,61,62,64,67,76,77,83,90)
                ";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount) && $recoveryCount != null) {
                foreach ($recoveryCount as $d) {
                    fputcsv($fopenW, $d);
                }


            }

        }


    }

    public function actionAppraisalsSocial()
    {
        $loopArray = [1, 2];

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        foreach ($loopArray as $loop) {
            if ($loop == 1) {
                $fromDate = 1530403200; //$fromJul012018
                $toDate = 1593543600; //$toJun302020

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/appraisals_social-Jul012018-Jun302020.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'appraisals_social-Jul012018-Jun302020.csv');

            } else {
                $fromDate = 1593561600;  //$fromJul012020
                $toDate = 1640977200;  //$toDec312021

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/appraisals_social-Jul012020-Dec312021.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'appraisals_social-Jul012020-Dec312021.csv');
            }

            $createColumn = array("application_id", "poverty_index", "house_ownership", "house_rent_amount",
                "land_size", "total_family_members", "ladies", "gents", "source_of_income", "total_household_income",
                "utility_bills", "educational_expenses", "medical_expenses", "kitchen_expenses", "monthly_savings", "month_saving_amount", "other_expenses"
            , "total_expenses", "economic_dealings", "social_behaviour", "fatal_disease", "house_condition", "description");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                appraisals_social.application_id,
                appraisals_social.poverty_index,
                appraisals_social.house_ownership,
                appraisals_social.house_rent_amount,
                appraisals_social.land_size,
                appraisals_social.total_family_members,
                appraisals_social.ladies,
                appraisals_social.gents,
                appraisals_social.source_of_income,
                appraisals_social.total_household_income,
                appraisals_social.utility_bills,
                appraisals_social.educational_expenses,
                appraisals_social.medical_expenses,
                appraisals_social.kitchen_expenses,
                appraisals_social.monthly_savings,
                appraisals_social.amount,
                appraisals_social.other_expenses,
                appraisals_social.total_expenses,
                appraisals_social.economic_dealings,
                appraisals_social.social_behaviour,
                appraisals_social.fatal_disease,
                appraisals_social.house_condition,
                appraisals_social.description
            FROM
                appraisals_social
            INNER JOIN
                applications
            ON
                applications.id = appraisals_social.application_id
            INNER JOIN
                loans
            ON
                loans.application_id = applications.id
            INNER JOIN
                members
            ON
                members.id = applications.member_id
            INNER JOIN
                branches
            ON
                branches.id = applications.branch_id
            WHERE
                loans.date_disbursed BETWEEN $fromDate AND $toDate AND loans.status IN('loan completed', 'collected') 
                AND loans.project_id NOT IN(52,61,62,64,67,76,77,83,90)
            ";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount) && $recoveryCount != null) {
                foreach ($recoveryCount as $d) {
                    fputcsv($fopenW, $d);
                }


            }
        }


    }

    public function actionAppraisalsAgri()
    {
        $loopArray = [1, 2];

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        foreach ($loopArray as $loop) {
            if ($loop == 1) {
                $fromDate = 1530403200; //$fromJul012018
                $toDate = 1593543600; //$toJun302020

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/appraisals_agriculture-Jul012018-Jun302020.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'appraisals_agriculture-Jul012018-Jun302020.csv');

            } else {
                $fromDate = 1593561600;  //$fromJul012020
                $toDate = 1640977200;  //$toDec312021

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/appraisals_agriculture-Jul012020-Dec312021.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'appraisals_agriculture-Jul012020-Dec312021.csv');
            }

            $createColumn = array("application_id", "water_analysis", "soil_analysis", "laser_level",
                "irrigation_source", "crop_year", "crop_production", "resources", "expenses", "available_resources", "required_resources"
            );
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                appraisals_agriculture.application_id,
                appraisals_agriculture.water_analysis,
                appraisals_agriculture.soil_analysis,
                appraisals_agriculture.laser_level,
                appraisals_agriculture.irrigation_source,
                appraisals_agriculture.crop_year,
                appraisals_agriculture.crop_production,
                appraisals_agriculture.resources,
                appraisals_agriculture.expenses,
                appraisals_agriculture.available_resources,
                appraisals_agriculture.required_resources
            FROM
                appraisals_agriculture
            INNER JOIN
                applications
            ON
                applications.id = appraisals_agriculture.application_id
            INNER JOIN
                loans
            ON
                loans.application_id = applications.id
            INNER JOIN
                members
            ON
                members.id = applications.member_id
            INNER JOIN
                branches
            ON
                branches.id = applications.branch_id
            WHERE
                loans.date_disbursed BETWEEN $fromDate AND $toDate AND loans.status IN('loan completed', 'collected') 
                AND loans.project_id NOT IN(52,61,62,64,67,76,77,83,90)
            ";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount) && $recoveryCount != null) {
                foreach ($recoveryCount as $d) {
                    fputcsv($fopenW, $d);
                }


            }
        }


    }

    public function actionMemberAddress()
    {
        $loopArray = [1, 2];
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        foreach ($loopArray as $loop) {
            if ($loop == 1) {
                $fromDate = 1530403200; //$fromJul012018
                $toDate = 1593543600; //$toJun302020

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/member_address-Jul012018-Jun302020.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'member_address-Jul012018-Jun302020.csv');

            } else {
                $fromDate = 1593561600;  //$fromJul012020
                $toDate = 1640977200;  //$toDec312021

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/member_address-Jul012020-Dec312021.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'member_address-Jul012020-Dec312021.csv');
            }


            $createColumn = array("member id", "address");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                    members_address.member_id,
                    members_address.address
                FROM
                    members_address
                INNER JOIN
                    members
                ON
                    members.id = members_address.member_id
                INNER JOIN
                    applications
                ON
                    applications.member_id = members.id
                INNER JOIN
                    loans
                ON
                    loans.application_id = applications.id
                WHERE
                    loans.date_disbursed BETWEEN $fromDate AND $toDate AND loans.status IN('loan completed', 'collected') 
                    AND loans.project_id NOT IN(52,61,62,64,67,76,77,83,90)
                GROUP BY
                    members.id";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount) && $recoveryCount != null) {
                foreach ($recoveryCount as $d) {
                    fputcsv($fopenW, $d);
                }


            }
        }


    }

    public function actionMemberPhone()
    {
        $loopArray = [1, 2];

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);
        foreach ($loopArray as $loop) {
            if ($loop == 1) {
                $fromDate = 1530403200; //$fromJul012018
                $toDate = 1593543600; //$toJun302020

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/members_phone-Jul012018-Jun302020.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'members_phone-Jul012018-Jun302020.csv');

            } else {
                $fromDate = 1593561600;  //$fromJul012020
                $toDate = 1640977200;  //$toDec312021

                $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/members_phone-Jul012020-Dec312021.csv';
                $fopenW = fopen($filepathW, 'w');
                header('Content-type: application/csv');
                header('Content-Disposition: attachment; filename=' . 'members_phone-Jul012020-Dec312021.csv');
            }
            $createColumn = array("member id", "phone");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                        members_phone.member_id,
                        members_phone.phone
                    FROM
                        `members_phone`
                    INNER JOIN
                        members
                    ON
                        members.id = members_phone.member_id
                    INNER JOIN
                        applications
                    ON
                        applications.member_id = members.id
                    INNER JOIN
                        loans
                    ON
                        loans.application_id = applications.id
                    WHERE
                        loans.date_disbursed BETWEEN $fromDate AND $toDate AND loans.status IN('loan completed', 'collected') 
                        AND loans.project_id NOT IN(52,61,62,64,67,76,77,83,90)
                    GROUP BY
                        members.id";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount) && $recoveryCount != null) {
                foreach ($recoveryCount as $d) {
                    fputcsv($fopenW, $d);
                }


            }


        }


    }

//================================================================================================================

//   extract-data/add-acc-verify
    public function actionAddAccVerify()
    {
        $sanctions = [];

        foreach ($sanctions as $s) {
            $db = Yii::$app->db;
            $loan_query = "SELECT * FROM loans 
              WHERE sanction_no='$s'";
            $LoanData = $db->createCommand($loan_query)->queryAll();
            if (!empty($LoanData) && $LoanData != null) {
                foreach ($LoanData as $loan) {
                    $acc_action = new LoanActions();
                    $acc_action->parent_id = $loan['id'];
                    $acc_action->user_id = $loan['updated_by'];
                    $acc_action->action = 'account_verification';
                    $acc_action->status = 0;
                    $acc_action->pre_action = 0;
                    $acc_action->expiry_date = 0;
                    $acc_action->created_by = $loan['updated_by'];
                    $acc_action->updated_by = 0;
                    if ($acc_action->save()) {

                    } else {
                        var_dump($acc_action->getErrors());
                        die();
                    }
                }

            }
        }
    }

// extract-data/member-recovery
    public function actionMemberRecovery()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);


        $dateArray = ['2021-01', '2021-02', '2021-03', '2021-04', '2021-05', '2021-06', '2021-07', '2021-08', '2021-09', '2021-10', '2021-11', '2021-12'];

        foreach ($dateArray as $date) {
            $from = date('Y-m-01', strtotime($date));
            $to = date('Y-m-t', strtotime($date));
            $fromData = strtotime($from);
            $toData = strtotime($to);

            $filepathW = ImageHelper::getAttachmentPath() . 'member_recovery/member_recovery' . $from . '-' . $to . '.csv';
            $fopenW = fopen($filepathW, 'w');
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename=' . 'member_recovery/member_recovery' . $from . '-' . $to . '.csv');

            $createColumn = array("Sanction", "Recovery", "receive_date");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT  
            loans.sanction_no,
            (recoveries.amount+recoveries.charges_amount) as recovery_amount,
            FROM_UNIXTIME(recoveries.receive_date) receive_date
                FROM
                    recoveries
                INNER JOIN
                    loans
                ON
                    loans.id = recoveries.loan_id
                WHERE
            recoveries.receive_date BETWEEN $fromData AND $toData 
            AND recoveries.deleted=0";

            $LoanData = $db->createCommand($loan_query)->queryAll();
            if (!empty($LoanData) && $LoanData != null) {
                foreach ($LoanData as $key => $d) {
                    fputcsv($fopenW, $d);

                }

            }
        }


    }

// extract-data/get-due-list 2022-02
    public function actionGetDueList($date)
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 15000);

        $paramsDate = $date;

        $schedule_date = strtotime(date('Y-m-d-23:59', (strtotime($paramsDate . '+ 9 days'))));
        $disbursed_date = strtotime(date("Y-m-d-23:59", $schedule_date) . " -1 months");
        $recovery_date = strtotime(date('Y-m-t-23:59', (strtotime($paramsDate . '- 1 months'))));
        $contactType = 'Mobile';

        $filepathW = ImageHelper::getAttachmentPath() . 'member_recovery/due-list.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'member_recovery/due-list.csv');
        $createColumn = array("member_sync_id", "application_no", "sanction_no", "inst_amnt", "project_id", "full_name", "cnic", "parentage", "loan_amount", "disbursed_amount", "region_id", "area_id", "branch_id", "date_disbursed", "branch_code", "phone", "grp_no", "schdl_till_current_month", "recovery", "due", "olp", "team");
        fputcsv($fopenW, $createColumn);

        $sql = 'SELECT
                    `loans`.`id` as `member_sync_id`,
                    `applications`.`application_no`,
                    `loans`.`sanction_no`,
                    `loans`.`inst_amnt`,
                    `loans`.`project_id`,
                    `members`.`full_name` AS `full_name`,
                    `members`.`cnic` AS `cnic`,
                    `members`.`parentage` AS `parentage`,
                    `loans`.`loan_amount`,
                    @amountapproved:=`loans`.`disbursed_amount` AS `disbursed_amount`,
                    `applications`.`region_id`,
                    `applications`.`area_id`,
                    `applications`.`branch_id`,
                    FROM_UNIXTIME(`loans`.`date_disbursed`) AS `date_disbursed`,
                    (
                    SELECT
                        code
                    FROM
                        branches
                    WHERE
                        id = applications.branch_id

                ) AS `branch_code`,
                    (
                    SELECT
                        phone
                    FROM
                        members_phone
                    WHERE
                        is_current = 1 AND member_id = members.id AND phone_type=\'Mobile\'
                    ORDER BY
                        id
                    DESC
                LIMIT 1
                ) AS `phone`, `groups`.`grp_no` AS `grp_no`, @schdl_till_current_month :=(
                SELECT
                  (sum(schedules.schdl_amnt)+sum(schedules.charges_schdl_amount))
                FROM
                    schedules
                WHERE
                    (
                        schedules.loan_id = loans.id and schedules.due_date<=' . $schedule_date . '
                    )
                ) AS `schdl_till_current_month`,
                @recovery :=(
                SELECT 
                   coalesce((sum(recoveries.amount)+sum(recoveries.charges_amount)),0)
                FROM
                    recoveries
                WHERE
                    ( 
                        recoveries.loan_id = loans.id and recoveries.deleted=0 and recoveries.receive_date<=' . $recovery_date . '
                    )
                ) AS `recovery`,
                (
                   IF(( @schdl_till_current_month - @recovery )>0,
                      ( IF(( @schdl_till_current_month - @recovery )<(`loans`.`inst_amnt`) AND  ((@amountapproved - @recovery)>(`loans`.`inst_amnt`)), (`loans`.`inst_amnt`) , (@schdl_till_current_month - @recovery) ) ), `loans`.`inst_amnt`) ) AS `due`,
                (
                    (
                        (@amountapproved-@recovery)
                    )
                ) AS `olp`,
                `teams`.`name` As team
                FROM
                    `loan_tranches`
                LEFT JOIN
                    `loans`
                ON
                    `loan_tranches`.`loan_id` = `loans`.`id`
                LEFT JOIN
                    `applications`
                ON
                    `loans`.`application_id` = `applications`.`id`
                LEFT JOIN
                    `members`
                ON
                    `applications`.`member_id` = `members`.`id`
                LEFT JOIN
                    `groups`
                ON
                    `applications`.`group_id` = `groups`.`id`
                LEFT JOIN
                    `teams`
                ON
                    `applications`.`team_id` = `teams`.`id`
                WHERE (`loans`.`project_id` NOT IN(52,61,62,67,76)) AND
                    (`loans`.`deleted` = 0) AND(
                        `loan_tranches`.`date_disbursed`<=' . $disbursed_date . '
                    ) AND(`loans`.`status` = \'collected\') AND(`loan_tranches`.`status` = 6)  GROUP BY `loans`.`id`';

        $queryData = Yii::$app->db->createCommand($sql);
        $branchActiveLoans = $queryData->queryAll();
        if (!empty($branchActiveLoans) && $branchActiveLoans != null) {
            foreach ($branchActiveLoans as $key => $d) {
                fputcsv($fopenW, $d);

            }

        }

    }

//===================================================================================
//    kpp and funding source reports export in /home/datadisk/paperless_attachments/kpp_reports
//     Start reports
//====================================================

//   nohup php yii extract-data/kpp-schedule-kisan-data
    public function actionKppScheduleKisanData()
    {
        ini_set('memory_limit', '4048M');
        ini_set('max_execution_time', 2000);
        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/kpp-schedules-kppKisan-data-79.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/kpp-schedules-kppKisan-data-79.csv');

        $createColumn = array("Region", "Area", "Branch", "Project", "Batch NO", "Sanction Number",
            "Tranche No", "CNIC", "Loan Amount", "Disbursed Amount", "Loan Period in years",
            "Date of Disbursement", "No of Installment", "Installment_date",
            "Installment_amount");

        fputcsv($fopenW, $createColumn);

        $db = Yii::$app->db;
        $loan_query = "SELECT a.id,d.name region_name,c.name area_name,b.name branch_name,e.name project_name,pfd.batch_no,
                    a.sanction_no,lt.tranch_no,mem.cnic,a.loan_amount,a.disbursed_amount,
                    ROUND(a.inst_months/12, 2) loan_period_years ,FROM_UNIXTIME(a.date_disbursed) disbursement_date,
                    ROUND(a.inst_months,2) no_of_installment
                    FROM loans a
                    INNER JOIN loan_tranches lt on lt.loan_id=a.id
                    INNER JOIN project_fund_detail pfd ON pfd.id=lt.batch_id
                    INNER JOIN applications app on app.id=a.application_id
                    INNER JOIN members mem on mem.id=app.member_id
                    INNER JOIN branches b on b.id=a.branch_id
                    INNER JOIN areas c on c.id=b.area_id
                    INNER JOIN regions d on d.id=b.region_id
                    INNER JOIN projects e on e.id=a.project_id
                    WHERE e.id=79 GROUP BY a.sanction_no";
//                    WHERE e.id=79 AND a.date_disbursed <= 1642921199 GROUP BY a.sanction_no
        $LoanData = $db->createCommand($loan_query)->queryAll();
        if (!empty($LoanData) && $LoanData != null) {
            $resultArray = [];
            foreach ($LoanData as $key => $d) {
                $loanId = $d['id'];
                $schedule_query = "SELECT FROM_UNIXTIME(schedules.due_date) sch_date, schdl_amnt FROM schedules WHERE loan_id=$loanId AND due_date=1646870400";
                $scheduleData = $db->createCommand($schedule_query)->queryAll();

                if (!empty($scheduleData)) {
                    foreach ($scheduleData as $keySchdl => $schdl) {
                        $resultArray[$key][$keySchdl]['region'] = $d['region_name'];
                        $resultArray[$key][$keySchdl]['area'] = $d['area_name'];
                        $resultArray[$key][$keySchdl]['branch'] = $d['branch_name'];
                        $resultArray[$key][$keySchdl]['project'] = $d['project_name'];
                        $resultArray[$key][$keySchdl]['batch_id'] = $d['batch_no'];
                        $resultArray[$key][$keySchdl]['sanction_number'] = $d['sanction_no'];
                        $resultArray[$key][$keySchdl]['tranche_no'] = $d['tranch_no'];
                        $resultArray[$key][$keySchdl]['cnic'] = $d['cnic'];
                        $resultArray[$key][$keySchdl]['loan_amount'] = $d['loan_amount'];
                        $resultArray[$key][$keySchdl]['disbursed_amount'] = $d['disbursed_amount'];
                        $resultArray[$key][$keySchdl]['loan_period_years'] = $d['loan_period_years'];
                        $resultArray[$key][$keySchdl]['date_of_disbursement'] = $d['disbursement_date'];
                        $resultArray[$key][$keySchdl]['no_of_installment'] = $d['no_of_installment'];
                        $resultArray[$key][$keySchdl]['sch_date'] = $schdl['sch_date'];
                        $resultArray[$key][$keySchdl]['sch_amount'] = $schdl['schdl_amnt'];
                    }
                }
            }

            foreach ($resultArray as $data) {
                foreach ($data as $d) {
                    fputcsv($fopenW, $d);
                }
            }


        }

    }

//    nohup php yii extract-data/kpp-schedule-karobar-data
    public function actionKppScheduleKarobarData()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);
        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/kpp-schedules-karobar-data-78.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/kpp-schedules-karobar-data-78.csv');

        $createColumn = array("Region", "Area", "Branch", "Project", "Batch NO", "Sanction Number",
            "Tranche No", "CNIC", "Loan Amount", "Disbursed Amount", "Loan Period in years",
            "Date of Disbursement", "No of Installment", "Installment_date",
            "Installment_amount");

        fputcsv($fopenW, $createColumn);

        $db = Yii::$app->db;
        $loan_query = "SELECT a.id,d.name region_name,c.name area_name,b.name branch_name,e.name project_name,pfd.batch_no,
                    a.sanction_no,lt.tranch_no,mem.cnic,a.loan_amount,a.disbursed_amount,
                    ROUND(a.inst_months/12, 2) loan_period_years ,FROM_UNIXTIME(a.date_disbursed) disbursement_date,
                    ROUND(a.inst_months,2) no_of_installment
                    FROM loans a
                    INNER JOIN loan_tranches lt on lt.loan_id=a.id
                    INNER JOIN project_fund_detail pfd ON pfd.id=lt.batch_id
                    INNER JOIN applications app on app.id=a.application_id
                    INNER JOIN members mem on mem.id=app.member_id
                    INNER JOIN branches b on b.id=a.branch_id
                    INNER JOIN areas c on c.id=b.area_id
                    INNER JOIN regions d on d.id=b.region_id
                    INNER JOIN projects e on e.id=a.project_id
                    WHERE e.id=78 GROUP BY a.sanction_no";
//                    WHERE e.id=78 AND a.date_disbursed <= 1642921199 GROUP BY a.sanction_no
        $LoanData = $db->createCommand($loan_query)->queryAll();
        if (!empty($LoanData) && $LoanData != null) {
            $resultArray = [];
            foreach ($LoanData as $key => $d) {
                $loanId = $d['id'];
                $schedule_query = "SELECT FROM_UNIXTIME(schedules.due_date) sch_date, schdl_amnt FROM schedules WHERE loan_id=$loanId AND due_date=1646870400";
                $scheduleData = $db->createCommand($schedule_query)->queryAll();
                if (!empty($scheduleData)) {
                    foreach ($scheduleData as $keySchdl => $schdl) {
                        $resultArray[$key][$keySchdl]['region'] = $d['region_name'];
                        $resultArray[$key][$keySchdl]['area'] = $d['area_name'];
                        $resultArray[$key][$keySchdl]['branch'] = $d['branch_name'];
                        $resultArray[$key][$keySchdl]['project'] = $d['project_name'];
                        $resultArray[$key][$keySchdl]['batch_id'] = $d['batch_no'];
                        $resultArray[$key][$keySchdl]['sanction_number'] = $d['sanction_no'];
                        $resultArray[$key][$keySchdl]['tranche_no'] = $d['tranch_no'];
                        $resultArray[$key][$keySchdl]['cnic'] = $d['cnic'];
                        $resultArray[$key][$keySchdl]['loan_amount'] = $d['loan_amount'];
                        $resultArray[$key][$keySchdl]['disbursed_amount'] = $d['disbursed_amount'];
                        $resultArray[$key][$keySchdl]['loan_period_years'] = $d['loan_period_years'];
                        $resultArray[$key][$keySchdl]['date_of_disbursement'] = $d['disbursement_date'];
                        $resultArray[$key][$keySchdl]['no_of_installment'] = $d['no_of_installment'];
                        $resultArray[$key][$keySchdl]['sch_date'] = $schdl['sch_date'];
                        $resultArray[$key][$keySchdl]['sch_amount'] = $schdl['schdl_amnt'];
                    }
                }

            }

            foreach ($resultArray as $data) {
                foreach ($data as $d) {
                    fputcsv($fopenW, $d);
                }
            }
        }

    }


//    nohup php yii extract-data/kpp-housing-schedule-data
    public function actionKppHousingScheduleData()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);
        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/kpp-schedules-housing-data-77.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/kpp-schedules-housing-data-77.csv');

        $createColumn = array("Region", "Area", "Branch", "Project", "Batch NO", "Sanction Number",
            "Tranche No", "CNIC", "Member Name", "Loan Amount", "Disbursed Amount", "Tranche Amount", "Loan Period in years",
            "Date of Disbursement", "No of Installment",
            "Installment_date", "Installment_amount", "OLP");

        fputcsv($fopenW, $createColumn);

        $db = Yii::$app->db;
        $loan_query = "SELECT a.id,d.name region_name,c.name area_name,b.name branch_name,e.name project_name,pfd.batch_no,
                    a.sanction_no,lt.tranch_no,mem.cnic,mem.full_name,a.loan_amount,a.disbursed_amount,lt.tranch_amount,
                    ROUND(a.inst_months/12, 2) loan_period_years ,FROM_UNIXTIME(a.date_disbursed) disbursement_date,
                    ROUND(a.inst_months,2) no_of_installment
                    FROM loans a
                    INNER JOIN loan_tranches lt on lt.loan_id=a.id
                    INNER JOIN project_fund_detail pfd ON pfd.id=lt.batch_id
                    INNER JOIN applications app on app.id=a.application_id
                    INNER JOIN members mem on mem.id=app.member_id
                    INNER JOIN branches b on b.id=a.branch_id
                    INNER JOIN areas c on c.id=b.area_id
                    INNER JOIN regions d on d.id=b.region_id
                    INNER JOIN projects e on e.id=a.project_id
                    WHERE e.id=77 GROUP BY a.sanction_no";
//                    WHERE e.id=77 AND a.date_disbursed <= 1642921199 GROUP BY a.sanction_no
        $LoanData = $db->createCommand($loan_query)->queryAll();
        if (!empty($LoanData) && $LoanData != null) {
            $resultArray = [];
            foreach ($LoanData as $key => $d) {
                $loanData = Loans::find()->where(['id' => $d['id']])->one();
                if (!empty($loanData)) {
                    $loanId = $d['id'];
                    $schedule_query = "SELECT FROM_UNIXTIME(schedules.due_date) sch_date, schdl_amnt FROM schedules WHERE loan_id=$loanId AND due_date>=1654066799 AND due_date<=1657522799";
                    $scheduleData = $db->createCommand($schedule_query)->queryAll();

                    if (!empty($scheduleData)) {
                        foreach ($scheduleData as $keySchdl => $schdl) {
                            $resultArray[$key][$keySchdl]['region'] = $d['region_name'];
                            $resultArray[$key][$keySchdl]['area'] = $d['area_name'];
                            $resultArray[$key][$keySchdl]['branch'] = $d['branch_name'];
                            $resultArray[$key][$keySchdl]['project'] = $d['project_name'];
                            $resultArray[$key][$keySchdl]['batch_id'] = $d['batch_no'];
                            $resultArray[$key][$keySchdl]['sanction_number'] = $d['sanction_no'];
                            $resultArray[$key][$keySchdl]['tranche_no'] = $d['tranch_no'];
                            $resultArray[$key][$keySchdl]['cnic'] = $d['cnic'];
                            $resultArray[$key][$keySchdl]['full_name'] = $d['full_name'];
                            $resultArray[$key][$keySchdl]['loan_amount'] = $d['loan_amount'];
                            $resultArray[$key][$keySchdl]['disbursed_amount'] = $d['disbursed_amount'];
                            $resultArray[$key][$keySchdl]['tranche_amount'] = $d['tranch_amount'];
                            $resultArray[$key][$keySchdl]['loan_period_years'] = $d['loan_period_years'];
                            $resultArray[$key][$keySchdl]['date_of_disbursement'] = $d['disbursement_date'];
                            $resultArray[$key][$keySchdl]['no_of_installment'] = $d['no_of_installment'];
                            $resultArray[$key][$keySchdl]['sch_date'] = $schdl['sch_date'];
                            $resultArray[$key][$keySchdl]['sch_amount'] = $schdl['schdl_amnt'];
                        }
                    }
                }

            }

            foreach ($resultArray as $data) {
                foreach ($data as $d) {
                    fputcsv($fopenW, $d);
                }
            }


        }

    }


//  extract-data/batch-wise-fund-report
    public function actionBatchWiseFundReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/batch_wise_funding_amount.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/batch_wise_funding_amount.csv');
        $createColumn = array("Batch Number", "Fundling Line", "PROJECT", "Date of request", "No Of Loans", "Amount requested", "Date of fund received", "Transaction MODE", "Transaction Number", "Amount received");
        fputcsv($fopenW, $createColumn);

        $sql = 'SELECT
                a.batch_no,
                b.name as funding_line,
                c.name AS project,
                FROM_UNIXTIME(a.allocation_date) date_of_request,
                a.no_of_loans,
                a.fund_batch_amount amount_requested,
                FROM_UNIXTIME(d.received_at) date_of_fund_received,
                d.txn_mode transaction_mode,
                d.txn_no transaction_no,
                a.fund_batch_amount amount_received
            FROM
                project_fund_detail a
            INNER JOIN
                funds b
            ON
                b.id = a.fund_id
            INNER JOIN
                projects c
            ON
                c.id = a.project_id
            INNER JOIN
                transactions d
            ON
                d.parent_id = a.id AND d.parent_table = \'project_fund_detail\'';

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();
        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                fputcsv($fopenW, $d);

            }

        }

    }


// extract-data/batch-wise-disbursement-report
    public function actionBatchWiseDisbursementReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/batch_wise_disbursement_portfolio.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/batch_wise_funding_amount.csv');
        $createColumn = array("Sanction No", "Name", "CNIC", "Project", "Batch No", "Date of Disbursement as per Bank Statement", "Loan Amount", "Disbursed Amount", "Bank Fund Receiving Date");
        fputcsv($fopenW, $createColumn);

        $sql = 'SELECT
                lon.sanction_no,
                mem.full_name,
                mem.cnic,
                c.name AS project,
                a.batch_no,
                if(dd.bank_disb_date>0,FROM_UNIXTIME(dd.bank_disb_date),0) AS bank_date_of_disb,
                lon.loan_amount,
                lon.disbursed_amount,
                FROM_UNIXTIME(d.received_at) banks_fund_receiving_date
            FROM
                project_fund_detail a
            INNER JOIN
                loan_tranches lt
            ON
                lt.batch_id = a.id
            INNER JOIN
                disbursement_details dd
            ON
                dd.tranche_id = lt.id
            INNER JOIN
                loans lon
            ON
                lon.id = lt.loan_id
            INNER JOIN
                applications app
            ON
                app.id = lon.application_id
            INNER JOIN
                members mem
            ON
                mem.id = app.member_id
            INNER JOIN
                projects c
            ON
                c.id = a.project_id
            INNER JOIN
                transactions d
            ON
                d.parent_id = a.id AND d.parent_table = \'project_fund_detail\'';

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();
        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                fputcsv($fopenW, $d);

            }

        }

    }


// extract-data/loan-financing-detail-report
    public function actionLoanFinancingDetailReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/loan_financing_detail.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/loan_financing_detail.csv');
        $createColumn = array("Sanction No", "Name", "CNIC", "Project", "Batch No", "Loan Amount", "Total Disbursed Amount", "Total Recovered Amount", "OLP");
        fputcsv($fopenW, $createColumn);

        $sql = 'SELECT
                    lon.sanction_no,
                    mem.full_name,
                    mem.cnic,
                    c.name AS project,
                    (SELECT GROUP_CONCAT(pfd.batch_no) FROM loan_tranches ltr INNER JOIN project_fund_detail pfd ON pfd.id=ltr.batch_id WHERE ltr.loan_id=lon.id GROUP BY ltr.loan_id ) as batch_no,
                    lon.loan_amount,
                    lon.disbursed_amount as total_disb_amount,
                    @total_rec_amount :=(SELECT if(SUM(rec.amount)>0,SUM(rec.amount),0) FROM recoveries rec WHERE rec.loan_id=lon.id AND rec.deleted=0 ) as total_rec_amount,
                    (lon.loan_amount-@total_rec_amount) as olp
                FROM
                    project_fund_detail a
                INNER JOIN
                    loan_tranches lt
                ON
                    lt.batch_id = a.id
                INNER JOIN
                    disbursement_details dd
                ON
                    dd.tranche_id = lt.id
                INNER JOIN
                    loans lon
                ON
                    lon.id = lt.loan_id
                INNER JOIN
                    applications app
                ON
                    app.id = lon.application_id
                INNER JOIN
                    members mem
                ON
                    mem.id = app.member_id
                INNER JOIN
                    projects c
                ON
                    c.id = a.project_id GROUP BY lon.sanction_no';

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();
        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                fputcsv($fopenW, $d);

            }

        }

    }

// extract-data/loan-recovery-detail-report
    public function actionLoanRecoveryDetailReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/loan_recovery_detail.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/loan_recovery_detail.csv');
        $createColumn = array("Sanction No", "Name", "CNIC", "Project", "Batch No", "Date of Recovery", "Recovery Amount");
        fputcsv($fopenW, $createColumn);

        $sql = 'SELECT
                lon.sanction_no,
                mem.full_name,
                mem.cnic,
                c.name AS project,
                a.batch_no,
                rec.receive_date date_of_recovery,
                (rec.amount + rec.charges_amount) AS recovery_mount
            FROM
                project_fund_detail a
            INNER JOIN
                loan_tranches lt
            ON
                lt.batch_id = a.id
            INNER JOIN
                loans lon
            ON
                lon.id = lt.loan_id
            INNER JOIN
                applications app
            ON
                app.id = lon.application_id
            INNER JOIN
                members mem
            ON
                mem.id = app.member_id
            INNER JOIN
                projects c
            ON
                c.id = a.project_id
            INNER JOIN
                recoveries rec
            ON
                rec.loan_id = lon.id
            WHERE
                rec.deleted = 0 GROUP BY rec.id';

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();

        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                fputcsv($fopenW, $d);

            }

        }

    }

// =================================================
//   End report
// =====================================================================


//  extract-data/secp-report
    public function actionSecpReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/secp_report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/secp_report.csv');
        $createColumn = array("CNIC", "Sanction No", "Name", "Parentage", "Marital Status", "Education", "Date Of Birth", "Gender", "Address", "Phone");
        fputcsv($fopenW, $createColumn);

        $sql = "SELECT
            c.cnic,
            a.sanction_no,
            c.full_name,
            c.parentage,
            c.marital_status,
            c.education,
            c.dob,
            c.gender,
            md.address,
            mp.phone
        FROM
            loans a
        INNER JOIN
            applications b
        ON
            b.id = a.application_id AND b.deleted=0
        INNER JOIN
            members c
        ON
            c.id = b.member_id
        LEFT JOIN
            members_address md
        ON
            md.member_id = c.id AND md.is_current = 1
        LEFT JOIN
            members_phone mp
        ON
            mp.member_id = c.id AND mp.is_current = 1
        WHERE
            a.deleted=0 AND
           a.sanction_no IN ('3117-D002-08102','45401-D025-00004')
        GROUP BY
            a.sanction_no";

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();

        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                fputcsv($fopenW, $d);

            }

        }

    }

//  extract-data/secp-member-report
    public function actionSecpMemberReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/secp_member_report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/secp_member_report.csv');
        $createColumn = array("CNIC", "Name");
        fputcsv($fopenW, $createColumn);

        $sql = "SELECT
            c.cnic,
            c.name
        FROM
        
            blacklist c
        
        WHERE
           c.cnic IN (
            '55402-6536527-7',
            '55401-6744504-1',
            '56503-6956545-3',
            '56503-0487214-1',
            '36603-1682853-9',
            '36302-6590156-9',
            '38520-2535638-9',
            '37401-0268667-9',
            '32402-8433218-9',
            '61101-1857451-3'
        ) GROUP BY c.cnic";

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();

        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                fputcsv($fopenW, $d);

            }

        }

    }

//  php yii extract-data/sehat-card
    public function actionSehatCard()
    {
        $projects = [77, 78, 79];
        $file_path = ImageHelper::getAttachmentPath() . 'kpp_reports/app_questions_upload.csv';
        $myfile = fopen($file_path, "r");
        while (($fileop = fgetcsv($myfile)) !== false) {
            $application = Applications::find()
                ->join('inner join', 'members', 'members.id=applications.member_id')
                ->where(['in', 'applications.project_id', $projects])
                ->andWhere(['members.cnic' => $fileop[0]])
                ->one();

            if (!empty($application) && $application != null) {
                $application_id = $application->id;
                $training_required = (isset($fileop[1]) && !empty($fileop[1])) ? $fileop[1] : 0;
                $trainee_type = (isset($fileop[2]) && !empty($fileop[2])) ? $fileop[2] : '';
                $trainee_name = (isset($fileop[3]) && !empty($fileop[3])) ? $fileop[3] : '';
                $trainee_guardian = (isset($fileop[4]) && !empty($fileop[4])) ? $fileop[4] : '';
                $trainee_cnic = (isset($fileop[5]) && !empty($fileop[5])) ? $fileop[5] : '';
                $trainee_relation = (isset($fileop[6]) && !empty($fileop[6])) ? $fileop[6] : '';
                $has_sehat_card = (isset($fileop[7]) && !empty($fileop[7])) ? $fileop[7] : 0;
                $want_sehat_card = (isset($fileop[8]) && !empty($fileop[8])) ? $fileop[8] : 0;

                if ($application->project_id == 79) {
                    $existingModel = ProjectsAgricultureKpp::find()->where(['application_id' => $application_id])->one();
                    if (empty($existingModel) && $existingModel == null) {
                        $agriPreviousData = ProjectsAgriculture::find()->where(['application_id' => $application_id])->one();
                        $model = new ProjectsAgricultureKpp();
                        $model->application_id = $application_id;
                        if (!empty($agriPreviousData) && $agriPreviousData != null) {
                            $model->loan_id = (isset($agriPreviousData->loan_id) && !empty($agriPreviousData->loan_id)) ? $agriPreviousData->loan_id : 0;
                            $model->kpp_owner = $agriPreviousData->owner;
                            $model->kpp_land_area_size = $agriPreviousData->land_area_size;
                            $model->kpp_land_area_type = $agriPreviousData->land_area_type;
                            $model->kpp_village_name = $agriPreviousData->village_name;
                            $model->kpp_uc_number = $agriPreviousData->uc_number;
                            $model->kpp_uc_name = $agriPreviousData->uc_name;
                            $model->kpp_crop_type = $agriPreviousData->crop_type;
                            $model->kpp_crops = $agriPreviousData->crops;
                        }
                        $model->kpp_training_required = $training_required;
                        $model->kpp_trainee_type = $trainee_type;
                        $model->kpp_trainee_name = $trainee_name;
                        $model->kpp_trainee_guardian = $trainee_guardian;
                        $model->kpp_trainee_cnic = $trainee_cnic;
                        $model->kpp_trainee_relation = $trainee_relation;
                        $model->kpp_has_sehat_card = $has_sehat_card;
                        $model->kpp_want_sehat_card = $want_sehat_card;
                        $model->created_by = 1;
                        $model->updated_by = 1;
                        if ($model->save()) {
                            echo 'saved in ProjectsAgricultureKpp';
                        }
                    }
                } else {
                    $existingModel = ProjectsKpp::find()->where(['application_id' => $application_id])->one();
                    if (empty($existingModel) && $existingModel == null) {
                        $model = new ProjectsKpp();
                        $model->application_id = $application_id;
                        $model->training_required = $training_required;
                        $model->trainee_type = $trainee_type;
                        $model->trainee_name = $trainee_name;
                        $model->trainee_guardian = $trainee_guardian;
                        $model->trainee_cnic = $trainee_cnic;
                        $model->trainee_relation = $trainee_relation;
                        $model->has_sehat_card = $has_sehat_card;
                        $model->want_sehat_card = $want_sehat_card;
                        $model->created_by = 1;
                        $model->updated_by = 1;
                        if ($model->save()) {
                            echo 'saved in ProjectsKpp';
                        }
                    }
                }
            }
        }
    }

//  php yii extract-data/secp-recoveries
    public function actionSecpRecoveries()
    {
        $fromDate = date('Y-m-01', strtotime('2020-07'));
        $toDate = date('Y-m-t', strtotime('2021-12'));

        $fromDateStr = strtotime($fromDate);
        $toDateStr = strtotime($toDate);

        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/recoveries/recoveries-secp' . $fromDate . '-' . $toDate . '.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'recoveries-secp' . $fromDate . '-' . $toDate . '.csv');

        $createColumn = array("Product Name", "Loan Count", "Recovery amount");
        fputcsv($fopenW, $createColumn);
        $resultArray = [];
        $products = Products::find()->where(['status' => 1])->all();

        foreach ($products as $key => $product) {
            $pId = $product->id;
            $db = Yii::$app->db;
            $count_loan_query = "SELECT
                COUNT(b.id) loan_count
            FROM
                loans b
            WHERE
                b.date_disbursed BETWEEN $fromDateStr AND $toDateStr 
                AND b.status IN('loan completed', 'collected')
                AND b.product_id=$pId
                AND b.deleted=0
        ";
            $loanCount = $db->createCommand($count_loan_query)->queryAll();

            $sum_recovery_query = "SELECT
                SUM(a.amount) rec_sum
            FROM
                recoveries a
            INNER JOIN
                loans b
            ON
                b.id = a.loan_id
            WHERE
                a.receive_date BETWEEN $fromDateStr AND $toDateStr 
                AND b.status IN('loan completed', 'collected')
                AND b.product_id=$pId
                AND b.deleted=0
                AND a.deleted=0
        ";
            $recoverySum = $db->createCommand($sum_recovery_query)->queryAll();


            $resultArray[$key]['product_name'] = $product->name;
            $resultArray[$key]['loan_count'] = $loanCount[0]['loan_count'];
            $resultArray[$key]['recovery_sum'] = $recoverySum[0]['rec_sum'];


        }

        if (!empty($resultArray) && $resultArray != null) {
            foreach ($resultArray as $d) {
                fputcsv($fopenW, $d);
                print_r($d);
                echo '<---->';
            }


        }


    }



// ================================================
//   BOP Report start
// =================================================


//    php yii extract-data/bop-karobar-report
    public function actionBopKarobarReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/bop_karobar_report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/bop_karobar_report.csv');
        $createColumn = array("Serial No", "Name", "Address", "District", "Province", "Cnic", "Registered with NSER (Yes/No)",
            "PMT No", "Sehat Insaaf Card Status (Yes/No)", "Loan Amount", "Disbursement Date", "Outstanding Loan Balance", "Loan Tenor",
            "Repayment date  of next instalment", "Repayment amount of next instalment", "Overdue amount", "Customer rate",
            "Subsidy rate", "Subsidy Amount", "Active Borrower of Akhuwat(Yes/No)", "CIB clearance (Yes/No)", "Group Life- Takaful/Insurance (Yes/No)");
        fputcsv($fopenW, $createColumn);

        $sql = "SELECT
                    c.full_name AS name,
                    (
                    SELECT
                        address
                    FROM
                        members_address
                    WHERE
                        is_current = 1 AND member_id = c.id AND address_type = \"home\"
                    LIMIT 1
                    ) AS address, drt.name district, pr.name province, c.cnic, apd.poverty_score, pkpp.has_sehat_card, a.loan_amount, FROM_UNIXTIME(a.date_disbursed) disbursement_date,a.balance, a.inst_months,
                    (
                    SELECT
                        FROM_UNIXTIME(scd.due_date)
                    FROM
                        schedules scd
                    WHERE
                        scd.due_date = 1649548800 AND scd.loan_id = a.id
                    ) repayment_date_next_schdule,
                    (
                    SELECT
                        scdl.schdl_amnt
                    FROM
                        schedules scdl
                    WHERE
                        scdl.due_date = 1649548800 AND scdl.loan_id = a.id
                    ) repayment_amount_next_schdule,
                    (
                    SELECT
                        scdl.overdue_log
                    FROM
                        schedules scdl
                    WHERE
                        scdl.due_date = 1646870400 AND scdl.loan_id = a.id
                    ) overdue_amount
                    FROM
                        loans a
                    INNER JOIN
                        applications b
                    ON
                        b.id = a.application_id
                    INNER JOIN
                        members c
                    ON
                        c.id = b.member_id
                    INNER JOIN
                        projects prj
                    ON
                        prj.id = a.project_id
                    LEFT JOIN
                        projects_kpp pkpp
                    ON
                        pkpp.application_id = b.id
                    LEFT JOIN
                        application_details apd
                    ON
                        apd.application_id = a.application_id AND apd.parent_type = 'member'
                    INNER JOIN
                        branches br
                    ON
                        br.id = a.branch_id
                    LEFT JOIN
                        provinces pr
                    ON
                        pr.id = br.province_id
                    LEFT JOIN
                        districts drt
                    ON
                        drt.id = br.district_id
                    WHERE
                        a.project_id = 78 AND a.status in ('collected','loan completed')";

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();

        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                $dataArray = [];
                $dataArray['Serial_No'] = $key;
                $dataArray['Name'] = $d['name'];
                $dataArray['Address'] = $d['address'];
                $dataArray['District'] = $d['district'];
                $dataArray['Province'] = $d['province'];
                $dataArray['Cnic'] = $d['cnic'];
                $dataArray['Registered_with_NSER'] = 'Yes';
                $dataArray['PMT_No'] = $d['poverty_score'];
                $dataArray['Sehat_Insaaf_Card_Status'] = ($d['has_sehat_card'] == 1) ? "Yes" : "No";
                $dataArray['Loan_amount'] = $d['loan_amount'];
                $dataArray['Disbursement_Date'] = $d['disbursement_date'];
                $dataArray['Outstanding_Loan_Balance'] = $d['balance'];
                $dataArray['Loan_Tenor'] = $d['inst_months'];
                $dataArray['Repayment_date_of_next_instalment'] = $d['repayment_date_next_schdule'];
                $dataArray['Repayment_amount_of_next_instalment'] = $d['repayment_amount_next_schdule'];
                $dataArray['Overdue_amount'] = $d['overdue_amount'];
                $dataArray['Customer_rate'] = 'NA';
                $dataArray['Subsidy_rate'] = 'NA';
                $dataArray['Subsidy_Amount'] = 'NA';
                $dataArray['Active_Borrower_of_Akhuwat'] = 'NO';
                $dataArray['CIB_clearance'] = 'Yes';
                $dataArray['Group_Life_Takaful'] = 'Yes';

                fputcsv($fopenW, $dataArray);

            }

        }

    }

//    php yii extract-data/bop-kisan-report
    public function actionBopKisanReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/bop_kisan_report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/bop_kisan_report.csv');
        $createColumn = array("Serial No", "Name", "Address", "District", "Province", "Cnic", "Registered with NSER (Yes/No)",
            "PMT No", "Sehat Insaaf Card Status (Yes/No)", "Loan Type", "Loan Amount - Farm Inputs (Max. 150,000)", "Loan Amount - Mach & Equipmt (Max. 200,000)",
            "Land Holding /Tenant  (Max, 12.5 acres)", "Other loans", "Disbursement date", "Outstanding Loan Balance", "Loan tenore ( Max. 1 year)",
            "Due Date", "Repayment date", "Repayment amount", "Overdue Amount", "Customer rate", "Subsidiary rate", "Subs. Amount", "Active Borrower of Akhuwat(Yes/No)",
            "CIB clearance (Yes/No)", "Group Life- Takaful/Insurance (Yes/No)");
        fputcsv($fopenW, $createColumn);

        $sql = "SELECT
                c.full_name AS name,
                (
                SELECT
                    address
                FROM
                    members_address
                WHERE
                    is_current = 1 AND member_id = c.id AND address_type = \"home\"
                LIMIT 1
            ) AS addess, drt.name zilla, pr.name province, c.cnic, apd.poverty_score,(
                IF(
                    pkpp.has_sehat_card != NULL,
                    pkpp.has_sehat_card,
                    0
                )
            ) AS has_sehat_card, IF(prj.name != '', \"Agriculture\", \"\") loan_type, a.loan_amount,(
            SELECT
                agrikpp.kpp_land_area_size
            FROM
                projects_agriculture_kpp agrikpp
            WHERE
                agrikpp.application_id = b.id
            ) land_holding,
            FROM_UNIXTIME(a.date_disbursed) date_disb,
            IF(
                a.balance > 0,
                a.balance,
                a.disbursed_amount
            ) outstanding_loan_balance,
            IF(a.inst_type != '', \"6 Months\", \"\") loan_tenore,
            (
            SELECT
                FROM_UNIXTIME(scd.due_date)
            FROM
                schedules scd
            WHERE
                scd.loan_id = a.id
            ) due_date,
            (
            SELECT
                FROM_UNIXTIME(scd.due_date)
            FROM
                schedules scd
            WHERE
                scd.loan_id = a.id
            ) repayment_date,
            (
            SELECT
                scdl.schdl_amnt
            FROM
                schedules scdl
            WHERE
                scdl.loan_id = a.id
            ) repayment_amount
            FROM
                loans a
            INNER JOIN
                applications b
            ON
                b.id = a.application_id
            INNER JOIN
                members c
            ON
                c.id = b.member_id
            INNER JOIN
                projects prj
            ON
                prj.id = a.project_id
            LEFT JOIN
                projects_kpp pkpp
            ON
                pkpp.application_id = b.id
            LEFT JOIN
                application_details apd
            ON
                apd.application_id = a.application_id AND apd.parent_type = 'member'
            INNER JOIN
                branches br
            ON
                br.id = a.branch_id
            LEFT JOIN
                provinces pr
            ON
                pr.id = br.province_id
            LEFT JOIN
                districts drt
            ON
                drt.id = br.district_id
            WHERE
                a.project_id = 79 AND a.status in ('collected','loan completed')";

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();

        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                $dataArray = [];
                $dataArray['Serial_No'] = $key;
                $dataArray['Name'] = $d['name'];
                $dataArray['Address'] = $d['addess'];
                $dataArray['District'] = $d['district'];
                $dataArray['Province'] = $d['province'];
                $dataArray['Cnic'] = $d['cnic'];
                $dataArray['Registered_with_NSER'] = 'Yes';
                $dataArray['PMT_No'] = $d['poverty_score'];
                $dataArray['Sehat_Insaaf_Card_Status'] = ($d['has_sehat_card'] == 1) ? "Yes" : "No";
                $dataArray['Loan_Type'] = $d['loan_type'];
                $dataArray['Loan_Amount'] = $d['loan_amount'];
                $dataArray['Loan_Amount_Mach_Equipmt'] = "NA";
                $dataArray['Land_Holding_Tenant'] = $d['land_holding'];
                $dataArray['Other_loans'] = "NA";
                $dataArray['Disbursement_date'] = $d['date_disb'];
                $dataArray['Outstanding_Loan_Balance'] = $d['outstanding_loan_balance'];
                $dataArray['Loan_tenore'] = $d['loan_tenore'];
                $dataArray['Due_Date'] = $d['due_date'];
                $dataArray['Repayment_date'] = $d['repayment_date'];
                $dataArray['Repayment_amount'] = $d['repayment_amount'];
                $dataArray['Overdue_Amount'] = 0;
                $dataArray['Customer_rate'] = 'NA';
                $dataArray['Subsidy_rate'] = 'NA';
                $dataArray['Subsidy_Amount'] = 'NA';
                $dataArray['Active_Borrower_of_Akhuwat'] = 'NO';
                $dataArray['CIB_clearance'] = 'Yes';
                $dataArray['Group_Life_Takaful'] = 'Yes';

                fputcsv($fopenW, $dataArray);

            }

        }

    }

//    php yii extract-data/bop-housing-report
    public function actionBopHousingReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/bop_housing_report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/bop_housing_report.csv');
        $createColumn = array("Serial No", "Name", "Address", "District", "Province", "Cnic", "Registered with NSER (Yes/No)",
            "PMT No", "Sehat Insaaf Card Status (Yes/No)", "Project type", "Tier 0( NON -NAPHDA)", "Loan amount( 2.0M)",
            "Unit size 5 marla or covered area upto 1250 sq ft", "Customer pricing 1-5 years", "Customer pricing 6-10 years", "Customer pricing 11-15 years",
            "Subsdiary rate", "Subs. Amount", "Active Borrower of Akhuwat(Yes/No)", "CIB clearance (Yes/No)", "Group Life- Takaful/Insurance (Yes/No)", "Other loans");

        fputcsv($fopenW, $createColumn);

        $sql = "SELECT
                prj.name project,
                c.full_name AS name,
                (
                SELECT
                    address
                FROM
                    members_address
                WHERE
                    is_current = 1 AND member_id = c.id AND address_type = \"home\"
                LIMIT 1
            ) AS addess, drt.name district, pr.name province, c.cnic, apd.poverty_score,(
                IF(
                    pkpp.has_sehat_card != NULL,
                    pkpp.has_sehat_card,
                    0
                )
            ) AS has_sehat_card, IF(a.project_table!='',\"Housing\",\"\") project_type ,a.loan_amount,(
            SELECT
                CEILING(kppahs.residential_area)
            FROM
                appraisals_housing kppahs
            WHERE
                kppahs.application_id = b.id
            ) unit_size
            
            FROM
                loans a
            INNER JOIN
                applications b
            ON
                b.id = a.application_id
            INNER JOIN
                members c
            ON
                c.id = b.member_id
            INNER JOIN
                projects prj
            ON
                prj.id = a.project_id
            LEFT JOIN
                projects_kpp pkpp
            ON
                pkpp.application_id = b.id
            LEFT JOIN
                application_details apd
            ON
                apd.application_id = a.application_id AND apd.parent_type = 'member'
            INNER JOIN
                branches br
            ON
                br.id = a.branch_id
            LEFT JOIN
                provinces pr
            ON
                pr.id = br.province_id
            LEFT JOIN
                districts drt
            ON
                drt.id = br.district_id
            WHERE
                a.project_id = 77 AND a.status in ('collected','loan completed')";

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();

        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                $dataArray = [];
                $dataArray['Serial_No'] = $key;
                $dataArray['Name'] = $d['name'];
                $dataArray['Address'] = $d['addess'];
                $dataArray['District'] = $d['district'];
                $dataArray['Province'] = $d['province'];
                $dataArray['Cnic'] = $d['cnic'];
                $dataArray['Registered_with_NSER'] = 'Yes';
                $dataArray['PMT_No'] = $d['poverty_score'];
                $dataArray['Sehat_Insaaf_Card_Status'] = ($d['has_sehat_card'] == 1) ? "Yes" : "No";
                $dataArray['Project_type'] = $d['project_type'];
                $dataArray['Tier_0_NON_NAPHDA)'] = "Yes";
                $dataArray['Loan amount'] = $d['loan_amount'];
                $dataArray['Unit_size_marla'] = $d['unit_size'];
                $dataArray['Customer_pricing_1_5'] = "2%";
                $dataArray['Customer_pricing_6_10'] = "4%";
                $dataArray['Customer_pricing_11_15'] = "5%";
                $dataArray['Subsidy_rate'] = 'NA';
                $dataArray['Subsidy_Amount'] = 'NA';
                $dataArray['Active_Borrower_of_Akhuwat'] = 'NO';
                $dataArray['CIB_clearance'] = 'Yes';
                $dataArray['Group_Life_Takaful'] = 'Yes';
                $dataArray['Other_loans'] = 'No';
                fputcsv($fopenW, $dataArray);

            }

        }

    }


// =================================================
//   BOP Report end
// =================================================
//   HBL Report start
// =================================================

//    php yii extract-data/bop-kisan-report
    public function actionHblSubsidyEaReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/hbl_subsidy_claim_ea.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/hbl_subsidy_claim_ea.csv');
        $createColumn = array("Serial No", "Loan No", "CNIC", "Address", "District", "Province", "Scheme",
            "Name of Borrower", "Date of birth of borrower", "Gender type of the borrower. M=Male. F=Female", "Father/Husband's name of the borrower",
            "Amount Disbursed", "Outstanding Loan Balance", "Principal Outstanding Amount against the facility", "Markup Amount Outstanding against the facility",
            "Disbursement Date", "Tenor", "Pricing", "Subsidy Rate", "Subsidy Claim Amount", "Status (Active/Overdue)", "Amount overdue", "Days Overdue",
            "Amount of Provision held against the facility", "Group Life/Takaful Insurance Cover (Y/N)", "Sehat Card (Y/N)", "Ehsas Registration");
        fputcsv($fopenW, $createColumn);

        $sql = "SELECT
                    a.sanction_no loan_no,
                    c.cnic Cnic,
                    (
                    SELECT
                        address
                    FROM
                        members_address
                    WHERE
                        is_current = 1 AND member_id = c.id AND address_type = \"home\"
                    LIMIT 1
                ) AS Addess, drt.name District, pr.name Province,(
                    CASE WHEN a.project_id = 77 THEN \"Husing\" WHEN a.project_id = 78 THEN \"Karobar\" ELSE \"Agriculture\"
                END
                ) Scheme, c.full_name AS Name, FROM_UNIXTIME(c.dob) date_of_birth_of_borrower, c.gender gender_type_of_the_borrower, 
                
                c.parentage_type ,
                 c.parentage father_husband_name_of_borrower,
                CONCAT_WS(
                    \"-\",
                    c.parentage_type,
                    c.parentage
                ) AS father_husband_name_of_borrower,
                 
                 
                 a.disbursed_amount Amount_disbursed, a.balance out_standing_loan_balance,(
                    IF(
                        a.project_id = 78 OR a.project_id = 79,
                        a.balance,
                        0
                    )
                ) Principal_Outstanding_Amount_against_the_facility,(
                    SELECT
                        SUM(scdls.charges_schdl_amount)
                    FROM
                        schedules scdls
                    WHERE
                        scdls.loan_id = a.id
                ) Markup_Amount_Outstanding_against_the_facility,
                FROM_UNIXTIME(a.date_disbursed) Disbursement_Date,
                a.inst_months Tenor,
                IF(a.deleted = 0, \"NA\", \"NA\") pricing,
                IF(a.deleted = 0, \"NA\", \"NA\") subsidy_rate,
                IF(a.deleted = 0, 0, 0) subsidy_claim_amount,
                @schdl_amnt :=(
                    SELECT
                        SUM(
                            schedules.schdl_amnt + schedules.charges_schdl_amount
                        )
                    FROM
                        schedules
                    WHERE
                        (
                            schedules.loan_id = a.id AND schedules.due_date <= UNIX_TIMESTAMP())
                        ) AS total_schdl_amnt,
                        @credit :=(
                        SELECT
                            COALESCE(
                                SUM(
                                    recoveries.amount + recoveries.charges_amount
                                ),
                                0
                            )
                        FROM
                            recoveries
                        WHERE
                            (
                                recoveries.loan_id = a.id AND recoveries.deleted = 0 AND recoveries.due_date <= UNIX_TIMESTAMP())
                            ) AS total_credit,
                            IF(
                                (@schdl_amnt - @credit) > 0,
                                'Overdue',
                                'Active'
                            ) Status_Active_Overdue,
                            @schFstAmount :=(
                            SELECT
                                (
                                    schedules.schdl_amnt + schedules.charges_schdl_amount
                                )
                            FROM
                                schedules
                            WHERE
                                schedules.loan_id = a.id
                            LIMIT 1
                        ) schAmount,
                        (@schdl_amnt - @credit) Amount_overdue,
                        IF(
                            (@schdl_amnt - @credit) > 0,
                            IF(
                                (@schdl_amnt - @credit) - @schFstAmount > @schFstAmount,
                                60,
                                30
                            ),
                            0
                        ) Days_Overdue,
                        IF(a.deleted = 0, \"NA\", \"NA\") Amount_of_Provision_held_against_the_facility,
                        IF(a.deleted = 0, \"Yes\", \"Yes\") Group_Life_Takaful_Insurance_Cover,
                        (
                            IF(
                                pkpp.has_sehat_card != NULL,
                                IF(
                                    pkpp.has_sehat_card = 0,
                                    \"NO\",
                                    \"Yes\"
                                ),
                                \"NO\"
                            )
                        ) AS Sehat_Card,
                        IF(a.deleted = 0, \"Yes\", \"Yes\") Ehsas_Registration
                    FROM
                        loans a
                    INNER JOIN
                        applications b
                    ON
                        b.id = a.application_id
                    INNER JOIN
                        members c
                    ON
                        c.id = b.member_id
                    INNER JOIN
                        projects prj
                    ON
                        prj.id = a.project_id
                    LEFT JOIN
                        projects_kpp pkpp
                    ON
                        pkpp.application_id = b.id
                    LEFT JOIN
                        application_details apd
                    ON
                        apd.application_id = a.application_id AND apd.parent_type = 'member'
                    INNER JOIN
                        branches br
                    ON
                        br.id = a.branch_id
                    LEFT JOIN
                        provinces pr
                    ON
                        pr.id = br.province_id
                    LEFT JOIN
                        districts drt
                    ON
                        drt.id = br.district_id
                    WHERE
                        a.project_id = 78 AND a.status IN('collected', 'loan completed')";

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();

        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                $dataArray = [];
                $dataArray['Serial_No'] = $key;
                $dataArray['Loan_No'] = $d['name'];
                $dataArray['CNIC'] = $d['name'];
                $dataArray['Address'] = $d['name'];
                $dataArray['District'] = $d['name'];
                $dataArray['Province'] = $d['name'];
                $dataArray['Scheme'] = $d['name'];
                $dataArray['Name_of_Borrower'] = $d['name'];
                $dataArray['dob'] = $d['name'];
                $dataArray['Gender'] = $d['name'];
                $dataArray['parentage'] = $d['name'];
                $dataArray['Amount_Disbursed'] = $d['name'];
                $dataArray['Outstanding_Loan_Balance'] = $d['name'];
                $dataArray['Principal_Outstanding_Amount'] = $d['name'];
                $dataArray['Markup_Outstanding_Amount'] = $d['name'];
                $dataArray['Disbursement_Date'] = $d['name'];
                $dataArray['Tenor'] = $d['name'];
                $dataArray['Pricing'] = $d['name'];
                $dataArray['Subsidy_Rate'] = $d['name'];
                $dataArray['Subsidy_Claim_Amount'] = $d['name'];
                $dataArray['Status'] = $d['name'];
                $dataArray['Amount_overdue'] = $d['name'];
                $dataArray['Days_Overdue'] = $d['name'];
                $dataArray['Amount_of_Provision'] = $d['name'];
                $dataArray['Takaful'] = $d['name'];
                $dataArray['Sehat_Card'] = $d['name'];
                $dataArray['Ehsas_Registration'] = $d['name'];
                fputcsv($fopenW, $dataArray);

            }

        }

    }

//    php yii extract-data/bop-housing-report
    public function actionHblSubsidyWlReport()
    {

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/bop_housing_report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/bop_housing_report.csv');
        $createColumn = array("Serial No", "Name of EA", "Amount Disbursed", "Outstanding Loan Balance",
            "Maturity Date", "Tenor", "Pricing", "Quarter End Date", "Subsidy Claim Amount", "Status (Active/Overdue)", "Days Overdue");

        fputcsv($fopenW, $createColumn);

        $sql = "";

        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();

        if (!empty($resultQuery) && $resultQuery != null) {
            foreach ($resultQuery as $key => $d) {
                $dataArray = [];
                $dataArray['Serial_No'] = $key;
                $dataArray['Name_of_EA'] = $d['name'];
                $dataArray['Amount_Disbursed'] = $d['name'];
                $dataArray['olp'] = $d['name'];
                $dataArray['Maturity_Date'] = $d['name'];
                $dataArray['Tenor'] = $d['name'];
                $dataArray['Pricing'] = $d['name'];
                $dataArray['Quarter_End_Date'] = $d['name'];
                $dataArray['Subsidy_Claim_Amount'] = $d['name'];
                $dataArray['status'] = $d['name'];
                $dataArray['Days_Overdue'] = $d['name'];

                fputcsv($fopenW, $dataArray);

            }

        }

    }

// =================================================
//   BOP Report end
// =================================================

//    php yii extract-data/data-app-detail
    public function actionDataAppDetail()
    {

        $array = [];

        foreach ($array as $a) {
            $existingApplication = Applications::find()->where(['id' => $a])->one();
            if (!empty($existingApplication) && $existingApplication != null) {
                $extAppDetails = ApplicationDetails::find()->where(['application_id' => $existingApplication->id])
                    ->andWhere(['parent_type' => 'member'])
                    ->andWhere(['parent_id' => $existingApplication->member_id])
                    ->andWhere(['poverty_score' => 0])
                    ->one();
                if (empty($extAppDetails) && $extAppDetails == null) {
                    $model = new ApplicationDetails();
                    $model->application_id = $existingApplication->id;
                    $model->parent_type = 'member';
                    $model->parent_id = $existingApplication->member_id;
                    $model->poverty_score = 0;
                    $model->status = 0;
                    if ($model->save()) {
                        echo 'saved ----';
                    } else {
                        var_dump($model->getErrors());
                        die();
                    }
                }

            }

        }

    }

//    php yii extract-data/export-cnic
    public function actionExportCnic()
    {
        $cnic = [];

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/cnic_report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/cnic_report.csv');
        $createColumn = array("Name", "Cnic", "Date Of Birth");

        fputcsv($fopenW, $createColumn);

        foreach ($cnic as $c) {
            $sql = "SELECT
            m.full_name,
            m.cnic,
            m.dob
        FROM
            members m
        WHERE
            m.cnic='$c'";
            $query = Yii::$app->db->createCommand($sql);
            $resultQuery = $query->queryAll();
            if (!empty($resultQuery[0]) && $resultQuery[0] != null) {
                fputcsv($fopenW, $resultQuery[0]);
            }
        }


    }

//------------------------------------------------------
//    php yii extract-data/export-who-will-work
    public function actionExportWhoWillWork()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/work_report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/work_report.csv');
        $createColumn = array("Sanction No", "Who Will Work", "name of other", "other cnic");
        fputcsv($fopenW, $createColumn);

        $file_path = ImageHelper::getAttachmentPath() . '/kpp_reports/sanction_data.csv';
        $myfile = fopen($file_path, "r");
        $header = fgetcsv($myfile);
        $i = 2;
        while (($fileop = fgetcsv($myfile)) !== false) {
            $sql = "SELECT
                    b.sanction_no,
                    a.who_will_work,
                    COALESCE(a.name_of_other, \"NA\") name_of_other,
                    COALESCE(a.other_cnic, \"NA\") other_cnic
                FROM
                    applications a
                INNER JOIN
                    loans b
                ON
                    b.application_id = a.id
                WHERE
                    b.sanction_no = '$fileop[0]'";
            $query = Yii::$app->db->createCommand($sql);
            $resultQuery = $query->queryAll();
            if (!empty($resultQuery[0]) && $resultQuery[0] != null) {
                if (empty($resultQuery[0]['name_of_other'])) {
                    $resultQuery[0]['name_of_other'] = "NA";
                }
                if (empty($resultQuery[0]['other_cnic'])) {
                    $resultQuery[0]['other_cnic'] = "NA";
                }
                fputcsv($fopenW, $resultQuery[0]);
            }
        }
    }

//------------------------------------------------------
//    php yii extract-data/export-cnic-dob
    public function actionExportCnicDob()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/cnic_dob.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/work_report.csv');
        $createColumn = array("cnic", "full_name", "dob");
        fputcsv($fopenW, $createColumn);

        $file_path = ImageHelper::getAttachmentPath() . '/kpp_reports/sanction_data.csv';
        $myfile = fopen($file_path, "r");
        $header = fgetcsv($myfile);
        $i = 2;

        $nicArray = [];
        foreach ($nicArray as $nic) {
            $sql = "SELECT `cnic`,`full_name`,`dob` FROM `members` WHERE `cnic` = '$nic'";
            $query = Yii::$app->db->createCommand($sql);
            $resultQuery = $query->queryAll();
            if (!empty($resultQuery[0]) && $resultQuery[0] != null) {
                $resultQuery[0]['dob'] = date('Y-m-d', $resultQuery[0]['dob']);
                fputcsv($fopenW, $resultQuery[0]);
            }
        }
    }

    //extract members and saction no by bcode
    public function actionMemberData()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);


        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/members_data-city-wise.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'members_data-city-wise.csv');


        $createColumn = array("ID", "Name", "Parentage", "CNIC", "City Name", "Mobile");
        fputcsv($fopenW, $createColumn);

        $db = Yii::$app->db;
        $loan_query = "SELECT m.id, m.full_name,m.parentage,m.cnic,ct.name,ph.phone FROM members m 
RIGHT JOIN members_phone ph ON m.id = ph.member_id AND ph.is_current = 1 AND LENGTH(ph.phone) >= 11
INNER JOIN branches br ON m.branch_id = br.id
INNER JOIN cities ct ON	ct.id = br.city_id
WHERE
	ct.id in (15,121,3,71,51,7,85)
GROUP BY
    ph.phone
ORDER BY
    m.cnic";
        $recoveryCount = $db->createCommand($loan_query)->queryAll();
        if (!empty($recoveryCount) && $recoveryCount != null) {
            foreach ($recoveryCount as $d) {
                fputcsv($fopenW, $d);
            }


        }
    }

// php yii extract-data/export-due-amount
    public function actionExportDueAmount()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/export-due-amount-July-2022.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/export-due-amount-July-2022.csv');
        $createColumn = array("Name", "Parentage", "CNIC", "Date Disbursed", "Sanction No", "Loan Amount", "Balance", "Inst Amount", "Overdue", "total schdl", "Total Recovery",  "Jul-22");
        fputcsv($fopenW, $createColumn);

        $file_path = ImageHelper::getAttachmentPath() . '/kpp_reports/due-amount-July-2022.csv';
        $myfile = fopen($file_path, "r");
        $flag = false;
        $i = 0;
        while (($fileop = fgetcsv($myfile)) !== false) {

            $sql = "SELECT
                        d.full_name,
                        d.parentage,
                        d.cnic,
                        b.date_disbursed,
                        b.sanction_no,
                        b.loan_amount,
                        b.balance,
                        b.inst_amnt,
                        b.overdue,
                        @sch_amnt :=(SELECT sum(a.schdl_amnt) FROM schedules a WHERE a.due_date <= '1657436399'  AND a.loan_id=b.id) schd_amount_total,
                        @rec_amnt :=(SELECT sum(r.amount) FROM recoveries r WHERE r.receive_date <= '1656572399' AND r.loan_id=b.id  AND r.deleted=0) rec_amount_total,
                        (@sch_amnt-@rec_amnt) as due_amount
                    FROM
                        schedules a
                        INNER JOIN loans b ON b.id=a.loan_id
                        INNER JOIN applications c ON c.id=b.application_id
                        INNER JOIN members d ON d.id=c.member_id
                    WHERE
                        b.sanction_no = '$fileop[0]' AND b.status='collected' GROUP BY b.sanction_no";

            $query = Yii::$app->db->createCommand($sql);
            $resultQuery = $query->queryAll();

            if (!empty($resultQuery[0]) && $resultQuery[0] != null) {
                foreach ($resultQuery as $r) {
                    $r['date_disbursed'] = date('Y-m-d', $r['date_disbursed']);
                    fputcsv($fopenW, $r);
                    print_r($r);
                }
            }
        }
    }

    // php yii extract-data/sanction-products

    public function actionSanctionProducts()
    {
        $cib_response = [1 => 'sanction-lists-products'];
        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/sanction-lists-products-data.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/sanction-lists-products-data.csv');
        $createColumn = array("Sanction No", "Product");
        fputcsv($fopenW, $createColumn);

        foreach ($cib_response as $report) {
            $batch_folder = [1 => 'Book1', 2 => 'Book2', 3 => 'Book3', 4 => 'Book4', 5 => 'Book5', 6 => 'Book6', 7 => 'Book7', 8 => 'Book8'];
            foreach ($batch_folder as $batch) {
                $file_path = ImageHelper::getAttachmentPath() . '/kpp_reports/' . $report . '/' .  $batch . '.csv';
                $myfile = fopen($file_path, "r");
                $header = fgetcsv($myfile);
                $i = 1;
                while (($fileop = fgetcsv($myfile)) !== false) {
                    $member = Loans::find()->where(['sanction_no' => $fileop[0]])->select(['product_id','sanction_no'])->one();
                    if (!empty($member)) {
                        $product = Products::find()->where(['id' => $member->product_id, 'deleted' => 0])->select(['name'])->one();
                        if (!empty($product)) {
                            $data = [
                                'sanction' => $fileop[0],
                                'product' => $product->name
                            ];
                            fputcsv($fopenW, $data);
                            print_r($data);

                        } else {
                            echo 'loan product not exists! against sanction no ' . $fileop[0];
                        }

                    } else {
                        echo 'loan does not exists! against sanction no ' .$fileop[0];
                    }
                    $i++;
                }
            }
        }
    }

// nohup php yii extract-data/loans-emergency-work
    public function actionLoansEmergencyWork()
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 10000);

        $regions = Regions::find()->where(['status'=>1])->all();
        foreach ($regions as $region){
            $filepathW = ImageHelper::getAttachmentPath() . 'member_recovery/member_emergency_data'.$region->id.'.csv';
            $fopenW = fopen($filepathW, 'w');
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename=' . 'member_recovery/member_emergency_data'.$region->id.'.csv');

            $createColumn = array("Sanction", "Disbursed Date", "last Recovery Date", "Recovery Count");
            fputcsv($fopenW, $createColumn);

            $db = Yii::$app->db;
            $loan_query = "SELECT
                loans.sanction_no,
                FROM_UNIXTIME(loans.date_disbursed) date_disbursed,
            (
                SELECT
                    FROM_UNIXTIME(recoveries.receive_date)
                FROM
                    recoveries
                WHERE
                    recoveries.loan_id = loans.id AND recoveries.deleted = 0
                ORDER BY
                    id
                DESC
            LIMIT 1
            ) last_recovery_date,
                (
                SELECT
                    COUNT(id)
                FROM
                    recoveries
                WHERE
                    recoveries.loan_id = loans.id AND recoveries.deleted = 0
            ) recovery_count
            FROM
                loans
            WHERE
                loans.status = 'loan completed' AND loans.region_id=$region->id
                ";

            $LoanData = $db->createCommand($loan_query)->queryAll();
            if (!empty($LoanData) && $LoanData != null) {
                foreach ($LoanData as $key => $d) {
                    fputcsv($fopenW, $d);

                }

            }
        }


    }
    // nohup php yii extract-data/active-loans-detail-olp 2025-07-07

    public function actionActiveLoansDetailOlp($date)
    {
        $dated = strtotime(date('Y-m-d',strtotime($date)));
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 10000);
        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/member_olp_guarantor_data.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'complete_data_extract/member_olp_guarantor_data.csv');

        $createColumn = array("Member Name", "Sanction No.", "Date Of Disbursed", "CNIC NO.", "Outstanding Amount (RS.)",
            "AML Risk Categorization", "Address", "Mobile No", "Beneficiary Name", "Beneficiary CNIC", "Family Member Name", "Family Member CNIC",
            "Guarantor1Name", "Guarantor1Parentage", "Guarantor1cnic", "Guarantor2Name", "Guarantor2Parentage", "Guarantor2cnic");
        fputcsv($fopenW, $createColumn);
        $db = Yii::$app->db;
        $regions = Regions::find()->where(['status'=>1])->all();

        foreach ($regions as $region){
            $loan_query = " 
                SELECT
                        d.full_name member_name,
                        b.sanction_no,
                        FROM_UNIXTIME(b.date_disbursed) date_disbursed,
                        d.cnic member_cnic,
                        @rec_amnt :=(SELECT coalesce(sum(r.amount),0) FROM recoveries r WHERE r.receive_date <= $dated AND r.loan_id=b.id  AND r.deleted=0) rec_amount_total,
                        (b.disbursed_amount-@rec_amnt) as olp,
                        (SELECT phone FROM members_phone where is_current=1 and member_id=d.id order by created_at desc LIMIT 1) as phone,
                        (SELECT address FROM members_address where address_type='home' and is_current=1 and member_id=d.id LIMIT 1) address,
                        c.name_of_other as beneficiary_name,
                        c.other_cnic as beneficiary_cnic,
                        d.family_member_name,
                        d.family_member_cnic,
                        MAX(CASE WHEN t2_row_num = 1 THEN t2.name END) AS name1,
                        MAX(CASE WHEN t2_row_num = 1 THEN t2.parentage END) AS parentage1,
                        MAX(CASE WHEN t2_row_num = 1 THEN t2.cnic END) AS cnic1,
                        MAX(CASE WHEN t2_row_num = 2 THEN t2.name END) AS name2,
                        MAX(CASE WHEN t2_row_num = 2 THEN t2.parentage END) AS parentage2,
                        MAX(CASE WHEN t2_row_num = 2 THEN t2.cnic END) AS cnic2
                    FROM 
                        loans b
                        INNER JOIN applications c ON c.id=b.application_id
                        INNER JOIN members d ON d.id=c.member_id
                    LEFT JOIN 
                        (SELECT 
                             t2.id,
                             t2.group_id,
                             t2.name,
                             t2.parentage,
                             t2.cnic,
                             IF(@group_id = t2.group_id, @row_num := @row_num + 1, @row_num := 1) AS t2_row_num,
                             @group_id := t2.group_id
                        FROM 
                             guarantors t2
                         JOIN 
                             (SELECT @row_num := 0, @group_id := NULL) vars
                         ORDER BY 
                             t2.group_id, t2.id) t2
                    ON 
                        c.group_id = t2.group_id
                    
                    WHERE b.status='collected' AND c.region_id=$region->id
                     
                    GROUP BY 
                        b.id
                    ORDER BY 
                        c.id;
             ";

            $LoanData = $db->createCommand($loan_query)->queryAll();
            if (!empty($LoanData) && $LoanData != null) {
                foreach ($LoanData as $key => $d) {

                    $array['member_name'] = $d['member_name'];
                    $array['sanction_no'] = $d['sanction_no'];
                    $array['date_disbursed'] = $d['date_disbursed'];
                    $array['member_cnic'] = $d['member_cnic'];
                    $array['olp'] = $d['olp'];
                    $array['risk_categorization'] = 'LOW';
                    $array['address'] = $d['address'];
                    $array['phone'] = $d['phone'];
                    $array['beneficiary_name'] = $d['beneficiary_name'];
                    $array['beneficiary_cnic'] = $d['beneficiary_cnic'];
                    $array['family_member_name'] = $d['family_member_name'];
                    $array['family_member_cnic'] = $d['family_member_cnic'];
                    $array['name1'] = $d['name1'];
                    $array['parentage1'] = $d['parentage1'];
                    $array['cnic1'] = $d['cnic1'];
                    $array['name2'] = $d['name2'];
                    $array['parentage2'] = $d['parentage2'];
                    $array['cnic2'] = $d['cnic2'];

                    fputcsv($fopenW, $array);

                }
            }
        }

    }

    // php yii extract-data/urgent-data-export

    public function actionUrgentDataExport()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/urgent_date.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/urgent_date.csv');
        $createColumn = ['Region', 'Area', 'Branch', 'Branch Code', 'Sanction No', 'Name', 'CNIC', 'Tranche No',
            'Project', 'Product', 'Disbursed date', 'Loan Amount', 'Tranche Amount'
            ,'BaseRent-Jul-23','FixedRent-Jul-23'
            ,'BaseRent-Aug-23','FixedRent-Aug-23'
            ,'BaseRent-Sept-23','FixedRent-Sept-23'
            ,'BaseRent-Oct-23','FixedRent-Oct-23'
            ,'BaseRent-Nov-23','FixedRent-Nov-23'
            ,'BaseRent-Dec-23','FixedRent-Dec-23'
            ,'BaseRent-Jan-24','FixedRent-Jan-24'
            ,'BaseRent-Feb-24','FixedRent-Feb-24'
            ,'BaseRent-Mar-24','FixedRent-Mar-24'
            ,'BaseRent-Apr-24','FixedRent-Apr-24'
            ,'BaseRent-May-24','FixedRent-May-24'
            ,'BaseRent-Jun-24','FixedRent-Jun-24'
            ,'BaseRent-Jul-24','FixedRent-Jul-24'
            ,'BaseRent-Aug-24','FixedRent-Aug-24'
        ];
        fputcsv($fopenW, $createColumn);

        $sql = "SELECT
            a.id,re.name region,ar.name area,br.name branch,br.code branch_code,a.sanction_no,m.full_name,m.cnic,
            ltr.tranch_no,ltr.tranch_amount,pr.name project,prd.name product,FROM_UNIXTIME(ltr.date_disbursed) date_disbursed,a.loan_amount
        FROM
            loan_tranches ltr
            INNER JOIN loans a on a.id=ltr.loan_id
            INNER JOIN applications app on app.id=a.application_id
            INNER JOIN members m on m.id=app.member_id
            INNER JOIN regions re on re.id=a.region_id
            INNER JOIN areas ar on ar.id=a.area_id
            INNER JOIN branches br on br.id=a.branch_id
            INNER JOIN projects pr on pr.id=a.project_id
            INNER JOIN products prd on prd.id=a.product_id
            
        WHERE
            a.project_id in(52,76,77,62,64,97,83,103,90,61,67,119,109,100,118,110,114,96,104) 
           and a.status in(\"collected\")";
        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();
        if (!empty($resultQuery[0]) && $resultQuery[0] != null) {
            foreach ($resultQuery as $result) {
                $arrayResult = [];
                $loanId = $result['id'];
                $arrayResult['region'] = $result['region'];
                $arrayResult['area'] = $result['area'];
                $arrayResult['branch'] = $result['branch'];
                $arrayResult['branch_code'] = $result['branch_code'];
                $arrayResult['sanction_no'] = $result['sanction_no'];
                $arrayResult['full_name'] = $result['full_name'];
                $arrayResult['cnic'] = $result['cnic'];
                $arrayResult['tranch_no'] = $result['tranch_no'];
                $arrayResult['project'] = $result['project'];
                $arrayResult['product'] = $result['product'];
                $arrayResult['date_disbursed'] = $result['date_disbursed'];
                $arrayResult['loan_amount'] = $result['loan_amount'];
                $arrayResult['tranch_amount'] = $result['tranch_amount'];

                if($result['tranch_no'] == 1){
                    $dateArray = ['2023-07', '2023-08', '2023-09', '2023-10', '2023-11', '2023-12', '2024-01', '2024-02', '2024-03','2024-04'
                        ,'2024-05','2024-06','2024-07','2024-08'];
                    foreach ($dateArray as $date) {
                        $date1 = date('Y-m-01', strtotime($date));
                        $date2 = date('Y-m-t', strtotime($date));
                        $dateFrom = strtotime($date1);
                        $dateTo = strtotime($date2);

                        $recoveryData = "
                    SELECT
                           sum(amount) base_rent, sum(charges_amount) fixed_rent
                        FROM
                            recoveries
                            
                        WHERE
                            deleted=0 and loan_id=$loanId
                            and `receive_date`>=$dateFrom AND `receive_date`<=$dateTo
                    ";

                        $queryRec = Yii::$app->db->createCommand($recoveryData);
                        $resultQueryRec = $queryRec->queryAll();
                        if (!empty($resultQueryRec[0]) && $resultQueryRec[0]['base_rent'] != null) {
                            $arrayResult['base_rent_'.$date] = $resultQueryRec[0]['base_rent'];
                            $arrayResult['fixed_rent_'.$date] = $resultQueryRec[0]['fixed_rent'];
                        } else {
                            $scheduleData = "
                            SELECT schdl_amnt as base_rent, charges_schdl_amount as fixed_rent
                                FROM schedules
                                WHERE
                                  loan_id=$loanId
                                  and `due_date`>=$dateFrom AND `due_date`<=$dateTo
                            ";

                            $querySchd = Yii::$app->db->createCommand($scheduleData);
                            $resultQuerySchd = $querySchd->queryAll();

                            $arrayResult['base_rent_'.$date] = $resultQuerySchd[0]['base_rent'];
                            $arrayResult['fixed_rent_'.$date] = $resultQuerySchd[0]['fixed_rent'];
                        }
                    }
                }

                fputcsv($fopenW, $arrayResult);
            }
        }

    }

    // php yii extract-data/urgent-future-data-export

    public function actionUrgentFutureDataExport()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/urgent_future_date.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/urgent_future_date.csv');
        $createColumn = ['Region', 'Area', 'Branch', 'Branch Code', 'Sanction No', 'Name', 'CNIC', 'Tranche No',
            'Project', 'Product', 'Disbursed date', 'Loan Amount', 'Tranche Amount'
            ,'BaseRent-Jan-2025','FixedRent-Jan-2025'
            ,'BaseRent-Feb-2025','FixedRent-Feb-2025'
            ,'BaseRent-Mar-2025','FixedRent-Mar-2025'
            ,'BaseRent-Apr-2025','FixedRent-Apr-2025'
            ,'BaseRent-May-2025','FixedRent-May-2025'
            ,'BaseRent-Jun-2025','FixedRent-Jun-2025'
            ,'BaseRent-Jun-2025','FixedRent-Jun-2025'
            ,'BaseRent-Aug-2025','FixedRent-Aug-2025'
            ,'BaseRent-Sep-2025','FixedRent-Sep-2025'
            ,'BaseRent-Oct-2025','FixedRent-Oct-2025'
            ,'BaseRent-Nov-2025','FixedRent-Nov-2025'
            ,'BaseRent-Dec-2025','FixedRent-Dec-2025'

        ];

        for ($year = 2026; $year <= 2037; $year++) {
            for ($month = 1; $month <= 12; $month++) {
                $date = \DateTime::createFromFormat('Y-m', "$year-$month");
                $formattedMonth = $date->format('M');
                $BaseRent = "BaseRent-$formattedMonth-$year";
                $FixedRent = "FixedRent-$formattedMonth-$year";
                array_push($createColumn, $BaseRent, $FixedRent);
            }
        }

        fputcsv($fopenW, $createColumn);

        $sql = " SELECT
            a.id,re.name region,ar.name area,br.name branch,br.code branch_code,a.sanction_no,m.full_name,m.cnic,
            ltr.tranch_no,ltr.tranch_amount,pr.name project,prd.name product,FROM_UNIXTIME(ltr.date_disbursed) date_disbursed,a.loan_amount
        FROM
            loan_tranches ltr
            INNER JOIN loans a on a.id=ltr.loan_id
            INNER JOIN applications app on app.id=a.application_id
            INNER JOIN members m on m.id=app.member_id
            INNER JOIN regions re on re.id=a.region_id
            INNER JOIN areas ar on ar.id=a.area_id
            INNER JOIN branches br on br.id=a.branch_id
            INNER JOIN projects pr on pr.id=a.project_id
            INNER JOIN products prd on prd.id=a.product_id
            
        WHERE
            a.project_id in(24,52,61,62,64,67,76,77,83,90,96,97,100,103,104,109,110,114,118,119,127,131,134) 
           and a.status in(\"collected\") ";
        $query = Yii::$app->db->createCommand($sql);
        $resultQuery = $query->queryAll();
        if (!empty($resultQuery[0]) && $resultQuery[0] != null) {
            foreach ($resultQuery as $result) {
                $arrayResult = [];
                $loanId = $result['id'];
                $arrayResult['region'] = $result['region'];
                $arrayResult['area'] = $result['area'];
                $arrayResult['branch'] = $result['branch'];
                $arrayResult['branch_code'] = $result['branch_code'];
                $arrayResult['sanction_no'] = $result['sanction_no'];
                $arrayResult['full_name'] = $result['full_name'];
                $arrayResult['cnic'] = $result['cnic'];
                $arrayResult['tranch_no'] = $result['tranch_no'];
                $arrayResult['project'] = $result['project'];
                $arrayResult['product'] = $result['product'];
                $arrayResult['date_disbursed'] = $result['date_disbursed'];
                $arrayResult['loan_amount'] = $result['loan_amount'];
                $arrayResult['tranch_amount'] = $result['tranch_amount'];

                if($result['tranch_no'] == 1){
                    $dateFrom = strtotime(date('2024-01-01'));
                    $dateTo = strtotime(date('2024-12-t'));

                    $scheduleData = "
                        SELECT schdl_amnt as base_rent, charges_schdl_amount as fixed_rent,due_date
                            FROM schedules
                            WHERE
                              loan_id = $loanId
                              and `due_date` >= $dateFrom
                     ";
                    $querySchedule = Yii::$app->db->createCommand($scheduleData);
                    $resultSchedules = $querySchedule->queryAll();

                    $borrowerOlp = "
                        SELECT
                          @rec_amnt :=(SELECT coalesce(sum(r.amount),0) FROM recoveries r WHERE r.receive_date <= $dateTo AND r.loan_id=b.id  AND r.deleted=0) rec_amount_total,
                          (b.disbursed_amount-@rec_amnt) as olp
                        FROM 
                          loans b
                        WHERE b.id=$loanId;
                     ";

                    $queryOlp = Yii::$app->db->createCommand($borrowerOlp);
                    $resultOlp = $queryOlp->queryAll();
                    $resultOlp = $resultOlp[0]['olp'];

                    foreach ($resultSchedules as $key=>$schedule){

                        if($resultOlp>0){
                            $baseRent = $resultOlp-$schedule['base_rent'];

                            if($baseRent > 0 && $baseRent < $resultOlp){
//                                echo date('Y-m-d',$schedule['due_date']);
//                                echo '---';
//                                echo $schedule['base_rent'];
//                                echo '---';
                                $arrayResult['base_rent_'.$key] = $schedule['base_rent'];
                                $arrayResult['fixed_rent_'.$key] = $schedule['fixed_rent'];
                            }else{
//                                echo date('Y-m-d',$schedule['due_date']);
//                                echo '--';
//                                echo $resultOlp;
//                                echo '---';
                                $arrayResult['base_rent_'.$key] = $resultOlp;
                                $arrayResult['fixed_rent_'.$key] = $schedule['fixed_rent'];
                            }

                            $resultOlp = $resultOlp-$schedule['base_rent'];
                        }
                    }

                }

                fputcsv($fopenW, $arrayResult);
            }
        }

    }

    //php yii extract-data/one-link-schedule-data
    public function actionOneLinkScheduleData(){
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);

        $branchID = 1;
        $paramsDate = date('Y-04-01');

        $due_date = strtotime(date('Y-m-10-00:00', (strtotime($paramsDate))));
        $disburse_date = strtotime(date("Y-m-d-23:59", $due_date));
        $receipt_date = strtotime(date('Y-m-t-23:59', (strtotime($paramsDate . '- 1 months'))));

        if ($branchID != 0) {

            $query = "
                select
                    rg.id AS region_id,
                    ar.id AS area_id,
                    br.id AS branch_id,
                    m.full_name,
                    m.parentage,
                    m.cnic,
                    maddr.address,
                    mphon.phone,
                    ln.sanction_no,
                    sch.id AS installment_id,
                    ln.project_id,
                    pr.name AS project_name,
                    pr.bank_prefix,
                    DATE_FORMAT(FROM_UNIXTIME(sch.due_date), '%Y-%m-%d') AS due_date,
                    DATE_FORMAT(FROM_UNIXTIME(ln.date_disbursed), '%Y-%m-%d') AS date_disbursed,
                    ln.disbursed_amount,
                    
                    @recovery:=(select COALESCE(sum(amount),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) 'recovery',
                    
                   @balance:=(ln.disbursed_amount-@recovery) as 'olp',
                    
                   @due_amount:=((select COALESCE(sum(schdl_amnt),0) from schedules where loan_id=ln.id and due_date <= $due_date)-@recovery) 'actual_due_amount',
                    
                    
                   @receive_charges_amount:=(select COALESCE(sum(charges_amount),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) as 'receive_charges_amount',
                   @due_charges_amount:=((select COALESCE(sum(charges_schdl_amount),0) from schedules where loan_id=ln.id and due_date <= $due_date group by loan_id)-@receive_charges_amount) 'actual_charges_amount_due',

                   @receive_sale_tax:=(select COALESCE(sum(credit_tax),0) from recoveries where loan_id=ln.id and deleted=0 and receive_date <=$receipt_date) as 'receive_sale_tax',
                   @due_amount_tax:=((select COALESCE(sum(charges_schdl_amnt_tax),0) from schedules where loan_id=ln.id and due_date <= $due_date group by loan_id )-@receive_sale_tax) 'actual_sale_tax_due'
                   ,@due_1 := CASE
                       WHEN @due_amount > sch.schdl_amnt THEN @due_amount
                       WHEN sch.schdl_amnt IS NULL THEN @due_amount
                       WHEN @balance < sch.schdl_amnt THEN @balance
                       ELSE sch.schdl_amnt
                    END AS 'due_amount',
            
                    @due_2 := CASE
                       WHEN @due_charges_amount > sch.charges_schdl_amount THEN @due_charges_amount
                       WHEN sch.charges_schdl_amount IS NULL THEN @due_charges_amount
                       ELSE sch.charges_schdl_amount
                       END AS 'due_charges_amount',
                   @due_3 := CASE
                       WHEN @due_amount_tax > sch.charges_schdl_amnt_tax THEN @due_amount_tax
                       WHEN sch.charges_schdl_amnt_tax IS NULL THEN @due_amount_tax
                       ELSE sch.charges_schdl_amnt_tax
                       END AS 'due_amount_tax',
                   
                   @due_1+@due_2+@due_3 'total_due'
                   
                    from
                    loans ln
                    inner join applications app
                    on ln.application_id = app.id and app.deleted=0 and app.status='approved'
                    inner join members m
                    on app.member_id = m.id
                    left join members_phone mphon
                    on mphon.member_id = m.id and mphon.is_current=1 and LENGTH(mphon.phone) >= 11
                    left join members_address maddr
                    on maddr.member_id = m.id and maddr.is_current=1
                    Inner join projects pr
                    on pr.id=ln.project_id
                    inner join groups grp
                    on ln.group_id = grp.id
                    inner join regions rg
                    on ln.region_id = rg.id
                    inner join areas ar
                    on ln.area_id=ar.id
                    inner join branches br
                    on ln.branch_id = br.id
                    LEFT JOIN schedules sch ON sch.loan_id = ln.id and sch.due_date=$due_date
                    where
                    ln.status in ('collected')
                    and
                    ln.date_disbursed <= $disburse_date
                    AND br.id in ($branchID)
                    group by ln.sanction_no
                    having olp>0 and due_amount > 0 due_date is not null
                ";


            $queryData = Yii::$app->db->createCommand($query);
            $branchActiveLoans = $queryData->queryAll();

            $response['meta'] = [
                'error' => true,
                'message' => 'Branch not exists!',
                'status_code' => 201
            ];
            $response['data'] = $branchActiveLoans;
            return JsonHelper::asJson($response);
        } else {
            $response['meta'] = [
                'error' => true,
                'message' => 'Branch not exists!',
                'status_code' => 201
            ];
            $response['data'] = [];
            return JsonHelper::asJson($response);
        }
    }


    // nohup php yii extract-data/general-data

    public function actionGeneralData()
    {
        ini_set('memory_limit', '202400M');
        ini_set('max_execution_time', 10000);
        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/members_general_data.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'complete_data_extract/members_general_data.csv');

        $createColumn = array(
            "Sanction No",
            "Project Name",
            "Group No",
            "Member Name",
            "Parentage",
            "Parentage Type",
            "CNIC",
            "Gender",
            "Date of Birth",
            "Marital Status",
            "Family Member Name",
            "Family Member CNIC",
            "Religion",
            "Business Condition",
            "Who Will Work",
            "Name of Other",
            "Other CNIC",
            "Requested Amount",
            "Recommended Amount",
            "Application Status",
            "Is Urban",
            "Client Contribution",
            "Loan Amount",
            "Disbursed Amount",
            "Service Charges",
            "Loan Charges Amount",
            "Installment Amount",
            "Installment Months",
            "Installment Type",
            "Date Disbursed",
            "Loan Status",
            "Poverty Index",
            "House Ownership",
            "House Rent Amount",
            "Land Size",
            "Total Family Members",
            "No of Earning Hands",
            "Ladies",
            "Gents",
            "Source of Income",
            "Total Household Income",
            "Utility Bills",
            "Educational Expenses",
            "Medical Expenses",
            "Kitchen Expenses",
            "Monthly Savings",
            "Appraisal Amount",
            "Date of Maturity",
            "Other Expenses",
            "Total Expenses",
            "Other Loan",
            "Appraisal Loan Amount",
            "Who Will Earn",
            "Earning Person Name",
            "Earning Person CNIC",
            "Economic Dealings",
            "Social Behaviour",
            "Fatal Disease",
            "Business Income",
            "Job Income",
            "House Rent Income",
            "Other Income",
            "Expected Increase in Income",
            "Parent",
            "Family Member Info",
            "Earning Hands Data",
            "Social Appraisal Address",
            "House Condition",
            "Domestic Assets",
            "Livestock Income",
            "Through Cultivation",
            "Pension",
            "Income Before Corona",
            "Income After Corona",
            "Expenses in Corona",
            "Emergency House Ownership",
            "Emergency Total Family Members",
            "Emergency Earning Hands",
            "Emergency Ladies",
            "Emergency Gents",
            "Emergency Economic Dealings",
            "Emergency Social Behaviour",
            "Emergency Fatal Disease",
            "Emergency Appraisal Address",
            "Place of Business",
            "Fixed Business Assets",
            "Fixed Business Assets Amount",
            "Running Capital",
            "Running Capital Amount",
            "Business Expenses",
            "Business Expenses Amount",
            "New Required Assets",
            "New Required Assets Amount",
            "Place of Buying",
            "Period of Business",
            "Who Are Customers",
            "Employees Before Loan",
            "Employees After Loan",
            "Business Appraisal Address",
            "Property Type",
            "Ownership",
            "Land Area",
            "Residential Area",
            "Living Duration",
            "Duration Type",
            "No of Rooms",
            "No of Kitchens",
            "No of Toilets",
            "Purchase Price",
            "Current Price",
            "Housing Address",
            "Applicant Share",
            "AIM Share",
            "Housing Appraisal Address",
            "Animal Type",
            "Business Type",
            "Business Condition",
            "Business Place",
            "Used Land Type",
            "Used Land Size",
            "Available Amount",
            "Required Amount",
            "New Assets",
            "Monthly Income",
            "Expected Income",
            "Due Date",
            "Scheduled Amount",
            "Schedule Charges Amount",
            "Schedule Charges Tax",
            "Recovery Amount",
            "Recovery Charges",
            "Receipt No",
            "Source",
            "Receive Date"

        );

        fputcsv($fopenW, $createColumn);
        $db = Yii::$app->db;
        $loan_query = " 
                SELECT  
                    l.sanction_no,
                    p.name AS project_name,
                    g.grp_no,
                    m.full_name,
                    m.parentage,
                    m.parentage_type,
                    m.cnic,
                    m.gender,
                    from_unixtime (m.dob, \"%y-%m-%d\")as dob,
                    m.marital_status,
                    m.family_member_name,
                    m.family_member_cnic,
                    m.religion,
                    a.bzns_cond,
                    a.who_will_work,
                    a.name_of_other,
                    a.other_cnic,
                    a.req_amount,
                    a.recommended_amount,
                    a.status AS application_status,
                    a.is_urban,
                    a.client_contribution,
                    l.loan_amount AS loan_amount,
                    l.disbursed_amount,
                    l.service_charges,
                    l.charges_amount AS loans_charges_amount,
                    l.inst_amnt,
                    l.inst_months,
                    l.inst_type,
                    from_unixtime (l.date_disbursed, \"%y-%m-%d\")as date_disbursed,
                    l.status AS loan_status,
                    aps.poverty_index,
                
                    -- Social Appraisal
                    aps.house_ownership,
                    aps.house_rent_amount,
                    aps.land_size,
                    aps.total_family_members,
                    aps.no_of_earning_hands,
                    aps.ladies,
                    aps.gents,
                    aps.source_of_income,
                    aps.total_household_income,
                    aps.utility_bills,
                    aps.educational_expenses,
                    aps.medical_expenses,
                    aps.kitchen_expenses,
                    aps.monthly_savings,
                    aps.amount,
                    aps.date_of_maturity,
                    aps.other_expenses,
                    aps.total_expenses,
                    aps.other_loan,
                    aps.loan_amount AS appraisal_loan_amount,
                    aps.who_will_earn,
                    aps.earning_person_name,
                    aps.earning_person_cnic,
                    aps.economic_dealings,
                    aps.social_behaviour,
                    aps.fatal_disease,
                    aps.business_income,
                    aps.job_income,
                    aps.house_rent_income,
                    aps.other_income,
                    aps.expected_increase_in_income,
                    aps.parent,
                    aps.family_member_info,
                    aps.earning_hands_data,
                    aps.social_appraisal_address,
                    aps.house_condition,
                    aps.domestic_assets,
                    aps.live_stock_income,
                    aps.through_cultivation,
                    aps.pension,
                
                    -- Emergency Appraisal
                    ape.income_before_corona,
                    ape.income_after_corona,
                    ape.expenses_in_corona,
                    ape.house_ownership AS emergency_house_ownership,
                    ape.total_family_members AS emergency_total_family_members,
                    ape.no_of_earning_hands AS emergency_earning_hands,
                    ape.ladies AS emergency_ladies,
                    ape.gents AS emergency_gents,
                    ape.economic_dealings AS emergency_economic_dealings,
                    ape.social_behaviour AS emergency_social_behaviour,
                    ape.fatal_disease AS emergency_fatal_disease,
                    ape.emergency_appraisal_address,
                
                    -- Business Appraisal
                    apb.place_of_business,
                    apb.fixed_business_assets,
                    apb.fixed_business_assets_amount,
                    apb.running_capital,
                    apb.running_capital_amount,
                    apb.business_expenses,
                    apb.business_expenses_amount,
                    apb.new_required_assets,
                    apb.new_required_assets_amount,
                    apb.place_of_buying,
                    apb.period_of_business,
                    apb.who_are_customers,
                    apb.emp_before_loan,
                    apb.emp_after_loan,
                    apb.business_appraisal_address,
                
                    -- Housing Appraisal
                    aph.property_type,
                    aph.ownership,
                    aph.land_area,
                    aph.residential_area,
                    aph.living_duration,
                    aph.duration_type,
                    aph.no_of_rooms,
                    aph.no_of_kitchens,
                    aph.no_of_toilets,
                    aph.purchase_price,
                    aph.current_price,
                    aph.address,
                    aph.applicant_share,
                    aph.aim_share,
                    aph.housing_appraisal_address,
                
                    -- Livestock Appraisal
                    apl.animal_type,
                    apl.business_type,
                    apl.business_condition,
                    apl.business_place,
                    apl.used_land_type,
                    apl.used_land_size,
                    apl.available_amount,
                    apl.required_amount,
                    apl.new_assets,
                    apl.monthly_income,
                    apl.expected_income,
                      -- Schedule Info
                   from_unixtime (s.due_date, \"%y-%m-%d\")as due_date,
                    s.schdl_amnt,
                    s.charges_schdl_amount,
                    s.charges_schdl_amnt_tax,
                    
                    -- Recovery Info (one per schedule based on due_date)
                   
                    r.amount AS recovery_amount,
                    r.charges_amount AS recovery_charges,
                    r.receipt_no,
                    r.source,
                    from_unixtime (r.receive_date, \"%y-%m-%d\")as receive_date

                
                FROM schedules s
                INNER JOIN applications a ON s.application_id = a.id
                INNER JOIN members m ON m.id = a.member_id
                LEFT JOIN groups g ON g.id = a.group_id
                LEFT JOIN loans l ON l.application_id = a.id
                LEFT JOIN appraisals_social aps ON aps.application_id = a.id
                LEFT JOIN appraisals_business apb ON apb.application_id = a.id
                LEFT JOIN appraisals_housing aph ON aph.application_id = a.id
                LEFT JOIN appraisals_emergency ape ON ape.application_id = a.id
                LEFT JOIN appraisals_livestock apl ON apl.application_id = a.id
                LEFT JOIN recoveries r 
                    ON r.application_id = s.application_id 
                    AND r.due_date = s.due_date 
                    AND r.deleted = 0
                INNER JOIN projects p ON p.id = a.project_id
                
                WHERE a.branch_id = 706
                  AND a.deleted = 0
                ORDER BY a.id, s.due_date;
             ";

        $LoanData = $db->createCommand($loan_query)->queryAll();
        if (!empty($LoanData) && $LoanData != null) {
            foreach ($LoanData as $key => $d) {
                $array = [
                    'sanction_no' => $d['sanction_no'],
                    'project_name' => $d['project_name'],
                    'grp_no' => $d['grp_no'],
                    'member_name' => $d['full_name'],
                    'parentage' => $d['parentage'],
                    'parentage_type' => $d['parentage_type'],
                    'cnic' => $d['cnic'],
                    'gender' => $d['gender'],
                    'dob' => $d['dob'],
                    'marital_status' => $d['marital_status'],
                    'family_member_name' => $d['family_member_name'],
                    'family_member_cnic' => $d['family_member_cnic'],
                    'religion' => $d['religion'],
                    'bzns_cond' => $d['bzns_cond'],
                    'who_will_work' => $d['who_will_work'],
                    'name_of_other' => $d['name_of_other'],
                    'other_cnic' => $d['other_cnic'],
                    'req_amount' => $d['req_amount'],
                    'recommended_amount' => $d['recommended_amount'],
                    'application_status' => $d['application_status'],
                    'is_urban' => $d['is_urban'],
                    'client_contribution' => $d['client_contribution'],
                    'loan_amount' => $d['loan_amount'],
                    'disbursed_amount' => $d['disbursed_amount'],
                    'service_charges' => $d['service_charges'],
                    'loans_charges_amount' => $d['loans_charges_amount'],
                    'inst_amnt' => $d['inst_amnt'],
                    'inst_months' => $d['inst_months'],
                    'inst_type' => $d['inst_type'],
                    'date_disbursed' => $d['date_disbursed'],
                    'loan_status' => $d['loan_status'],
                    'poverty_index' => $d['poverty_index'],
                    'house_ownership' => $d['house_ownership'],
                    'house_rent_amount' => $d['house_rent_amount'],
                    'land_size' => $d['land_size'],
                    'total_family_members' => $d['total_family_members'],
                    'no_of_earning_hands' => $d['no_of_earning_hands'],
                    'ladies' => $d['ladies'],
                    'gents' => $d['gents'],
                    'source_of_income' => $d['source_of_income'],
                    'total_household_income' => $d['total_household_income'],
                    'utility_bills' => $d['utility_bills'],
                    'educational_expenses' => $d['educational_expenses'],
                    'medical_expenses' => $d['medical_expenses'],
                    'kitchen_expenses' => $d['kitchen_expenses'],
                    'monthly_savings' => $d['monthly_savings'],
                    'amount' => $d['amount'],
                    'date_of_maturity' => $d['date_of_maturity'],
                    'other_expenses' => $d['other_expenses'],
                    'total_expenses' => $d['total_expenses'],
                    'other_loan' => $d['other_loan'],
                    'appraisal_loan_amount' => $d['appraisal_loan_amount'],
                    'who_will_earn' => $d['who_will_earn'],
                    'earning_person_name' => $d['earning_person_name'],
                    'earning_person_cnic' => $d['earning_person_cnic'],
                    'economic_dealings' => $d['economic_dealings'],
                    'social_behaviour' => $d['social_behaviour'],
                    'fatal_disease' => $d['fatal_disease'],
                    'business_income' => $d['business_income'],
                    'job_income' => $d['job_income'],
                    'house_rent_income' => $d['house_rent_income'],
                    'other_income' => $d['other_income'],
                    'expected_increase_in_income' => $d['expected_increase_in_income'],
                    'parent' => $d['parent'],
                    'family_member_info' => $d['family_member_info'],
                    'earning_hands_data' => $d['earning_hands_data'],
                    'social_appraisal_address' => $d['social_appraisal_address'],
                    'house_condition' => $d['house_condition'],
                    'domestic_assets' => $d['domestic_assets'],
                    'live_stock_income' => $d['live_stock_income'],
                    'through_cultivation' => $d['through_cultivation'],
                    'pension' => $d['pension'],
                    'income_before_corona' => $d['income_before_corona'],
                    'income_after_corona' => $d['income_after_corona'],
                    'expenses_in_corona' => $d['expenses_in_corona'],
                    'emergency_house_ownership' => $d['emergency_house_ownership'],
                    'emergency_total_family_members' => $d['emergency_total_family_members'],
                    'emergency_earning_hands' => $d['emergency_earning_hands'],
                    'emergency_ladies' => $d['emergency_ladies'],
                    'emergency_gents' => $d['emergency_gents'],
                    'emergency_economic_dealings' => $d['emergency_economic_dealings'],
                    'emergency_social_behaviour' => $d['emergency_social_behaviour'],
                    'emergency_fatal_disease' => $d['emergency_fatal_disease'],
                    'emergency_appraisal_address' => $d['emergency_appraisal_address'],
                    'place_of_business' => $d['place_of_business'],
                    'fixed_business_assets' => $d['fixed_business_assets'],
                    'fixed_business_assets_amount' => $d['fixed_business_assets_amount'],
                    'running_capital' => $d['running_capital'],
                    'running_capital_amount' => $d['running_capital_amount'],
                    'business_expenses' => $d['business_expenses'],
                    'business_expenses_amount' => $d['business_expenses_amount'],
                    'new_required_assets' => $d['new_required_assets'],
                    'new_required_assets_amount' => $d['new_required_assets_amount'],
                    'place_of_buying' => $d['place_of_buying'],
                    'period_of_business' => $d['period_of_business'],
                    'who_are_customers' => $d['who_are_customers'],
                    'emp_before_loan' => $d['emp_before_loan'],
                    'emp_after_loan' => $d['emp_after_loan'],
                    'business_appraisal_address' => $d['business_appraisal_address'],
                    'property_type' => $d['property_type'],
                    'ownership' => $d['ownership'],
                    'land_area' => $d['land_area'],
                    'residential_area' => $d['residential_area'],
                    'living_duration' => $d['living_duration'],
                    'duration_type' => $d['duration_type'],
                    'no_of_rooms' => $d['no_of_rooms'],
                    'no_of_kitchens' => $d['no_of_kitchens'],
                    'no_of_toilets' => $d['no_of_toilets'],
                    'purchase_price' => $d['purchase_price'],
                    'current_price' => $d['current_price'],
                    'address' => $d['address'],
                    'applicant_share' => $d['applicant_share'],
                    'aim_share' => $d['aim_share'],
                    'housing_appraisal_address' => $d['housing_appraisal_address'],
                    'animal_type' => $d['animal_type'],
                    'business_type' => $d['business_type'],
                    'business_condition' => $d['business_condition'],
                    'business_place' => $d['business_place'],
                    'used_land_type' => $d['used_land_type'],
                    'used_land_size' => $d['used_land_size'],
                    'available_amount' => $d['available_amount'],
                    'required_amount' => $d['required_amount'],
                    'new_assets' => $d['new_assets'],
                    'monthly_income' => $d['monthly_income'],
                    'expected_income' => $d['expected_income'],
                    'due_date' => $d['due_date'],
                    'schdl_amnt' => $d['schdl_amnt'],
                    'charges_schdl_amount' => $d['charges_schdl_amount'],
                    'charges_schdl_amnt_tax' => $d['charges_schdl_amnt_tax'],
                    'recovery_amount' => $d['recovery_amount'],
                    'recovery_charges' => $d['recovery_charges'],
                    'receipt_no' => $d['receipt_no'],
                    'source' => $d['source'],
                    'receive_date' => $d['receive_date'],
                ];
                fputcsv($fopenW, $array);
            }
        }

    }

}