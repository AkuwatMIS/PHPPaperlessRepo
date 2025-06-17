<?php

namespace console\controllers;

use common\components\Helpers\AblDisbursementHelper;
use common\components\Helpers\AcagHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\StructureHelper;
use common\models\Actions;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Awp;
use common\models\AwpOverdue;
use common\models\Branches;
use common\models\BranchProjectsMapping;
use common\models\Donations;
use common\models\DynamicReports;
use common\models\Images;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\MemberInfo;
use common\models\Members;
use common\models\ProgressReports;
use common\models\ProjectProductMapping;
use common\models\Projects;
use common\models\Recoveries;
use common\models\Schedules;
use common\models\Users;
use common\models\Visits;
use frontend\modules\branch\Branch;
use Ratchet\App;
use Yii;
use yii\web\NotFoundHttpException;
use yii\console\Controller;


class GeneralInfoController extends Controller
{


    public function actionMemberWithOneLoan()
    {
        $db = Yii::$app->db;
        $loan_query = "SELECT member_id,COUNT(lon.id)as c FROM  loans lon inner JOIN applications app ON app.id=lon.application_id
                        WHERE
                        lon.status='collected' OR lon.status='loan completed'
                        GROUP BY member_id
                        HAVING c=1";
        $data = $db->createCommand($loan_query)->execute();
        $filename = "member_loan_count.csv";
        $fp = fopen('php://output', 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        fputcsv($fp, array('data' => $data));
        exit;
    }

    public function actionMemberPortfolio()
    {
        ini_set('memory_limit', '204878M');
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT
    mem.cnic,    
    mem.dob,
    mem.full_name,
    mem.parentage,
    mem.gender,
    mem.education,
    mem.marital_status,
    (SELECT addr.address FROM members_address addr WHERE addr.member_id=mem.id AND addr.is_current=1 ORDER BY addr.id LIMIT 1 ) address, 
    regions.name region,
    areas.name area,
    branches.name as branch,
    projects.name project,
    app.req_amount,
   (SELECT cite.name FROM cities cite INNER JOIN branches brc ON brc.city_id = cite.id WHERE brc.id=mem.branch_id ORDER BY cite.id LIMIT 1 ) city
FROM
    applications app
    
INNER JOIN
    members mem
ON
    mem.id = app.member_id
INNER JOIN
    regions
ON
    regions.id = mem.region_id
INNER JOIN
    areas
ON
    areas.id = mem.area_id
INNER JOIN
    branches
ON
    branches.id = mem.branch_id
INNER JOIN
    projects
ON
    projects.id = app.project_id
WHERE
    mem.cnic IN(
'21301-5160850-9',
'31301-3694549-7',
'31202-7520184-5'
    )
    GROUP BY mem.id");
        $data = $command->queryAll();
        $filename = "member_loan_portfolio.csv";
        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopen = fopen($filepath, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        $createColumn = array("CNIC", "DateOfBirth", "Full Name", "Parentage", "Gender", "Education", "Marital Status", "Address", "Region", "Area", "Branch", "Project", "Loan Amount", "City");
        fputcsv($fopen, $createColumn);
        foreach ($data as $d) {
            fputcsv($fopen, $d);
        }
        fclose($fopen);
        exit;
    }

    public function actionAgeWiseLoanCount()
    {
        ini_set('memory_limit', '204878M');
        $age_limit_18_24 = 0;
        $age_limit_25_34 = 0;
        $age_limit_35_44 = 0;
        $age_limit_45_60 = 0;

        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT
            SUM(IF(DATE_FORMAT(FROM_UNIXTIME(1605542824), '%Y')-DATE_FORMAT(FROM_UNIXTIME(IF((a.dob) < 0 ,(-1 * (a.dob)),a.dob)), '%Y') < 20,1,0)) as 'Under 20',
            SUM(IF(DATE_FORMAT(FROM_UNIXTIME(1605542824), '%Y')-DATE_FORMAT(FROM_UNIXTIME(IF((a.dob) < 0 ,(-1 * (a.dob)),a.dob)), '%Y') BETWEEN 20 and 29,1,0)) as '20 - 29',
            SUM(IF(DATE_FORMAT(FROM_UNIXTIME(1605542824), '%Y')-DATE_FORMAT(FROM_UNIXTIME(IF((a.dob) < 0 ,(-1 * (a.dob)),a.dob)), '%Y') BETWEEN 30 and 39,1,0)) as '30 - 39',
            SUM(IF(DATE_FORMAT(FROM_UNIXTIME(1605542824), '%Y')-DATE_FORMAT(FROM_UNIXTIME(IF((a.dob) < 0 ,(-1 * (a.dob)),a.dob)), '%Y') BETWEEN 40 and 49,1,0)) as '40 - 49',
            SUM(IF(DATE_FORMAT(FROM_UNIXTIME(1605542824), '%Y')-DATE_FORMAT(FROM_UNIXTIME(IF((a.dob) < 0 ,(-1 * (a.dob)),a.dob)), '%Y') BETWEEN 50 and 59,1,0)) as '50 - 59'
    FROM members a; ");

        $applications = $command->queryAll();
        echo $age_limit_18_24;

//        $members34 = Members::find()
//            ->andWhere(['between', 'dob', 343008000, 504921600])
//            ->all();
//
//        foreach ($members34 as $member){
//            $connection = Yii::$app->getDb();
//            $command = $connection->createCommand("
//                            SELECT
//                                   COUNT(lon.id) count_loan
//                            FROM
//                                applications app
//
//                            INNER JOIN
//                                loans lon
//                            ON
//                                lon.application_id = app.id
//                            WHERE
//                                app.member_id=$member->id
//                             AND  (lon.status = 'collected' OR lon.status = 'completed') ");
//
//            $applications = $command->queryAll();
//            if($applications){
//                $age_limit_25_34+=$applications[0]['count_loan'];
//            }
//        }
//        echo $age_limit_18_24;
//
//        $members44 = Members::find()
//            ->andWhere(['between', 'dob', 343008000, 189302400])
//            ->all();
//        foreach ($members44 as $member){
//            $connection = Yii::$app->getDb();
//            $command = $connection->createCommand("
//                            SELECT
//                                   COUNT(lon.id) count_loan
//                            FROM
//                                applications app
//
//                            INNER JOIN
//                                loans lon
//                            ON
//                                lon.application_id = app.id
//                            WHERE
//                                app.member_id=$member->id
//                             AND  (lon.status = 'collected' OR lon.status = 'completed') ");
//
//            $applications = $command->queryAll();
//            if($applications){
//                $age_limit_35_44+=$applications[0]['count_loan'];
//            }
//        }
//        echo $age_limit_18_24;
//
//        $members60 = Members::find()
//            ->andWhere(['between', 'dob', 343008000, -315619200])
//            ->all();
//        foreach ($members60 as $member){
//            $connection = Yii::$app->getDb();
//            $command = $connection->createCommand("
//                            SELECT
//                                   COUNT(lon.id) count_loan
//                            FROM
//                                applications app
//
//                            INNER JOIN
//                                loans lon
//                            ON
//                                lon.application_id = app.id
//                            WHERE
//                                app.member_id=$member->id
//                             AND  (lon.status = 'collected' OR lon.status = 'completed') ");
//
//            $applications = $command->queryAll();
//            if($applications){
//                $age_limit_45_60+=$applications[0]['count_loan'];
//            }
//        }
//        echo $age_limit_18_24;


    }

    public function actionEducationWiseLoanCount()
    {
        ini_set('memory_limit', '204878M');


        $educations = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '123', 'bachelor', 'illiterate', 'intermediate', 'masters', 'matric', 'middle', 'mphil', 'phd', 'primary'];
        $result = [];
        foreach ($educations as $education) {
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("
                            SELECT COUNT(lon.id) FROM loans lon 
                            INNER JOIN applications app on app.id=lon.application_id
                            INNER JOIN members mem on  mem.id=app.member_id
                            WHERE
                                (lon.status = 'collected' OR lon.status = 'completed') 
                                AND mem.education=$education                       
                  ");

            $applications = $command->queryAll();
            if ($applications) {
                $result[$education] = $applications[0]['count_loan'];
            }
            print_r($result);
            die();
        }

    }

    public function actionReligionWiseLoanCount()
    {
        ini_set('memory_limit', '204878M');


        $educations = ['0', '1', '2', '3'];
        $result = [];
        foreach ($educations as $education) {
            $connection = Yii::$app->getDb();
            $command = $connection->createCommand("
                            SELECT COUNT(lon.id) FROM loans lon 
                            INNER JOIN applications app on app.id=lon.application_id
                            INNER JOIN members mem on  mem.id=app.member_id
                            WHERE
                                (lon.status = 'collected' OR lon.status = 'completed') 
                                AND mem.education=$education                       
                  ");

            $applications = $command->queryAll();
            if ($applications) {
                $result[$education] = $applications[0]['count_loan'];
            }
            print_r($result);
            die();
        }

    }

    public function actionSingleLoan()
    {
        $connection = Yii::$app->getDb();
        $commanda = $connection->createCommand("SELECT
    COUNT(b.id) count_app
FROM
    applications a
INNER JOIN
    loans b
ON
    b.application_id = a.id
    WHERE
    (b.status='collected' or b.status='completed')
GROUP BY
    `member_id`
HAVING
    count_app = 1");
        $commandb = $connection->createCommand("SELECT
    COUNT(b.id) count_app
FROM
    applications a
INNER JOIN
    loans b
ON
    b.application_id = a.id
    WHERE
    (b.status='collected' or b.status='completed')
GROUP BY
    `member_id`
HAVING
    count_app >= 2");

        $memberA = $commanda->queryAll();
        $memberB = $commandb->queryAll();

        $result = [
            'with count 1' => count($memberA),
            'with count > 1' => count($memberB)
        ];

        print_r($result);
        die();
    }

    public function actionActivityLoan()
    {
        ini_set('memory_limit', '204878M');
        $connection = Yii::$app->getDb();

        $branches = Branches::find()->all();
        $filename = "activity_loan_portfolio.csv";
        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopen = fopen($filepath, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        $createColumn = array("activity_id", "branch_id", "branch_name", "acum_loan_amount", "loan_count");
        fputcsv($fopen, $createColumn);
        foreach ($branches as $branch) {
            $command = $connection->createCommand(
                "SELECT a.activity_id, a.branch_id, SUM(a.disbursed_amount) acum_loan_amount, COUNT(a.id) loan_count 
              FROM loans a
              INNER  JOIN  loan_tranches b ON  b.loan_id=a.id
              WHERE a.branch_id=" . $branch->id . " 
              AND
              AND ( a.status = 'collected' OR a.status = 'loan completed' ) 
              GROUP BY a.activity_id
        ");
            $query = $command->queryAll();
            foreach ($query as $d) {
                print_r($d);
                fputcsv($fopen, $d);
            }
        }
        fclose($fopen);
        exit;
    }

    public function actionRecoveryMonthWise()
    {

        $years = ['2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020'];
        $months = ['January' => '01', 'February' => '02', 'March' => '03', 'April' => '04', 'May' => '05', 'June' => '06', 'July' => '07', 'August' => '08', 'September' => '09', 'October' => '10', 'November' => '11', 'December' => '12'];
        $filename = "member_recovery_amount.csv";
        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopen = fopen($filepath, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        foreach ($years as $year) {
            $createColumn = array("YEAR", $year);
            fputcsv($fopen, $createColumn);
            $createColumn = array("YEAR", "Month", "Recovery Amount");
            fputcsv($fopen, $createColumn);
            foreach ($months as $key => $month) {
                $dt = $year . "-" . $month . "-10";
                $start_date = strtotime(date("Y-m-01 00:00:00", strtotime($dt)));
                $end_date = strtotime(date("Y-m-t 18:59:59", strtotime($dt)));

                $db = Yii::$app->db;
                $loan_query = "
                    SELECT YEAR(FROM_UNIXTIME(receive_date)) year_data,
                            MONTH( FROM_UNIXTIME(receive_date)) month_data,
                            SUM(amount) recovery_amount
                        FROM
                            recoveries a 
                            INNER JOIN loans b
                            ON b.id=a.loan_id
                        WHERE
                            b.status IN ('loan completed','collected')
                            AND a.receive_date BETWEEN $start_date AND $end_date
                            AND a.project_id=1
                            AND a.deleted=0";
//                $data = $db->createCommand($loan_query)->getRawSql();
                echo $key;
                $data = $db->createCommand($loan_query)->queryAll();
                foreach ($data as $d) {
                    fputcsv($fopen, $d);
                }

            }

        }

        fclose($fopen);
        exit;

    }

    public function actionDisbursementMonthWise()
    {

        $years = ['2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020'];
        $months = ['January' => '01', 'February' => '02', 'March' => '03', 'April' => '04', 'May' => '05', 'June' => '06', 'July' => '07', 'August' => '08', 'September' => '09', 'October' => '10', 'November' => '11', 'December' => '12'];
        $filename = "member_disbursement_amount.csv";
        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopen = fopen($filepath, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        foreach ($years as $year) {
            $createColumn = array("YEAR", $year);
            fputcsv($fopen, $createColumn);
            $createColumn = array("YEAR", "Month", "Disbursement Amount");
            fputcsv($fopen, $createColumn);
            foreach ($months as $key => $month) {
                $dt = $year . "-" . $month . "-10";
                $start_date = strtotime(date("Y-m-01 00:00:00", strtotime($dt)));
                $end_date = strtotime(date("Y-m-t 18:59:59", strtotime($dt)));

                $db = Yii::$app->db;
                $loan_query = "
                    SELECT YEAR( FROM_UNIXTIME(a.date_disbursed) ) year_data,
                        MONTH(  FROM_UNIXTIME(a.date_disbursed) ) month_data,
                        SUM(b.tranch_amount) disburse_amount
                    FROM
                        loans a
                    INNER JOIN
                        loan_tranches b
                    ON
                        b.loan_id = a.id
                    WHERE
                        a.date_disbursed BETWEEN $start_date AND $end_date
                        AND a.project_id=1
                        AND b.status=6";
//                $data = $db->createCommand($loan_query)->getRawSql();
                $data = $db->createCommand($loan_query)->queryAll();
                foreach ($data as $d) {
                    fputcsv($fopen, $d);
                }
                echo $key;
            }

        }
        fclose($fopen);
        exit;

    }

    public function actionRecoveryCheck()
    {

        $db = Yii::$app->db;
        $loan_query = "
                    SELECT  *
                        FROM  recoveries a 
                        INNER JOIN loans b 
                        ON b.id=a.loan_id
                        WHERE 
                        a.project_id != b.project_id 
                       ";
        $data = $db->createCommand($loan_query)->queryAll();
        print_r($data);
        die();


    }

    public function actionLoanCount()
    {

        $db = Yii::$app->db;
        $loan_query = "SELECT mem.cnic, 
        ( SELECT COUNT(appli.id)
           FROM
            applications appli
           INNER JOIN
            loans lo
           ON
            lo.application_id = appli.id
           WHERE
            appli.member_id = mem.id AND lo.status IN ('collected','loan completed')
        ) AS no_of_loans 
                    FROM  members mem
                    WHERE mem.cnic IN (
                  '35101-8660451-0'
                    )";
        $data = $db->createCommand($loan_query)->queryAll();
        $filename = "member_loan_portfolio.csv";
        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopen = fopen($filepath, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        $createColumn = array("CNIC", "Loans Count");
        fputcsv($fopen, $createColumn);
        foreach ($data as $d) {
            fputcsv($fopen, $d);
        }
        fclose($fopen);
        exit;
    }

    public function actionLoanData()
    {
        $db = Yii::$app->db;
        $loan_query = "SELECT b.id,a.sanction_no,b.amount,FROM_UNIXTIME(b.receive_date) received_date FROM loans a
INNER JOIN recoveries b ON b.loan_id=a.id
WHERE a.sanction_no IN (
'511-D019-03735',
'511-D019-03736') and b.deleted=0";
        $data = $db->createCommand($loan_query)->queryAll();
        $filename = "member_loan_portfolio.csv";
        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopen = fopen($filepath, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        $createColumn = array("recovery-index", "Sanction No", "Recovery Amount", "Recovery Date");
        fputcsv($fopen, $createColumn);
        foreach ($data as $d) {
            fputcsv($fopen, $d);
            echo 'done';
        }
        fclose($fopen);
        exit;
    }

    public function actionMemberAccountDetail()
    {

        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . 'member_sanction.csv';
        $myfile = fopen($filepath, "r");
        $flag = true;
        $header = 'Sanction_No,Bank_Name,Bank_Account';
        $createColumn = [];
        $header_list = explode(',', $header);
        foreach ($header_list as $header) {
            $createColumn[] = ucwords(str_replace('_', ' ', $header));
        }
        $savefilepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . 'member_account_detail.csv';
        $fopen = fopen($savefilepath, 'w');
        fputcsv($fopen, $createColumn);
        while (($fileop = fgetcsv($myfile)) !== false) {
            if ($fileop[0] != 'Sanction No') {
                $sql = "SELECT
                        a.sanction_no,
                        dbe.bank_name bank_name,
                        dbe.account_no
                    FROM
                        loans a
                    INNER JOIN
                        loan_tranches ltr
                    ON
                        ltr.loan_id = a.id
                    INNER JOIN
                        disbursement_details dbe
                    ON
                        dbe.tranche_id = ltr.id
                    WHERE
                        a.sanction_no IN($fileop[0]) AND dbe.status = 3 AND dbe.deleted = 0
                    GROUP BY
                        a.sanction_no";
                $query = Yii::$app->db->createCommand($sql);
                $data = $query->queryAll();
                $acc = $data[0]['account_no'];
                $data[0]['account_no'] = "'$acc'";
                fputcsv($fopen, $data[0]);
                echo '__';
                echo $data;
                echo '--';
                echo $acc;
                echo '__';
            }
        }
        fclose($fopen);
    }

    public function actionAwpCreate()
    {

        $branches = Branches::find()->all();
        foreach ($branches as $branch) {
            $awp = Awp::find()->where(['month' => '2021-06'])
                ->where(['region_id' => $branch->region_id])
                ->andWhere(['area_id' => $branch->area_id])
                ->andWhere(['branch_id' => $branch->id])
                ->andWhere(['project_id' => 30])
                ->one();
            if (empty($awp)) {
            } else {
                $model = new Awp();
                $model->month = '2021-06';
                $model->region_id = $branch->region_id;
                $model->area_id = $branch->area_id;
                $model->branch_id = $branch->id;
                $model->project_id = 30;
                if ($model->save()) {
                    echo '2021-05';
                    echo '<-->';
                }
            }
            echo $branch->id;
            echo '<-->';
        }
    }

    public function actionApplicationCount()
    {
        $filename = "member_app_count.csv";
        $filepath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'portfolio-member' . '/' . $filename;
        $fopen = fopen($filepath, 'w');
        $flag = true;
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        $header = array("cnic", "app_count");
        fputcsv($fopen, $header);
        $sql = "
        SELECT a.cnic,COUNT(b.id) app_count FROM members a INNER JOIN applications b ON a.id=b.member_id
            WHERE a.cnic IN (
            '82601-0570695-0'
            ) AND  b.application_date BETWEEN 1583002800 AND 1609397999 GROUP by b.member_id
        ";
        $query = Yii::$app->db->createCommand($sql);
        $data = $query->queryAll();

        foreach ($data as $d) {
            fputcsv($fopen, $d);
            echo '__';
            echo $d;
            echo '__';
        }
        fclose($fopen);
    }

    public function actionAddAwpOverDueData()
    {
        $files = ['2021-12'];
        foreach ($files as $file) {
            $filePath = ImageHelper::getAttachmentPath() . 'dynamic_reports/' . 'awp' . '/' . $file . '.csv';
            $myFile = fopen($filePath, "r");
            while (($fileOpp = fgetcsv($myFile)) !== false) {
                if ($fileOpp[0] != 'Branch Code') {
                    $branch = Branches::find()->where(['code' => $fileOpp[0]])->one();
                    $getData = AwpOverdue::find()->where(['branch_id' => $branch->id])->andWhere(['month' => "$file"])->one();

                    $getData->branch_id;
                    echo '<>';
                    $getData->month;
                    echo '<>';
                    echo '----';
                    if (!empty($getData) && $getData != null) {
                        $getData->diff_olp = $fileOpp[1];      //Total Deferred
                        $getData->def_recovered = $fileOpp[2];      //Fully Recovered
                        $getData->awp_olp = $fileOpp[3];      //1 Pending
                        $getData->write_off_amount = $fileOpp[4];      //2 Pending
                        $getData->write_off_recovered = $fileOpp[5];      //3 Pending
                        $getData->awp_active_loans = $fileOpp[6];      //No Payment
                        $getData->save();

                        echo $file[0];
                        echo '<>';
                    }
                }
            }

        }

        fclose($myFile);

    }

    public function actionPortFolioPsic()
    {
        $filePath = ImageHelper::getAttachmentPath() . 'dynamic_reports/portfolio-member/cnic_list_pisc.csv';
        $myFile = fopen($filePath, "r");

        $filepathW = ImageHelper::getAttachmentPath() . 'dynamic_reports/portfolio-member/member_loan_portfolio.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'member_loan_portfolio.csv');
        $createColumn = array("CNIC", "Loans Count");
        fputcsv($fopenW, $createColumn);
        while (($fileOpp = fgetcsv($myFile)) !== false) {
            if ($fileOpp[0] != 'CNIC') {
                $cnic = "'$fileOpp[0]'";
                $db = Yii::$app->db;
                $loan_query = "SELECT mem.cnic, 
                    ( SELECT COUNT(app.id)
                       FROM
                        applications app
                       INNER JOIN
                        loans lo
                       ON
                        lo.application_id = app.id
                       WHERE
                        app.member_id = mem.id AND lo.status IN ('collected','loan completed')
                        AND lo.project_id=1
                         AND lo.date_disbursed BETWEEN 1593586800 AND 1609397999
                    ) AS no_of_loans 
                    FROM  members mem
                    WHERE mem.cnic = $cnic";
                $data = $db->createCommand($loan_query)->queryAll();
                if (!empty($data) && $data != null) {
                    foreach ($data as $d) {
                        fputcsv($fopenW, $d);
                    }
                    print_r($data);
                    echo '<--->';
                }
            }
        }

        fclose($fopenW);
        fclose($myFile);

    }

    public function actionInserDataProjectPsic()
    {
        $array = ['4839491',
            '4839597',
            '4262896',
        ];

        foreach ($array as $a) {
            $db = Yii::$app->db;
            $loan_query = "INSERT INTO `projects_psic`(`application_id`, `diploma_holder`, `institute`,`assigned_to`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted`) 
                VALUES ($a,0,'psdf',5507,5507,0,1611209972,1611209972,0)";
            $db->createCommand($loan_query)->execute();
        }
    }

    public function actionExportMisData()
    {
        $filepathW = ImageHelper::getAttachmentPath() . 'dynamic_reports/portfolio-member/member_loan_portfolio.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'member_loan_portfolio.csv');
        $createColumn = array("App No", "CNIC", "Date Disbursed", "Loan id");
        fputcsv($fopenW, $createColumn);
        $db = Yii::$app->db;
        $sanctions = array('36302-4001820-9',
            '21203-7281872-3',
        );
        foreach ($sanctions as $key => $sanction) {
            $loan_query = "SELECT b.sanction_no,a.bzns_cond FROM applications a 
                                INNER JOIN loans b on b.application_id=a.id
                                WHERE b.sanction_no='$sanction'";
            $data = $db->createCommand($loan_query)->queryAll();
            if (!empty($data) && $data != null) {
                foreach ($data as $d) {
                    fputcsv($fopenW, $d);
                }
                print_r($sanction);
                echo '<--->';
            }
        }


        fclose($fopenW);

    }


    public function actionUpdateLoan()
    {
        $filePath = ImageHelper::getAttachmentPath() . 'dynamic_reports/awp/loan-update.csv';
        $myFile = fopen($filePath, "r");
        while (($fileOpp = fgetcsv($myFile)) !== false) {
            if ($fileOpp[0] != 'id') {
                $id = $fileOpp[0];
                $amount = $fileOpp[1];

                $loan = Loans::find()->where(['id' => $id])->one();
                $loan->loan_amount = $amount;
                $loan->disbursed_amount = $amount;
                $loan->save();
                echo $loan->id;
            }
        }
        fclose($myFile);

    }

    public function actionRecoverySum()
    {
        $filepathW = ImageHelper::getAttachmentPath() . 'dynamic_reports/gene_info/member_recovery_sum.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'member_recovery_sum.csv');
        $createColumn = array("province_id", "Members Count", "recovery Sum");
        fputcsv($fopenW, $createColumn);

        $db = Yii::$app->db;
        $loan_query = "SELECT brc.province_id, COUNT(mem.id) member_count, SUM(rec.amount) recovery_sum FROM recoveries rec
                    INNER JOIN applications app ON app.id=rec.application_id
                    INNER JOIN members mem ON mem.id=app.member_id
                    INNER JOIN branches brc ON  brc.id=mem.branch_id
                    WHERE rec.receive_date BETWEEN 1585699200 AND 1617191999
                    AND rec.deleted=0
                    GROUP BY mem.id";
        $recoveryCount = $db->createCommand($loan_query)->queryAll();
        if (!empty($recoveryCount) && $recoveryCount != null) {
            foreach ($recoveryCount as $d) {
                fputcsv($fopenW, $d);
                print_r($d);
                echo '<--->';
            }


        }


    }

//       AND rec.project_id=52

    public function actionCibResponseFile()
    {
        //heyyy
    }


    public function actionProjectBranchMapping()
    {
        //$branches =  Branches::find()->select('id')->where(['province_id'=>1])->asArray()->all();
        $db = Yii::$app->db;
        $branches = "SELECT id from branches where province_id = 1";
        $branches = $db->createCommand($branches)->queryAll();
        if (!empty($branches)) {
            foreach ($branches as $b) {

                $model = new BranchProjectsMapping();
                $model->account_id = 0;
                $model->branch_id = $b['id'];
                $model->project_id = '76';
                $model->assigned_to = 0;
                $model->created_by = 1;
                if ($model->save()) {
                    echo 'success';
                } else {
                    print_r($model->getErrors());
                }
            }
        }
    }


    public function actionBranchProjectMapping()
    {
        $b_ids = [];

        foreach ($b_ids as $id) {
            $bpm = new BranchProjectsMapping();
            $bpm->project_id = 69;
            $bpm->branch_id = $id;
            $bpm->account_id = 1;
            $bpm->assigned_to = 1;
            $bpm->created_by = 1;
            $bpm->updated_by = 1;
            $bpm->save();
        }


        $b_ids_n = [];

        foreach ($b_ids_n as $id) {
            $bpm = new BranchProjectsMapping();
            $bpm->project_id = 66;
            $bpm->branch_id = $id;
            $bpm->account_id = 1;
            $bpm->assigned_to = 1;
            $bpm->created_by = 1;
            $bpm->updated_by = 1;
            $bpm->save();
        }
    }


    public function actionHousingAppraisals()
    {
        $app_id = [];

        foreach ($app_id as $id) {
            $bpm = new ApplicationActions();
            $bpm->parent_id = $id;
            $bpm->user_id = 1;
            $bpm->action = 'housing_appraisal';
            $bpm->created_by = 0;
            $bpm->save();
        }
    }


    public function actionFileData()
    {
        $applications = Applications::find()
            ->join('inner join', 'application_actions', 'application_actions.parent_id=applications.id')
            ->join('inner join', 'group_actions', 'group_actions.parent_id=applications.group_id')
            ->leftJoin('loans', 'loans.application_id = applications.id')
            ->where(['application_actions.action' => 'group_formation', 'application_actions.status' => 1])
            ->andWhere(['applications.status' => 'approved'])
            ->andWhere(['in', 'applications.project_id', StructureHelper::trancheProjects()])
            ->andWhere(['<>', 'applications.group_id', 0])
            ->andWhere(['<>', 'applications.recommended_amount', 0])
            ->andWhere(['=', 'applications.branch_id', 7])
            ->orderBy('applications.updated_at desc')
            ->all();
        echo '<pre>';
        print_r($applications);
        die();
    }

//  php yii general-info/sanctions-recovery-data

    public function actionSanctionsRecoveryData()
    {

        $filepathW = ImageHelper::getAttachmentPath() . 'dynamic_reports/gene_info/member_recovery.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'member_recovery.csv');
        $createColumn = array("sanction no", "recovery amount", "recovery date");
        fputcsv($fopenW, $createColumn);

        $sanctionArray = [];
        $recoveryArray = [];
        $resultArray = [];
        foreach ($sanctionArray as $key => $sanction) {
            $loan = Loans::find()->where(['sanction_no' => $sanction])->one();
            if (!empty($loan) && $loan != null) {
                $recovery = Recoveries::find()->where(['loan_id' => $loan->id, 'deleted' => 0])->all();
                if (!empty($recovery) && $recovery != null) {
                    foreach ($recovery as $rKey => $r) {
                        $recoveryArray[$key][$rKey]['sanction_no'] = $sanction;
                        $recoveryArray[$key][$rKey]['amount'] = $r->amount;
                        $recoveryArray[$key][$rKey]['receive_date'] = date('Y-m-d', $r->receive_date);
                    }
                }
            }
        }
        foreach ($recoveryArray as $recoveries) {
            foreach ($recoveries as $rec) {
                $resultArray[] = $rec;
            }
        }

        if (!empty($resultArray) && $resultArray != null) {
            foreach ($resultArray as $d) {
                fputcsv($fopenW, $d);
                print_r($d['sanction_no']);
                echo '<--->';
            }
        }
    }

    public function actionLoanRecoveryData()
    {
        $filepathW = ImageHelper::getAttachmentPath() . 'dynamic_reports/gene_info/member_applications.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'member_applications_data.csv');
        $createColumn = array("Application no", "application date", "status");
        fputcsv($fopenW, $createColumn);

        $resultArray = [];
        $application_query = Applications::find()->select(['application_no', 'application_date', 'status'])->where(['between', 'application_date', 1606824000, 1638273599])->all();
        foreach ($application_query as $key => $application) {
            if (!empty($application) && $application != null) {
                $resultArray[$key]['application_no'] = $application->application_no;
                $resultArray[$key]['application_date'] = $application->application_date;
                $resultArray[$key]['status'] = $application->status;
            }
        }

        if (!empty($resultArray) && $resultArray != null) {
            foreach ($resultArray as $d) {
                fputcsv($fopenW, $d);
            }
        }
    }

// php yii general-info/project-product-mapping

    public function actionProjectProductMapping()
    {
        $projects = Projects::find()->where(['not in', 'id', [1, 52, 61, 62, 64, 67, 76, 77, 83, 90]])->all();
        foreach ($projects as $project) {
            $productIds = [13, 14, 15, 16];
            foreach ($productIds as $id) {
                $model = new ProjectProductMapping();
                $model->project_id = $project->id;
                $model->product_id = $id;
                if ($model->save()) {

                } else {
                    var_dump($model->getErrors());
                    die();
                }
            }
        }
    }

    public function actionExportData()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);
        $branches = [
            256, 275, 338, 248, 779, 560, 221, 45, 541, 230, 252, 773, 809, 583, 185, 276, 229, 311, 806, 566, 323,
            132, 542, 300, 289, 220, 447, 822, 10, 344, 820, 245, 801, 563, 825, 3, 612, 232, 278, 274, 490,
            87, 218, 236, 734, 459, 238, 67, 458, 240,
        ];
        $overall_data = [];
        $all_data = [];

        $header = ['Name', 'CNIC', 'Parentage', 'Gender', 'District', 'Branch Code', 'Project',
            'Product', 'Purpose', 'Date Disbursed', 'Sanction No', 'Loan Amount', 'No of Installments',
            'Installment Amount', 'Inst Type', 'Expiry Date', 'balance',
            'house_ownership', 'house_rent_amount', 'land_size', 'total_family_members',
            'no_of_earning_hands', 'ladies', 'gents', 'source_of_income',
            'total_household_income', 'utility_bills', 'educational_expenses', 'medical_expenses',
            'kitchen_expenses', 'monthly_savings', 'job_income', 'house_rent_income', 'other_income',
            'house_condition', 'economic_dealings', 'social_behaviour', 'total_expenses', 'place_of_business',
            'fixed_business_assets', 'fixed_business_assets_amount', 'running_capital', 'running_capital_amount',
            'business_expenses', 'business_expenses_amount', 'new_required_assets', 'new_required_assets_amount',
            'marital_status', 'religion', 'education', 'dob', 'mobile_no', 'address'
        ];
        //foreach ($branches as $b){
        $data = [];
        $sql = "select m.full_name as name,m.cnic,m.parentage,m.gender,d.name as district,b.code as branch_code,p.name as project,pr.name as product,act.name as purpose,
from_unixtime(l.date_disbursed, '%Y-%m-%d') as date_disbursed,l.sanction_no,l.loan_amount,l.inst_months,l.inst_amnt,l.inst_type,
from_unixtime(l.loan_expiry, '%Y-%m-%d') as loan_expiry,
l.balance,
sa.house_ownership,
sa.house_rent_amount,
sa.land_size,
sa.total_family_members,
sa.no_of_earning_hands,
sa.ladies,
sa.gents,
sa.source_of_income,
sa.total_household_income,
sa.utility_bills,
sa.educational_expenses,
sa.medical_expenses,
sa.kitchen_expenses,
sa.monthly_savings,
sa.job_income,
sa.house_rent_income,
sa.other_income,
sa.house_condition,
sa.economic_dealings,
sa.social_behaviour,
sa.total_expenses,
ba.place_of_business,
ba.fixed_business_assets,
ba.fixed_business_assets_amount,
ba.running_capital,
ba.running_capital_amount,
ba.business_expenses,
ba.business_expenses_amount,
ba.new_required_assets,
ba.new_required_assets_amount,
m.marital_status,
m.religion,
m.education,
from_unixtime(m.dob, \"%m/%d/%Y\") as dob,
(select phone from members_phone where is_current=1 and member_id=m.id and phone_type=\"Mobile\" ORDER by id DESC limit 1) as mobile,
(select address from members_address where is_current=1 and member_id=m.id and address_type=\"home\" limit 1) as address
from loans l
inner join applications a on a.id=l.application_id
inner join members m on m.id=a.member_id
inner join appraisals_social sa on sa.application_id=a.id
inner join appraisals_business ba on ba.application_id=a.id
inner join branches b on b.id=l.branch_id
inner join districts d on b.district_id=d.id
inner join projects p on p.id=l.project_id
INNER join products pr on pr.id=l.product_id
INNER join activities act on act.id=l.activity_id
where 1 and l.status='collected' and l.deleted=0
";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $all_data = array_merge($all_data, $data);
        //}

        $file_name_due_aging = 'export_data' . '_' . date('d-m-Y-H-i-s') . '.csv';
        $file_path_due_aging = ImageHelper::getAttachmentPath() . '/' . $file_name_due_aging;
        //$file_path_due_aging = 'D:/wamp/www/paperless/frontend/web/uploads/export'. '/' . $file_name_due_aging;
        $fopen = fopen($file_path_due_aging, 'w');

        fputcsv($fopen, $header);
        foreach ($all_data as $g) {
            fputcsv($fopen, $g);
        }

    }

    public function actionExportDataActivity()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);
        $overall_data = [];
        $all_data = [];

        $header = ['Sanction No', 'Loan Amount', 'loan status', 'Recovery', 'balance', 'Branch Code', 'Branch Id', 'place_of_business',
            'fixed_business_assets', 'fixed_business_assets_amount', 'running_capital', 'running_capital_amount',
            'business_expenses', 'business_expenses_amount', 'new_required_assets', 'new_required_assets_amount',
        ];
        //foreach ($branches as $b){
        $data = [];
        $sql = "select l.sanction_no,l.loan_amount,l.status,
(select sum(recoveries.amount) from recoveries where recoveries.loan_id=l.id and recoveries.deleted=0) as recovery,
l.balance,b.code,b.id,
ba.place_of_business,
ba.fixed_business_assets,
ba.fixed_business_assets_amount,
ba.running_capital,
ba.running_capital_amount,
ba.business_expenses,
ba.business_expenses_amount,
ba.new_required_assets,
ba.new_required_assets_amount
from loans l
left join appraisals_business ba on ba.application_id=l.application_id
inner join branches b on b.id=l.branch_id
where 1 and l.status in ('collected','loan completed') and l.deleted=0 and l.activity_id=35
";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $all_data = array_merge($all_data, $data);
        //}

        $file_name_due_aging = 'export_data-vendor' . '_' . date('d-m-Y-H-i-s') . '.csv';
        $file_path_due_aging = ImageHelper::getAttachmentPath() . '/' . $file_name_due_aging;
        //$file_path_due_aging = 'D:/wamp/www/paperless/frontend/web/uploads/export'. '/' . $file_name_due_aging;
        $fopen = fopen($file_path_due_aging, 'w');

        fputcsv($fopen, $header);
        foreach ($all_data as $g) {
            fputcsv($fopen, $g);
        }

    }

    public function actionExportDataActivity1()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);
        $overall_data = [];
        $all_data = [];

        $header = ['Sanction No', 'Loan Amount', 'loan status'
        ];
        //foreach ($branches as $b){
        $data = [];
        $sql = "select l.sanction_no,l.loan_amount,l.status,
(select sum(recoveries.amount) from recoveries where recoveries.loan_id=l.id and recoveries.deleted=0) as recovery
from loans l
where 1 and l.status in ('collected','loan completed') and l.deleted=0 and l.activity_id=35
";
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $all_data = array_merge($all_data, $data);
        //}

        $file_name_due_aging = 'export_data-vendor' . '_' . date('d-m-Y-H-i-s') . '.csv';
        $file_path_due_aging = ImageHelper::getAttachmentPath() . '/' . $file_name_due_aging;
        //$file_path_due_aging = 'D:/wamp/www/paperless/frontend/web/uploads/export'. '/' . $file_name_due_aging;
        $fopen = fopen($file_path_due_aging, 'w');

        fputcsv($fopen, $header);
        foreach ($all_data as $g) {
            fputcsv($fopen, $g);
        }

    }


//  php yii general-info/recovery-export

    public function actionRecoveryExport()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/jordan_recovery_date.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/jordan_recovery_date.csv');
        $createColumn = ['Sanction No', 'Name', 'CNIC', 'Jan-22-date', 'Jan-22-amount', 'Feb-22-date', 'Feb-22-amount'];
        fputcsv($fopenW, $createColumn);
        $cnic = [];

        foreach ($cnic as $c) {
            $sql = "SELECT
             a.id,
            a.sanction_no,
            m.full_name,
            m.cnic
        FROM
            loans a
            INNER JOIN applications b on b.id=a.application_id
            INNER JOIN members m on m.id=b.member_id
        WHERE
            a.sanction_no='$c'";
            $query = Yii::$app->db->createCommand($sql);
            $resultQuery = $query->queryAll();
            if (!empty($resultQuery[0]) && $resultQuery[0] != null) {
                $arrayResult = [];
                $loanId = $resultQuery[0]['id'];

                $arrayResult['sanction_no'] = $resultQuery[0]['sanction_no'];
                $arrayResult['full_name'] = $resultQuery[0]['full_name'];
                $arrayResult['cnic'] = $resultQuery[0]['cnic'];

                $dateArray = ['2022-01', '2022-02'];
                foreach ($dateArray as $date) {
                    $date1 = date('Y-m-01', strtotime($date));
                    $date2 = date('Y-m-t', strtotime($date));
                    $dateFrom = strtotime($date1);
                    $dateTo = strtotime($date2);

                    $sqlRecovery = "SELECT  FROM_UNIXTIME(`receive_date`) receive_date,`amount` FROM `recoveries` WHERE `loan_id` = $loanId AND `receive_date`>=$dateFrom AND `receive_date`<=$dateTo";
                    $queryRecovery = Yii::$app->db->createCommand($sqlRecovery);
                    $resultQueryRecovery = $queryRecovery->queryAll();
                    if (!empty($resultQueryRecovery) && $resultQueryRecovery != null) {
                        $arrayResult[$date . '-date'] = $resultQueryRecovery[0]['receive_date'];
                        $arrayResult[$date . '-amount'] = $resultQueryRecovery[0]['amount'];
                    } else {
                        $arrayResult[$date] = 0;
                    }
                }

                fputcsv($fopenW, $arrayResult);
            }
        }

    }

// php yii general-info/mdp-export

    public function actionMdpExport()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);

        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/jordan_donation_date.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'kpp_reports/jordan_donation_date.csv');
        $createColumn = ['Sanction No', 'Name', 'CNIC', 'Jan-21', 'Feb-21', 'Mar-21', 'Apr-21', 'May-21', 'Jun-21', 'Jul-21', 'Aug-21', 'Sep-21', 'Oct-21', 'Nov-21', 'Dec-21', 'Jan-22', 'Feb-22'];
        fputcsv($fopenW, $createColumn);
        $cnic = [];

        foreach ($cnic as $c) {
            $sql = "SELECT
             a.id,
            a.sanction_no,
            m.full_name,
            m.cnic
        FROM
            loans a
            INNER JOIN applications b on b.id=a.application_id
            INNER JOIN members m on m.id=b.member_id
        WHERE
            a.sanction_no='$c'";
            $query = Yii::$app->db->createCommand($sql);
            $resultQuery = $query->queryAll();
            if (!empty($resultQuery[0]) && $resultQuery[0] != null) {
                $arrayResult = [];
                $loanId = $resultQuery[0]['id'];

                $arrayResult['sanction_no'] = $resultQuery[0]['sanction_no'];
                $arrayResult['full_name'] = $resultQuery[0]['full_name'];
                $arrayResult['cnic'] = $resultQuery[0]['cnic'];

                $dateArray = ['2021-01', '2021-02', '2021-03', '2021-04', '2021-05', '2021-06', '2021-07', '2021-08', '2021-09', '2021-10', '2021-11', '2021-12', '2022-01', '2022-02'];
                foreach ($dateArray as $date) {
                    $date1 = date('Y-m-01', strtotime($date));
                    $date2 = date('Y-m-t', strtotime($date));
                    $dateFrom = strtotime($date1);
                    $dateTo = strtotime($date2);

                    $sqlDonations = "SELECT  FROM_UNIXTIME(`receive_date`) receive_date,`amount` FROM `donations` WHERE `loan_id` = $loanId AND `receive_date`>=$dateFrom AND `receive_date`<=$dateTo";
                    $queryDonations = Yii::$app->db->createCommand($sqlDonations);
                    $resultQueryDonations = $queryDonations->queryAll();
                    if (!empty($resultQueryDonations) && $resultQueryDonations != null) {
                        $arrayResult[$date] = $resultQueryDonations[0]['amount'];
                    } else {
                        $arrayResult[$date] = 0;
                    }
                }

                fputcsv($fopenW, $arrayResult);
            }
        }

    }

// php yii general-info/appraisals-business
    public function actionAppraisalsBusiness()
    {
        $cnic = [];

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);
        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/appraisals_business_jordan.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'appraisals_business_jordan.csv');

        $createColumn = array("Sanction No", "Name", "CNIC", "application_id", "place_of_business", "fixed_business_assets_amount",
            "running_capital", "running_capital_amount", "business_expenses",
            "business_expenses_amount", "new_required_assets", "new_required_assets_amount",
            "who_are_customers");
        fputcsv($fopenW, $createColumn);

        foreach ($cnic as $c) {
            $db = Yii::$app->db;
            $loan_query = "SELECT
                loans.sanction_no,
                members.full_name,
                members.cnic,
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
            WHERE
                loans.sanction_no='$c'
                ";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount[0]) && $recoveryCount[0] != null) {
                fputcsv($fopenW, $recoveryCount[0]);
            }

        }


    }

// php yii general-info/appraisals-social
    public function actionAppraisalsSocial()
    {
        $cnic = [];

        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);
        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/appraisals_social_jordan.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'appraisals_social_jordan.csv');
        $createColumn = array("Sanction No", "Name", "CNIC", "application_id", "poverty_index", "house_ownership", "house_rent_amount",
            "land_size", "total_family_members", "ladies", "gents", "source_of_income", "total_household_income",
            "utility_bills", "educational_expenses", "medical_expenses", "kitchen_expenses", "monthly_savings", "month_saving_amount", "other_expenses"
        , "total_expenses", "economic_dealings", "social_behaviour", "fatal_disease", "house_condition", "description");
        fputcsv($fopenW, $createColumn);

        foreach ($cnic as $c) {
            $db = Yii::$app->db;
            $loan_query = "SELECT
                loans.sanction_no,
                members.full_name,
                members.cnic,
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
            WHERE
                 loans.sanction_no='$c'
            ";
            $recoveryCount = $db->createCommand($loan_query)->queryAll();
            if (!empty($recoveryCount[0]) && $recoveryCount[0] != null) {
                fputcsv($fopenW, $recoveryCount[0]);
            }
        }


    }

    // php yii general-info/update-loan-amount

    public function actionUpdateLoanAmount()
    {
        $loan_ids = [];

        foreach ($loan_ids as $id) {
            $loans_tranches_amount = LoanTranches::find()->where(['loan_id' => $id])->andWhere(['status' => 6])->sum('tranch_amount');
            if (!empty($loans_tranches_amount)) {
                $loan = Loans::find()->where(['id' => $id])->one();
                if (!empty($loan) && $loan != null) {
                    $loan->disbursed_amount = $loans_tranches_amount;
                    if ($loan->save(false)) {
                        echo 'saved amount';
                        echo $loans_tranches_amount;
                        echo '<--->';
                    } else {
                        var_dump($loan->getErrors());
                        die();
                    }
                }
            }
        }
    }


    // php yii general-info/active-member-null-doc
    public function actionActiveMemberNullDoc()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);
        $filepathW = ImageHelper::getAttachmentPath() . 'kpp_reports/mem_data.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . 'mem_data.csv');
        $createColumn = array("Cnic", "Sanction No", "CNIC Expiry");
        fputcsv($fopenW, $createColumn);
        $db = Yii::$app->db;
        $member_query = "SELECT
               mem.id as mem_id,
               mem.cnic,
               lon.sanction_no,
               minfo.cnic_expiry_date
            FROM
                applications app
                inner join loans lon on lon.application_id=app.id
                inner join members mem on mem.id=app.member_id
                inner join member_info minfo on mem.id=minfo.member_id
                WHERE
                lon.status in('collected') GROUP BY lon.sanction_no
            ";
        $data = $db->createCommand($member_query)->queryAll();

        foreach ($data as $d) {
            $memImg = Images::find()->where(['parent_type' => 'members'])
                ->andWhere(['parent_id' => $d['mem_id']])
                ->andWhere(['image_type' => 'nadra_document'])
                ->one();

            if (empty($memImg) && $memImg == null) {
                $dataResponse = [];
                $dataResponse['cnic'] = $d['cnic'];
                $dataResponse['sanction_no'] = $d['sanction_no'];
                $dataResponse['cnic_expiry_date'] = $d['cnic_expiry_date'];

                fputcsv($fopenW, $dataResponse);
            } else {
                echo '----------------------------' . $d['cnic'] . '-----------------------------------';
            }

        }

    }

    // php yii general-info/loan-readjust
    public function actionLoanReadjust()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 2000);
        $sanctionNos = [];

        foreach ($sanctionNos as $sanction) {
            $loan = Loans::find()->where(['sanction_no' => $sanction])->one();
            if (!empty($loan) && $loan != null) {
                $years = round($loan->inst_months / 12);
                $charges = ($loan->loan_amount * (4.49) * $years) / 100;
                $inst_amnt = (round($loan->loan_amount / $loan->inst_months) / 100) * 100;

                $loan->service_charges = $charges;
                $loan->inst_amnt = $inst_amnt;
                if ($loan->save()) {
                    FixesHelper::ledger_regenerate($loan);
                    echo 'success';
                }
            }


        }
    }

    // php yii general-info/acag-loan-trigger
    public function actionAcagLoanTrigger()
    {
        ini_set('memory_limit', '20878M');
        ini_set('max_execution_time', 2000);

        $loans = Loans::find()
            ->where(['project_id' => 132])
            ->all();
        if (!empty($loans) && $loans != null) {

            foreach ($loans as $loan) {

//                $trancheDisbursed = LoanTranches::find()
//                    ->innerJoin('disbursement_details','disbursement_details.tranche_id=loan_tranches.id')
//                    ->where(['loan_tranches.tranch_no'=>1])
//                    ->andWhere(['not in','loan_tranches.status',[6]])
//                    ->andWhere(['not in','disbursement_details.status',[2,3]])
//                    ->one();

                if ($loan->status == 'collected') {
                    $status = 'Loan Disbursed';
                    $statusReason = 'Loan Disbursed';
                    $disbursedAmount = $loan->disbursed_amount;

                } elseif ($loan->status == 'pending' && $loan->date_disbursed > 0) {
//                    if(!empty($trancheDisbursed) && $trancheDisbursed!=null){
                        $status = 'Loan Approved';
                        $statusReason = 'Loan Approved';
                        $disbursedAmount = null;
//                    }
                } elseif ($loan->status == 'rejected') {
                    $status = 'Loan Rejected';
                    $statusReason = $loan->reject_reason;
                    $disbursedAmount = null;

                } elseif ($loan->status == 'pending' && $loan->date_disbursed == 0) {
                    $status = 'Loan In Process';
                    $statusReason = 'Loan In Process';
                    $disbursedAmount = null;

                } elseif ($loan->status == 'not collected') {
                    $status = 'Loan In Process';
                    $statusReason = 'Loan In Process';
                    $disbursedAmount = null;
                }

                AcagHelper::actionPush($loan->application, $status, $statusReason, $disbursedAmount, date('Y-m-d'), 0, $loan);
                echo '--loan id---';
                echo $loan->id;
                echo '----';
            }

        } else {
            $applications = Applications::find()
                ->where(['project_id' => 132])
                ->andWhere(['in', 'status', ['pending', 'approved', 'rejected']])
                ->andWhere(['deleted' => 0])
                ->all();

            if (!empty($applications) && $applications != null) {
                foreach ($applications as $application) {

                    if ($application->status == 'rejected') {
                        $status = 'Loan Rejected';
                        $statusReason = $application->reject_reason;
                    } else {
                        $status = 'Submitted';
                        $statusReason = 'Submitted';
                    }
                    AcagHelper::actionPush($application, $status, $statusReason, $application->req_amount, date('Y-m-d'), 0, null);
                    echo '----app id---';
                    echo $application->id;
                    echo '----';
                }
            }

        }

    }

    // php yii general-info/acag-visit-push
    public function actionAcagVisitPush()
    {
        ini_set('memory_limit', '20878M');
        ini_set('max_execution_time', 2000);

        $apv = 0;
        // Fetch applications with project_id = 132
        $loans = Loans::find()
            ->where(['project_id' => 132])
            ->andWhere(['status' => 'collected'])
            ->all();

        foreach ($loans as $loan) {
            $visit = Visits::find()
                ->where(['parent_type' => 'application'])
                ->andWhere(['parent_id' => $loan->application_id])
                ->andWhere(['is_shifted'=> 1])
                ->andWhere(['>','shifted_verified_by', 0])
                ->one();
            if (!empty($visit) && $visit!=null) {
                if ($visit->percent > 0) {
                    AcagHelper::actionPush($loan->application, 'Visit', 'Visit', 0, date('Y-m-d'), $visit->percent, $loan);
                }
            }
        }
        echo $apv;
    }

//  php yii general-info/acag-recovery-push
    public function actionAcagRecoveryPush()
    {
        ini_set('memory_limit', '20878M');
        ini_set('max_execution_time', 2000);

        // Fetch applications with project_id = 132
        $loans = Loans::find()
            ->where(['project_id' => 132])
            ->all();

        foreach ($loans as $loan) {
            $recoverySum = Loans::getRecovery($loan->id); // Access the loan relation
            if ($recoverySum > 0) {
                AcagHelper::actionPush($loan->application, 'Recovery', 'Recovery', $recoverySum, date('Y-m-d'), 0, $loan);
            }
        }
    }

    //  php yii general-info/abl-disbursement-push
    public function actionAblDisbursementPush()
    {
        ini_set('memory_limit', '20878M');
        ini_set('max_execution_time', 2000);

        AblDisbursementHelper::actionPushDisbursement();
    }
}