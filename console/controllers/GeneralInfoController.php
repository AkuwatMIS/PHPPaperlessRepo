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

    //  php yii general-info/acag-disbursement-push
    public function actionAcagDisbursementPush()
    {
        $nicArray = ['33202-8950046-1',
            '33302-8066016-1',
            '31105-3097530-9',
            '36303-8401712-1',
            '33202-2732574-0',
            '36502-7583407-5',
            '37405-7739246-9',
            '33303-8954789-1',
            '34202-8399886-1',
            '31101-2027710-0',
            '61101-5444828-5',
            '37201-7715287-3',
            '34101-2902670-9',
            '34501-1931194-7',
            '37302-2709887-5',
            '61101-5586414-5',
            '36502-8108044-9',
            '38406-0361119-1',
            '38402-2769054-3',
            '33204-0390009-1',
            '33204-0537587-9',
            '32103-4913274-3',
            '42201-8741240-5',
            '35101-6710572-5',
            '38401-0351671-9',
            '35301-4746726-2',
            '37103-6420508-7',
            '38302-1157346-8',
            '90403-0135943-5',
            '31104-6387612-1',
            '32104-0429138-3',
            '32304-9910340-3',
            '33202-6455641-2',
            '33202-0826056-7',
            '34501-1955102-1',
            '35202-0673150-9',
            '37203-1503376-3',
            '33202-9868694-9',
            '38302-4555924-8',
            '32403-0144919-7',
            '34301-7515578-9',
            '32304-1381534-3',
            '35201-4160542-3',
            '37201-3062085-4',
            '32403-9975873-7',
            '38302-9256760-9',
            '32304-2194583-2',
            '37203-1483191-1',
            '32304-1581000-2',
            '32102-5244869-9',
            '32103-9233083-9',
            '36102-1855642-9',
            '33203-1408438-1',
            '31104-8905326-2',
            '32402-6491619-3',
            '33303-6619655-4',
            '32102-7299206-0',
            '36103-5879745-6',
            '33202-6922177-8',
            '32403-2807393-9',
            '42301-7269540-6',
            '38103-2928211-1',
            '35401-1840578-9',
            '32403-6250652-9',
            '32103-2197633-9',
            '33202-3158206-3',
            '32304-1619695-7',
            '32403-7462627-5',
            '32402-0839969-7',
            '32402-9060331-7',
            '32402-5816699-0',
            '33204-0357704-5',
            '32304-2176841-6',
            '38302-8294298-7',
            '42201-8308369-6',
            '36402-8712916-3',
            '32203-2078710-3',
            '35301-1853998-7',
            '36502-3654289-1',
            '34301-1784613-7',
            '36601-8756929-0',
            '36502-6248989-1',
            '36401-9012311-6',
            '34301-7532020-5',
            '36502-1993510-2',
            '35301-2235715-7',
            '35401-1145926-7',
            '34301-5506865-2',
            '35401-5099214-3',
            '34501-5762044-5',
            '34301-6846949-4',
            '37203-0878130-1',
            '34302-1816872-9',
            '34301-1728242-3',
            '38303-1018188-7',
            '34301-3383607-5',
            '34301-1773703-9',
            '37405-7386398-5',
            '37401-5653641-9',
            '37203-2448047-7',
            '37204-0144032-3',
            '34301-3221455-5',
            '33203-0787354-3',
            '31302-5240870-1',
            '32304-5429538-2',
            '33202-4361177-7',
            '32102-1026160-5',
            '33204-0669840-4',
            '33204-0619598-7',
            '32203-6599538-3',
            '32104-0382710-9',
            '35403-9407643-7',
            '36501-5283855-7',
            '33303-9118447-6',
            '33303-6337906-1',
            '33301-9531730-9',
            '36603-4985545-5',
            '35302-4667062-2',
            '36502-3022755-3',
            '36303-0928558-1',
            '36301-3887760-1',
            '35302-2761160-1',
            '61101-1744450-8',
            '33204-0428751-1',
            '36602-7323899-5',
            '38104-5664324-9',
            '37201-9812496-5',
            '34403-0808911-1',
            '36402-0761850-9',
            '35503-0203410-3',
            '34201-0530711-1',
            '33302-7490785-7',
            '31302-6185020-5',
            '36502-7616173-9',
            '38101-9134661-9',
            '36103-2456050-5',
            '38104-3859877-6',
            '34301-5675320-1',
            '32403-8889057-5',
            '32304-8211345-1',
            '35201-3496819-3',
            '38104-0791466-8',
            '31105-2238478-5',
            '38302-2154004-7',
            '35301-1204335-7',
            '33202-1271474-8',
            '36602-0977523-3',
            '36402-7726926-3',
            '37405-6120584-3',
            '32304-8293390-3',
            '33203-6056888-5',
            '31302-0375262-3',
            '32402-8380729-6',
            '33201-1557876-6',
            '33201-1659601-7',
            '34501-9582623-1',
            '34201-7043877-5',
            '38302-1181320-3',
            '38302-2317526-3',
            '37405-8285289-8',
            '36402-4683301-9',
            '36602-2929880-5',
            '33204-0636578-0',
            '32103-6588857-9',
            '35301-7397806-5',
            '36602-0906454-4',
            '37406-1598117-7',
            '38302-1193565-3',
            '37302-1132623-9',
            '32403-1145710-4',
            '37201-6288960-1',
            '38406-0577342-7',
            '38302-1159979-9',
            '37405-0353715-1',
            '32403-9176573-5',
            '35202-8968421-1',
            '33202-6367532-1',
            '32102-0908440-9',
            '33303-2214091-1',
            '38101-3227113-5',
            '36603-8738635-0',
            '36103-0843880-3',
            '32403-0735683-2',
            '38302-5732044-4',
            '32402-0546352-6',
            '38301-7887975-3',
            '38302-6366831-5',
            '32403-2865649-2',
            '32402-4659927-3',
            '33100-6986613-5',
            '38302-4156833-9',
            '38302-3914408-7',
            '32203-6686703-1',
            '35302-3497674-8',
            '38302-1232580-3',
            '36502-8988841-0',
            '34103-6476034-1',
            '36502-0515134-5',
            '34301-8077623-9',
            '35301-3474026-0',
            '36502-3527866-5',
            '34301-1739085-7',
            '34301-9358917-9',
            '37401-8095822-9',
            '34501-1903084-6',
            '37201-4809801-5',
            '36502-4785158-7',
            '31101-2136569-1',
            '34104-8996461-1',
            '38101-0687226-9',
            '36103-7524258-3',
            '36502-8730991-7',
            '38101-7067307-9',
            '36502-1270721-8',
            '37203-1420266-3',
            '37203-4458570-3',
            '32402-7380309-8',
            '37102-1250301-9',
            '31102-2996778-3',
            '37405-1522689-9',
            '38302-1125791-9',
            '35202-2707139-7',
            '38302-7714962-9',
            '33202-5851225-5',
            '38302-1227195-7',
            '36103-1623612-9',
            '36201-6135589-3',
            '33202-3810439-2',
            '32103-0375872-3',
            '34403-4895336-3',
            '34403-6004740-5',
            '32103-1837981-5',
            '37201-6737974-7',
            '37201-3399738-3',
            '37203-0967439-9',
            '31105-5985087-3',
            '36303-0994257-5',
            '33101-4852470-5',
            '36301-2622779-7',
            '36603-7104376-9',
            '32304-9649254-2',
            '32304-6061421-5',
            '36302-1192634-9',
            '33202-3577231-0',
            '33204-0369946-3',
            '33303-0111837-3',
            '33202-8713771-5',
            '38405-5606618-7',
            '35202-2879482-5',
            '37405-9759653-1',
            '37201-1689338-9',
            '37201-1602115-7',
            '37201-2812098-9',
            '33100-0999439-7',
            '33106-9679962-7',
            '33202-3010732-3',
            '38401-8618720-9',
            '32304-4742238-3',
            '36401-0810736-5',
            '36303-2284333-5',
            '32103-6518953-9',
            '32103-4466100-9',
            '32402-6806362-2',
            '32103-1149544-3',
            '36303-1087000-1',
            '42101-5093630-7',
            '31101-2629172-5',
            '32103-0262482-9',
            '35401-8987990-9',
            '33204-0497735-1',
            '31203-3767249-7',
            '36302-5781026-5',
            '36303-0224482-3',
            '32102-5773970-3',
            '36402-0770926-7',
            '31102-4583101-1',
            '33100-4413305-1',
            '31203-5503669-6',
            '35202-5378843-1',
            '35401-8294835-9',
            '37405-7400764-1',
            '35201-1602315-5',
            '36302-0430574-9',
            '32304-2127833-9',
            '31301-9927549-2',
            '33202-3358217-3',
            '35202-8714559-7',
            '34403-1921348-1',
            '34501-1876217-0',
            '38202-5833618-9',
            '37204-0122317-4',
            '37201-7172410-6',
            '34403-0584014-1',
            '34101-4007883-2',
            '38402-6886252-7',
            '32304-1589080-3',
            '37102-2826939-5',
            '32304-4172899-4',
            '42000-0164073-0',
            '32304-4045719-1',
            '38403-5286623-5',
            '37203-1499419-3',
            '37201-4136219-9',
            '33202-1137957-3',
            '33104-5990387-1',
            '33303-6162211-1',
            '37405-4541498-2',
            '34402-6197934-5',
            '38302-4348715-7',
            '36302-1227782-0',
            '33204-0416389-7',
            '37401-3146385-9',
            '37203-9846275-3',
            '37403-3180882-9',
            '38302-1244292-3',
            '36303-0800770-7',
            '36103-1640315-2',
            '32402-0896808-4',
            '32304-1768319-0',
            '36303-0192857-1',
            '31303-8232401-5',
            '36303-0987819-1',
            '37405-9823213-7',
            '32102-8026361-7',
            '37201-1753917-5',
            '36303-0998783-3',
            '33202-4592742-5',
            '36602-1260181-3',
            '36501-1842883-1',
            '33106-0349434-3',
            '37405-2734940-5',
            '35201-1320315-2',
            '37401-7534252-3',
            '33105-5374128-7',
            '21105-2585124-1',
            '35102-2523798-1',
            '32402-9428018-4',
            '37404-5914208-1',
            '36602-1696199-3',
            '38403-3183212-3',
            '34201-1812867-3',
            '36201-8064675-9',
            '34201-0540442-3',
            '35301-0125443-5',
            '36502-1275033-3',
            '36104-0695192-7',
            '35202-2805526-6',
            '32403-1633350-7',
            '36501-8416614-8',
            '32102-1276155-2',
            '33203-5599589-9',
            '31104-4777949-8',
            '31301-9061631-7',
            '38401-6825780-3',
            '36603-1712848-0',
            '37402-4120803-7',
            '36303-3840493-0',
            '37401-7733727-9',
            '37105-0246234-3',
            '34201-3079080-3',
            '34403-1903301-9',
            '31302-8075933-9',
            '31302-4958961-5',
            '35202-0327415-8',
            '38101-0649154-5',
            '38101-6881656-3',
            '38101-6844960-5',
            '34104-7054049-3',
            '34101-6993467-9',
            '34301-3982427-9',
            '35202-4832638-1',
            '35101-8103657-1',
            '36603-9183474-3',
            '34301-7721021-9',
            '38402-1538899-1',
            '34603-9247233-7',
            '34501-2791130-5',
            '33303-3547519-9',
            '34104-5424361-7',
            '35202-1827122-2',
            '32302-8917803-2',
            '32302-0235581-4',
            '32302-5367636-3',
            '31201-1824253-9',
            '32102-0860275-2',
            '32302-6190622-0',
            '32103-0281809-7',
            '38405-2291054-5',
            '33105-0314383-8',
            '33203-1442919-7',
            '36603-1809111-7',
            '31102-4598457-7',
            '36101-0441496-1',
            '36501-5069494-2',
            '36601-4818472-3',
            '35301-0598649-1',
            '38402-3501882-7',
            '32402-3846021-1',
            '32304-9797304-1',
            '32302-6278005-7',
            '33204-0425216-5',
            '34603-8359569-6',
            '35202-8995361-1',
            '35200-1394229-2',
            '32102-8036986-1',
            '33102-6138279-5',
            '32104-0347942-3',
            '35301-5094472-7',
            '34101-4500960-4',
            '45305-0566148-0',
            '36602-4101303-7',
            '31101-3018578-1',
            '31201-7142406-8',
            '31205-3280206-8',
            '32302-3747833-7',
            '32403-3296176-1',
            '34104-4474864-1',
            '35201-1349170-5',
            '33103-0642857-5',
            '35301-7739321-7',
            '33303-3093100-1',
            '33302-5891317-7',
            '33202-6934722-3',
            '38403-2400355-3',
            '13503-1582662-0',
            '37201-1790510-3',
            '38201-2051335-7',
            '38402-3805057-3',
            '33202-8869077-1',
            '33303-3820197-9',
            '31202-1123600-9',
            '36401-8682722-0',
            '36303-3598805-8',
            '35201-9744295-5',
            '36601-1630566-7',
            '33100-4339547-7',
            '36202-7325353-3',
            '31201-0996398-1',
            '35201-5180301-5',
            '32102-1208322-9',
            '36601-9413846-9',
            '33102-7260916-7',
            '33103-1477922-6',
            '35201-3551884-5',
            '36303-0709027-5',
            '32304-1561769-0',
            '36502-2438258-1',
            '36102-8699975-5',
            '37102-7256036-3',
            '33203-8624010-1',
            '35201-1296659-1',
            '33106-2164873-9',
            '32103-0304076-9',
            '42301-3171072-3',
            '33100-0789038-9',
            '36601-9065906-5',
            '32103-3808729-7',
            '32301-1082711-9',
            '36602-2014306-5',
            '35202-8776503-1',
            '37402-0971952-5',
            '35303-2165296-7',
            '34201-8563267-3',
            '31103-5013256-3',
            '34202-1522948-4',
            '35202-6828262-0',
            '33302-7147878-5',
            '34402-1606070-2',
            '37402-0984440-3',
            '33204-0586491-4',
            '33203-1339916-0',
            '35301-1322748-3',
            '35301-9108531-3',
            '37406-1549094-8',
            '36501-9561221-5',
            '37201-1689596-3',
            '37405-1668210-5',
            '37302-2572668-7',
            '37201-0245350-1',
            '34603-6620613-3',
            '37201-1792049-5',
            '32304-6974427-8',
            '35201-3603724-1',
            '34101-2347065-9',
            '33303-8223154-9',
            '35201-3087827-3',
            '38103-6907184-9',
            '37302-1918240-1',
            '38302-1178074-9',
            '37405-2834198-8',
            '38402-3497431-1',
            '34301-1839636-1',
            '34501-1974441-9',
            '38403-6960070-1',
            '34501-9696427-0',
            '34301-4796953-7',
            '34101-9710142-5',
            '34104-2351413-1',
            '38302-1240275-5',
            '38403-2267195-7',
            '34104-5953620-9',
            '35103-8004711-9',
            '34501-3712084-8',
            '34603-8202716-0',
            '34104-5392988-3',
            '34104-2298734-1',
            '38301-9410484-7',
            '35302-1443595-9',
            '36601-4283217-0',
            '61101-6670924-0',
            '33204-0407917-3',
            '35301-1861875-9',
            '32103-0303185-5',
            '32103-3575480-9',
            '33203-7808333-3',
            '36603-1239842-7',
            '32103-3896176-7',
            '32103-6908445-3',
            '33204-0528123-1',
            '32102-3349615-3',
            '38101-4000981-5',
            '33204-0569704-6',
            '35201-2712690-7',
            '33304-0426334-9',
            '37405-6194592-5',
            '36601-4373577-5',
            '36402-3900556-9',
            '31202-6474087-3',
            '34101-7402967-1',
            '35102-0612146-1',
            '32302-1723111-3',
            '35201-7187939-1',
            '35202-5694467-5',
            '35202-2429292-1',
            '36103-7445533-0',
            '33203-1295841-1',
            '34101-2732156-5',
            '35201-9710617-9',
            '34301-1745654-7',
            '38402-1353699-3',
            '33202-5978476-3',
            '32102-2795885-1',
            '38403-8351775-2',
            '32301-7986920-1',
            '34104-2227146-6',
            '35201-2536762-3',
            '38201-1225854-9',
            '31302-9590367-4',
            '33303-6693284-1',
            '38401-0223788-9',
            '34403-1095816-5',
            '38403-2114900-3',
            '32302-8916781-3',
            '33201-2485593-1',
            '35201-7065351-1',
            '36603-7477996-5',
            '38403-0902612-8',
            '38301-1944714-5',
            '37405-6302495-9',
            '32202-1625277-3',
            '36304-7784439-8',
            '36302-1078058-7',
            '35401-8837421-8',
            '38201-0834775-5',
            '36603-1458807-1',
            '36303-3629930-5',
            '32103-0812515-1',
            '35201-4728222-8',
            '35201-1561693-7',
            '35201-1361462-5',
            '33202-2012996-8',
            '32304-3994772-9',
            '31201-8408051-5',
            '32102-6316396-0',
            '32103-2322391-1',
            '32301-1606767-9',
            '32102-7900705-5',
            '31201-0336133-7'
        ];
        ini_set('memory_limit', '20878M');
        ini_set('max_execution_time', 2000);

        // Fetch applications with project_id = 132
        $placeholders = [];
        $params = [];
        foreach ($nicArray as $index => $cnic) {
            $key = ':cnic' . $index;
            $placeholders[] = $key;
            $params[$key] = $cnic;
        }

        $sql = "
                SELECT members.cnic, loans.date_disbursed,loans.disbursed_amount
                FROM loans
                INNER JOIN applications ON applications.id = loans.application_id
                INNER JOIN members ON members.id = applications.member_id
                WHERE members.cnic IN (" . implode(', ', $placeholders) . ")
                  AND loans.project_id = :project_id
            ";
        $params[':project_id'] = 132;
        $command = Yii::$app->db->createCommand($sql);
        foreach ($params as $key => $value) {
            $command->bindValue($key, $value);
        }

        $trancheDisbursed = $command->queryAll();

        foreach ($trancheDisbursed as $loan) {
            $cnic_without_hyphens = str_replace('-', '', $loan['cnic']);
            $obj = [
                "CNIC"=> $cnic_without_hyphens,
                "FirstDisbursementDate"=> date('Y-m-d', $loan['date_disbursed']),
                "NoOfInstallments"=>null,
                "MonthlyInstallmentAmount"=> null,
                "FirstDueDate"=>null,
                "SecondDisbursementDate"=> null,
            ];

            AcagHelper::actionPushDisbursement($obj);
            echo '---'.$cnic_without_hyphens.'---';
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