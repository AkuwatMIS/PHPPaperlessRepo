<?php


namespace console\controllers;


use common\components\Helpers\BankaccountsHelper;
use common\components\Helpers\CibDataCheckHelper;
use common\components\Helpers\CibHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\FixesHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\PmypPushHelper;
use common\models\Applications;
use common\models\ApplicationsCib;
use common\models\CibTypes;
use common\models\DisbursementDetails;
use common\models\FilesAccounts;
use common\models\FilesApplication;
use common\models\Loans;
use common\models\LoanTranches;
use common\models\MemberInfo;
use common\models\Members;
use common\models\MembersAccount;
use common\models\PmypLoansInfo;
use common\models\PmypReport;
use common\models\Projects;
use common\models\Provinces;
use common\widgets\Cib\Cib;
use Ratchet\App;
use SimpleXMLElement;
use Yii;
use yii\console\Controller;
use yii\helpers\Json;
use yii\web\Response;
use kartik\mpdf\Pdf;

class PmypController extends Controller
{

    public function actionLogin()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.pmyp.gov.pk/users/authenticate',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'{
                     "username" : "PMYPAkhuwat",
                    "password" : "PMYP^&*Akhuwat#834"
              }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: cookiesession1=678B28EBE73226A4B5FEB6E8F9B7D304'
            ),
        ));
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            $error_msg = curl_error($curl);
            // In console, log to console output or a file directly
            $this->stderr("cURL Error: " . $error_msg . "\n"); // Use stderr for errors
            return self::EXIT_CODE_ERROR; // Indicate an error occurred
        }

        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Log the full response for debugging (console output)
        $this->stdout("API Response (HTTP {$http_code}): " . $response . "\n");

        $data = json_decode($response, true);

        if ($http_code === 200 && $data && isset($data['token'])) {
            $this->stdout("Authentication successful!\n");
            $this->stdout("token: " . $data['token'] . "\n"); // Or whatever data you need
            $this->stdout("Id: " . $data['id'] . "\n"); // Or whatever data you need
            // You might return the token or process it further
            return self::EXIT_CODE_NORMAL;
        } else {
            $this->stderr("API authentication failed.\n");
            $this->stderr("Response: " . print_r($data, true) . "\n");
            $this->stderr("HTTP Code: " . $http_code . "\n");
            return self::EXIT_CODE_ERROR;
        }
    }

    public function actionDataByCnic($auth, $nic)
    {
        $body = [
            "id" => $auth['id'],
            "cnic" => str_replace('-', '', $nic),
            "Phase" => "3"
        ];

        $headers = array
        (
            'Authorization: bearer ' . $auth['token'],
            'Content-Type: application/json'
        );


        $ch = curl_init('https://api.pmyp.gov.pk/api/ApplicantData/PostPhase2APIDataByCnic');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        return $result;
    }

    public function actionStatusReasonWise($auth, $loan)
    {
        $headers = array
        (
            'Authorization: bearer ' . $auth['token'],
            'Content-Type: application/json'
        );
        $body = PmypPushHelper::parseRequestBody($auth['id'], $loan);

        $ch = curl_init('https://api.pmyp.gov.pk/api/ApplicantData/PostPhase2StatusReasonWise');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        print_r($result);
        return $result;
    }

//    php yii pmyp/execute-call type

    public function actionExecuteCall($type)
    {
        if ($type == 'post') {
            $loansModel = Loans::find()
               // ->where(['sanction_no'=>'205-D027-00003'])
                ->andWhere(['in', 'project_id', [105, 106]])
                ->andWhere(['in', 'status', ['collected', 'loan completed']])
                ->all();

            $auth = self::actionLogin(); 
            
            if(isset($auth['token']) && !empty($auth['token'])){
                foreach ($loansModel as $loan) {
                    $infoExisting = PmypLoansInfo::find()->where(['loan_id' => $loan->id])->one();
                    if (!empty($infoExisting) && $infoExisting != null) {
                        if($infoExisting->status != $loan->status){
                            $responseData = self::actionStatusReasonWise($auth, $loan);
                            $infoExisting->response = $responseData;
                            $infoExisting->status = $loan->status;
                            $infoExisting->trns_count += 1;
                            if (!$infoExisting->save()) {
                                var_dump($infoExisting->getErrors());
                            }
                        }
                    } else {
                        $responseData = self::actionStatusReasonWise($auth, $loan);
                        $info = new PmypLoansInfo();
                        $info->loan_id = $loan->id;
                        $info->status = $loan->status;
                        $info->amount = $loan->loan_amount;
                        $info->response = $responseData;
                        $info->trns_count = 1;
                        $info->region_id = $loan->region_id;
                        $info->area_id = $loan->area_id;
                        $info->branch_id = $loan->branch_id;
                        if (!$info->save(false)) {
                            var_dump($info->getErrors());
                        } else {
                            echo '<-->';
                            echo $loan->id;
                            echo '<-->';
                        }
                    }
                }
            }else{
                echo 'The page you are looking for is temporarily unavailable.  Please try again later';

            }


        } elseif ($type == 'get') {
            $nic = "41601-0693838-6";
            echo $nic;
            $auth = self::actionLogin();
            print_r($auth);
            $responseData = self::actionDataByCnic($auth, $nic);
            print_r($responseData);
            die();
        } else {
            echo 'type not found';
        }
    }


//  php yii pmyp/progress-report date

    public function actionProgressReport($month,$pmt)
    {
        $connection = Yii::$app->db;
        Yii::$app->db->createCommand()->truncateTable('pmyp_reports')->execute();

        $report_date = explode(' - ', $month);
        $date1 = $report_date[0];
        $date2 = $report_date[1];

        $dateFrom = strtotime(date('Y-m-01', strtotime($date2)));
        $dateTo = strtotime(date('Y-m-t', strtotime($date2)));

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

            $applications_between_date_query = "
                  SELECT t1.province,t1.province_id,t1.project_id,t1.sector,t1.product_type,t1.gender,
                        sum(CASE WHEN t1.status='pending' THEN t1.application_count ELSE 0 END) AS pending_apps,
                        sum(CASE WHEN t1.status='rejected' THEN t1.application_count ELSE 0 END) AS rejected_apps,
                        sum(CASE WHEN t1.status='approved' THEN t1.application_count ELSE 0 END) AS approved_apps,
                        sum(CASE WHEN t1.status='pending' THEN t1.req_amount_sum ELSE 0 END) AS pending_req_amnt_sum,
                        sum(CASE WHEN t1.status='rejected' THEN t1.req_amount_sum ELSE 0 END) AS rejected_req_amnt_sum,
                        sum(CASE WHEN t1.status='approved' THEN t1.req_amount_sum ELSE 0 END) AS approved_req_amnt_sum
                        from
                        (SELECT prv.name province,
                                           prv.id province_id,
                                           prj.id project_id,
                                           prj.name sector,
                                           prod.name product_type,
                                           mem.gender gender,
                                           app.status,
                                           count(app.id) application_count,
                                           sum(app.req_amount) req_amount_sum

                                    FROM applications app
                                    inner join members mem on mem.id=app.member_id
                                    inner join projects prj on prj.id=app.project_id
                                    inner join products prod on prod.id=app.product_id
                                    inner join branches br on br.id=app.branch_id
                                    inner join provinces prv on prv.id=br.province_id
                                    inner join application_details appd on appd.application_id=app.id
                                    
                                    where prj.id in (105,106) and appd.poverty_score=$pmt
                                    and app.application_date between $dateFrom and $dateTo AND app.deleted=0
                                    group by prv.id,prj.id,prod.name,mem.gender,app.status) t1

                        group by t1.province,t1.project_id,t1.product_type,t1.gender
                ";

            $array_one = $connection->createCommand($applications_between_date_query)->queryAll();

            if (!empty($array_one) && $array_one != null) {
                foreach ($array_one as $key => $d) {

                    if ($d['gender'] == 'f') {
                        $gender = "Female";
                    } elseif ($d['gender'] == 'm') {
                        $gender = "Male";
                    } else {
                        $gender = "Transgender";
                    }
                    $ReportingMonthApplicationReceived       = $d['pending_apps'] + $d['rejected_apps'] + $d['approved_apps'];
                    $ReportingMonthApplicationReceivedAmount = $d['pending_req_amnt_sum'] + $d['rejected_req_amnt_sum'] + $d['approved_req_amnt_sum'];

                    $pmypReport  = PmypReport::find()->where(['province_id'=>$d['province_id']])
                        ->andWhere(['project_id'=>$d['project_id']])
                        ->andWhere(['product_type'=>$d['product_type']])
                        ->andWhere(['gender'=>$gender])
                        ->one();
                    if(!empty($pmypReport) && $pmypReport!=null){
                        $pmypReport->received_application  = $ReportingMonthApplicationReceived;
                        $pmypReport->rejected_application  = $d['rejected_apps'];
                        $pmypReport->received_application_amnt  = $ReportingMonthApplicationReceivedAmount;
                        $pmypReport->rejected_application_amnt  = $d['rejected_req_amnt_sum'];
                        $pmypReport->report_date           = $month;
                        $pmypReport->save();
                    }else{
                        $pmypReport = new PmypReport();
                        $pmypReport->province_id           = $d['province_id'];
                        $pmypReport->project_id            = $d['project_id'];
                        $pmypReport->sector                = $d['sector'];
                        $pmypReport->product_type          = $d['product_type'];
                        $pmypReport->province              = $d['province'];
                        $pmypReport->gender                = $gender;
                        $pmypReport->received_application  = $ReportingMonthApplicationReceived;
                        $pmypReport->rejected_application  = $d['rejected_apps'];
                        $pmypReport->received_application_amnt  = $ReportingMonthApplicationReceivedAmount;
                        $pmypReport->rejected_application_amnt  = $d['rejected_req_amnt_sum'];
                        $pmypReport->report_date           = $month;
                        $pmypReport->save();
                    }
                }

            }

        $application_accumulated_query = "
        SELECT t1.province,t1.province_id,t1.project_id,t1.sector,t1.product_type,t1.gender,
                sum(CASE WHEN t1.status='pending' THEN t1.total_application_count ELSE 0 END) AS total_pending_apps,
                sum(CASE WHEN t1.status='rejected' THEN t1.total_application_count ELSE 0 END) AS total_rejected_apps,
                sum(CASE WHEN t1.status='approved' THEN t1.total_application_count ELSE 0 END) AS total_approved_apps,
                        sum(CASE WHEN t1.status='pending' THEN t1.req_amount_sum ELSE 0 END) AS pending_req_amnt_sum,
                        sum(CASE WHEN t1.status='rejected' THEN t1.req_amount_sum ELSE 0 END) AS rejected_req_amnt_sum,
                        sum(CASE WHEN t1.status='approved' THEN t1.req_amount_sum ELSE 0 END) AS approved_req_amnt_sum
                from
                (SELECT prv.name province,
                       prv.id province_id,
                       prj.id project_id,
                       prj.name sector,
                       prod.name product_type,
                       mem.gender gender,
                       app.status,
                       count(app.id) total_application_count,
                       sum(app.req_amount) req_amount_sum

                FROM applications app
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join products prod on prod.id=app.product_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                inner join application_details appd on appd.application_id=app.id
                                    
                where prj.id in (105,106) and appd.poverty_score=$pmt
                and app.application_date <= $dateTo  AND app.deleted=0
                group by prv.id,prj.id,prod.name,mem.gender,app.status

                ) t1

                group by t1.province,t1.project_id,t1.product_type,t1.gender
        ";
        $array_two = $connection->createCommand($application_accumulated_query)->queryAll();

        if (!empty($array_two) && $array_two != null) {
            foreach ($array_two as $key => $d) {
                if ($d['gender'] == 'f') {
                    $gender = "Female";
                } elseif ($d['gender'] == 'm') {
                    $gender = "Male";
                } else {
                    $gender = "Transgender";
                }
                $pmypReport = PmypReport::find()->where(['province_id' => $d['province_id']])
                    ->andWhere(['project_id' => $d['project_id']])
                    ->andWhere(['product_type'=>$d['product_type']])
                    ->andWhere(['gender' => $gender])
                    ->one();

                if (!empty($pmypReport) && $pmypReport != null) {
                    $pmypReport->total_pending_application = $d['total_pending_apps'];
                    $pmypReport->total_rejected_application = $d['total_rejected_apps'];
                    $pmypReport->total_approved_application = $d['total_approved_apps'];
                    $pmypReport->total_pending_application_amnt = $d['pending_req_amnt_sum'];
                    $pmypReport->total_rejected_application_amnt = $d['rejected_req_amnt_sum'];
                    $pmypReport->total_approved_application_amnt = $d['approved_req_amnt_sum'];

                    $pmypReport->save();
                } else {
                    $pmypReport = new PmypReport();
                    $pmypReport->province_id = $d['province_id'];
                    $pmypReport->project_id = $d['project_id'];
                    $pmypReport->sector = $d['sector'];
                    $pmypReport->product_type  = $d['product_type'];
                    $pmypReport->province = $d['province'];
                    $pmypReport->gender = $gender;
                    $pmypReport->total_pending_application  = $d['total_pending_apps'];
                    $pmypReport->total_rejected_application = $d['total_rejected_apps'];
                    $pmypReport->total_approved_application = $d['total_approved_apps'];
                    $pmypReport->total_pending_application_amnt = $d['pending_req_amnt_sum'];
                    $pmypReport->total_rejected_application_amnt = $d['rejected_req_amnt_sum'];
                    $pmypReport->total_approved_application_amnt = $d['approved_req_amnt_sum'];
                    $pmypReport->report_date = $month;
                    $pmypReport->save();
                }

            }

        }

        $loans_between_query = "
                 SELECT prv.name province,
                       prv.id province_id,
                       prj.id project_id,
                       prj.name sector,
                       prod.name product_type,
                       mem.gender gender,
                       count(lon.id) month_loan_count,
                       sum(lon.loan_amount) month_loan_amount
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join products prod on prod.id=app.product_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                inner join application_details appd on appd.application_id=app.id
                                    
                where prj.id in (105,106) and appd.poverty_score=$pmt
                and lon.date_approved between $dateFrom and $dateTo AND lon.deleted=0
                group by prv.id,prj.id,prod.name,mem.gender
        ";
        $array_three = $connection->createCommand($loans_between_query)->queryAll();

        if (!empty($array_three) && $array_three != null) {
            foreach ($array_three as $key => $d) {

                if ($d['gender'] == 'f') {
                    $gender = "Female";
                } elseif ($d['gender'] == 'm') {
                    $gender = "Male";
                } else {
                    $gender = "Transgender";
                }
                $pmypReport = PmypReport::find()->where(['province_id' => $d['province_id']])
                    ->andWhere(['project_id' => $d['project_id']])
                    ->andWhere(['product_type'=>$d['product_type']])
                    ->andWhere(['gender' => $gender])
                    ->one();

                if (!empty($pmypReport) && $pmypReport != null) {
                    $pmypReport->loan_count = $d['month_loan_count'];
                    $pmypReport->loan_amount = $d['month_loan_amount'];
                    $pmypReport->save();
                } else {
                    $pmypReport = new PmypReport();
                    $pmypReport->province_id = $d['province_id'];
                    $pmypReport->project_id = $d['project_id'];
                    $pmypReport->sector = $d['sector'];
                    $pmypReport->product_type  = $d['product_type'];
                    $pmypReport->province = $d['province'];
                    $pmypReport->gender = $gender;
                    $pmypReport->loan_count = $d['month_loan_count'];
                    $pmypReport->loan_amount = $d['month_loan_amount'];
                    $pmypReport->report_date = $month;
                    $pmypReport->save();
                }
            }

        }

        $loan_accumulative_query = "
                 SELECT prv.name province,
                       prv.id province_id,
                       prj.id project_id,
                       prj.name sector,
                       prod.name product_type,
                       mem.gender gender,
                       count(lon.id) total_loan_count,
                       sum(lon.loan_amount) total_loan_amount
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join products prod on prod.id=app.product_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                inner join application_details appd on appd.application_id=app.id
                                    
                where prj.id in (105,106) and appd.poverty_score=$pmt
                and lon.date_approved <= $dateTo AND lon.deleted=0
                group by prv.id,prj.id,prod.name,mem.gender
        ";
        $array_four = $connection->createCommand($loan_accumulative_query)->queryAll();

        if (!empty($array_four) && $array_four != null) {
            foreach ($array_four as $key => $d) {
                if ($d['gender'] == 'f') {
                    $gender = "Female";
                } elseif ($d['gender'] == 'm') {
                    $gender = "Male";
                } else {
                    $gender = "Transgender";
                }
                $pmypReport = PmypReport::find()->where(['province_id' => $d['province_id']])
                    ->andWhere(['project_id' => $d['project_id']])
                    ->andWhere(['product_type'=>$d['product_type']])
                    ->andWhere(['gender' => $gender])
                    ->one();

                if (!empty($pmypReport) && $pmypReport != null) {
                    $pmypReport->total_loan_count = $d['total_loan_count'];
                    $pmypReport->total_loan_amount = $d['total_loan_amount'];
                    $pmypReport->save();
                } else {
                    $pmypReport = new PmypReport();
                    $pmypReport->province_id = $d['province_id'];
                    $pmypReport->project_id = $d['project_id'];
                    $pmypReport->sector = $d['sector'];
                    $pmypReport->product_type  = $d['product_type'];
                    $pmypReport->province = $d['province'];
                    $pmypReport->gender = $gender;
                    $pmypReport->total_loan_count = $d['total_loan_count'];
                    $pmypReport->total_loan_amount = $d['total_loan_amount'];
                    $pmypReport->report_date = $month;
                    $pmypReport->save();
                }
            }

        }


        $disbursed_between_query = "
              SELECT prv.name province,
                       prv.id province_id,
                       prj.id project_id,
                       prj.name sector,
                       prod.name product_type,
                       mem.gender gender,
                       count(lon.id) disb_loan_count,
                       sum(lon.loan_amount) disb_loan_amount
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join products prod on prod.id=app.product_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                inner join application_details appd on appd.application_id=app.id
                                    
                where prj.id in (105,106) and appd.poverty_score=$pmt
                and lon.date_disbursed between $dateFrom and $dateTo AND lon.deleted=0
                and lon.status in ('collected','loan completed')
                group by prv.id,prj.id,prod.name,mem.gender
        ";
        $array_five = $connection->createCommand($disbursed_between_query)->queryAll();

        if (!empty($array_five) && $array_five != null) {
            foreach ($array_five as $key => $d) {

                if ($d['gender'] == 'f') {
                    $gender = "Female";
                } elseif ($d['gender'] == 'm') {
                    $gender = "Male";
                } else {
                    $gender = "Transgender";
                }
                $pmypReport = PmypReport::find()->where(['province_id' => $d['province_id']])
                    ->andWhere(['project_id' => $d['project_id']])
                    ->andWhere(['product_type'=>$d['product_type']])
                    ->andWhere(['gender' => $gender])
                    ->one();

                if (!empty($pmypReport) && $pmypReport != null) {
                    $pmypReport->disb_loan_count = $d['disb_loan_count'];
                    $pmypReport->disb_loan_amount = $d['disb_loan_amount'];
                    $pmypReport->save();
                } else {
                    $pmypReport = new PmypReport();
                    $pmypReport->province_id = $d['province_id'];
                    $pmypReport->project_id = $d['project_id'];
                    $pmypReport->sector = $d['sector'];
                    $pmypReport->product_type  = $d['product_type'];
                    $pmypReport->province = $d['province'];
                    $pmypReport->gender = $gender;
                    $pmypReport->disb_loan_count = $d['disb_loan_count'];
                    $pmypReport->disb_loan_amount = $d['disb_loan_amount'];
                    $pmypReport->report_date = $month;
                    $pmypReport->save();
                }

            }

        }

        $disbursed_accumulated_query = "
                SELECT prv.name province,
                       prv.id province_id,
                       prj.id project_id,
                       prj.name sector,
                       prod.name product_type,
                       mem.gender gender,
                       count(lon.id) disb_total_loan_count,
                       sum(lon.loan_amount) disb_total_loan_amount
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join products prod on prod.id=app.product_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                inner join application_details appd on appd.application_id=app.id
                                    
                where prj.id in (105,106) and appd.poverty_score=$pmt
                and lon.date_disbursed <= $dateTo AND lon.deleted=0
                and lon.status in ('collected','loan completed')
                group by prv.id,prj.id,prod.name,mem.gender
        ";
        $array_six = $connection->createCommand($disbursed_accumulated_query)->queryAll();

        if (!empty($array_six) && $array_six != null) {
            foreach ($array_six as $key => $d) {

                if ($d['gender'] == 'f') {
                    $gender = "Female";
                } elseif ($d['gender'] == 'm') {
                    $gender = "Male";
                } else {
                    $gender = "Transgender";
                }
                $pmypReport = PmypReport::find()->where(['province_id' => $d['province_id']])
                    ->andWhere(['project_id' => $d['project_id']])
                    ->andWhere(['product_type'=>$d['product_type']])
                    ->andWhere(['gender' => $gender])
                    ->one();

                if (!empty($pmypReport) && $pmypReport != null) {
                    $pmypReport->disb_total_loan_count = $d['disb_total_loan_count'];
                    $pmypReport->disb_total_loan_amount = $d['disb_total_loan_amount'];
                    $pmypReport->save();
                } else {
                    $pmypReport = new PmypReport();
                    $pmypReport->province_id = $d['province_id'];
                    $pmypReport->project_id = $d['project_id'];
                    $pmypReport->sector = $d['sector'];
                    $pmypReport->product_type  = $d['product_type'];
                    $pmypReport->province = $d['province'];
                    $pmypReport->gender = $gender;
                    $pmypReport->disb_total_loan_count = $d['disb_total_loan_count'];
                    $pmypReport->disb_total_loan_amount = $d['disb_total_loan_amount'];
                    $pmypReport->report_date = $month;
                    $pmypReport->save();
                }
            }

        }

        $active_loan_query = "
            SELECT prv.name province,
                       prv.id province_id,
                       prj.id project_id,
                       prj.name sector,
                       prod.name product_type,
                       mem.gender gender,
                       count(lon.id) active_loan_count
                        
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join products prod on prod.id=app.product_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                inner join application_details appd on appd.application_id=app.id
                                    
                where prj.id in (105,106) and appd.poverty_score=$pmt
                and lon.date_disbursed <= $dateTo AND lon.deleted=0
                and lon.status='collected'
                group by prv.id,prj.id,prod.name,mem.gender
        ";
        $array_seven = $connection->createCommand($active_loan_query)->queryAll();

        if (!empty($array_seven) && $array_seven != null) {
            foreach ($array_seven as $key => $d) {

                if ($d['gender'] == 'f') {
                    $gender = "Female";
                } elseif ($d['gender'] == 'm') {
                    $gender = "Male";
                } else {
                    $gender = "Transgender";
                }
                $pmypReport = PmypReport::find()->where(['province_id' => $d['province_id']])
                    ->andWhere(['project_id' => $d['project_id']])
                    ->andWhere(['product_type'=>$d['product_type']])
                    ->andWhere(['gender' => $gender])
                    ->one();

                if (!empty($pmypReport) && $pmypReport != null) {
                    $pmypReport->active_loan_count = $d['active_loan_count'];
                    $pmypReport->save();
                } else {
                    $pmypReport = new PmypReport();
                    $pmypReport->province_id = $d['province_id'];
                    $pmypReport->project_id = $d['project_id'];
                    $pmypReport->sector = $d['sector'];
                    $pmypReport->product_type  = $d['product_type'];
                    $pmypReport->province = $d['province'];
                    $pmypReport->gender = $gender;
                    $pmypReport->active_loan_count = $d['active_loan_count'];
                    $pmypReport->report_date = $month;
                    $pmypReport->save();
                }
            }

        }

        $active_loan_olp_query = "
           select 
               t1.province,
               t1.province_id,
               t1.project_id,
               t1.sector,
               t1.product_type,
               t1.gender,
               sum(t1.loan_amount) loan_amnt,
               sum(t1.rec_amnt) rec_amnt
           from

              (SELECT  
                       lon.id,
                       prv.name province,
                       prv.id province_id,
                       prj.id project_id,
                       prj.name sector,
                       prod.name product_type,
                       mem.gender gender,
                       lon.loan_amount,
                       (select coalesce(SUM(rec.amount),0) from recoveries rec where rec.loan_id=lon.id and rec.deleted=0 and rec.receive_date<=$dateTo) rec_amnt
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join products prod on prod.id=app.product_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                inner join application_details appd on appd.application_id=app.id
                                    
                where prj.id in (105,106) and appd.poverty_score=$pmt
                and lon.date_disbursed <= $dateTo AND lon.deleted=0
                and lon.status in('collected') ) t1

            group by t1.province_id,t1.project_id,t1.product_type,t1.gender
        ";
        $array_eight = $connection->createCommand($active_loan_olp_query)->queryAll();

        if (!empty($array_eight) && $array_eight != null) {
            foreach ($array_eight as $key => $d) {

                if ($d['gender'] == 'f') {
                    $gender = "Female";
                } elseif ($d['gender'] == 'm') {
                    $gender = "Male";
                } else {
                    $gender = "Transgender";
                }
                $pmypReport = PmypReport::find()->where(['province_id' => $d['province_id']])
                    ->andWhere(['project_id' => $d['project_id']])
                    ->andWhere(['product_type'=>$d['product_type']])
                    ->andWhere(['gender' => $gender])
                    ->one();

                if (!empty($pmypReport) && $pmypReport != null) {
                    $olp = $d['loan_amnt']-$d['rec_amnt'];
                    $pmypReport->olp_amount = $olp;
                    $pmypReport->save();
                } else {
                    $olp = $d['loan_amnt']-$d['rec_amnt'];
                    $pmypReport = new PmypReport();
                    $pmypReport->province_id = $d['province_id'];
                    $pmypReport->project_id = $d['project_id'];
                    $pmypReport->sector = $d['sector'];
                    $pmypReport->product_type  = $d['product_type'];
                    $pmypReport->province = $d['province'];
                    $pmypReport->gender = $gender;
                    $pmypReport->olp_amount = $olp;
                    $pmypReport->report_date = $month;
                    $pmypReport->save();
                }
            }

        }

        return PmypReport::find()->select([
            'province as Name_of_the_Province_Category',
            'sector as Sector',
            'product_type as Product_Type',
            'gender as Gender_Wise',
            'received_application as Reporting_Month_Received',
            'rejected_application as Reporting_Month_Rejected',
            'received_application_amnt as Reporting_Month_Received_Amount',
            'rejected_application_amnt as Reporting_Month_Rejected_Amount',
            'total_pending_application as Cumulative_Pending',
            'total_rejected_application as Cumulative_Rejected',
            'total_approved_application as Cumulative_Approved',
            'total_pending_application_amnt as Cumulative_Pending_Amount',
            'total_rejected_application_amnt as Cumulative_Rejected_Amount',
            'total_approved_application_amnt as Cumulative_Approved_Amount',
            'loan_count as Reporting_Month_Loans_Count',
            'loan_amount as Reporting_Month_Loans_Amount',
            'total_loan_count as Cumulative_Loans_Count',
            'total_loan_amount as Cumulative_Loans_Amount',
            'disb_loan_count as Reporting_Month_Disbursed_Count',
            'disb_loan_amount as Reporting_Month_Disbursed_Amount',
            'disb_total_loan_count as Cumulative_Disbursed_Count',
            'disb_total_loan_amount as Cumulative_Disbursed_Amount',
            'active_loan_count as Total_Active_Loans',
            'olp_amount as OLP'
        ])
            ->asArray()->all();

    }


    public function actionProgressReportEx($month)
    {
        $connection = Yii::$app->db;

        $dateFrom = strtotime(date('Y-m-01'), strtotime($month));
        $dateTo = strtotime(date('Y-m-t'), strtotime($month));

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/pmyp-progress-report.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=active_loans_30th_Sep_2022.csv');

        $a = array("PMYB&ALS - TIER-I");
        fputcsv($fopenW, $a);
        $b = array("Prime Minister’s Youth Business & Agriculture Loan Scheme");
        fputcsv($fopenW, $b);
        $c = array("Monthly Position of Financing by the Banks");
        fputcsv($fopenW, $c);
        $d = array("Name of the Bank", "Bank of Punjab", " ", " ", " ", " ", " ", " ", " ", "Reporting Month", "$month", " ", " ", " ", " ");
        fputcsv($fopenW, $d);
        $e = array(" ", " (Amounts in Million Rs.)");
        fputcsv($fopenW, $e);
        $f = array(" ", " ", " ", "No. of Applications", "Details of Sanctioned Applications", "Disbursements", "Outstanding at the end of Reporting Month");
        fputcsv($fopenW, $f);
        $g = array(" ", " ", " ", "Reporting Month", "Cumulative", "Disbursements", "Reporting Month", "Cumulative", "Reporting Month", "Cumulative", " ", " ");
        fputcsv($fopenW, $g);

        $createColumn = array("Name of the Province/ Category", "Sector", "Gender-Wise",
            "Reporting Month Received", "Reporting Month Rejected",
            "Cumulative	Received", "Cumulative Rejected", "Cumulative Pending",
            "Reporting Month Loans Count", "Reporting Month Loans Amount",
            "Cumulative Loans Count", "Cumulative Loans Amount",
            "Reporting Month Disbursed Count", "Reporting Month Disbursed Amount",
            "Cumulative Disbursed Count", "Cumulative Disbursed Amount",
            "Total Active Loans", "OLP");
        fputcsv($fopenW, $createColumn);

        $applications_between_date_query = "
           SELECT table1.province province_name,table1.sector sector_name,table1.gender gender_type,
                table1.pending_apps,
                table1.rejected_apps,
                table1.approved_apps,
                table2.total_pending_apps,
                table2.total_rejected_apps,
                table2.total_approved_apps,
                table3.month_loan_count,
                table3.month_loan_amount,
                table4.total_loan_count,
                table4.total_loan_amount,
                table5.disb_loan_count,
                table5.disb_loan_amount,
                table6.disb_total_loan_count,
                table6.disb_total_loan_amount,
                table7.active_loan_count,
                table7.olp_amount
                
                from
                (
                SELECT t1.province,t1.project_id,t1.sector,t1.gender,
                sum(CASE WHEN t1.status='pending' THEN t1.application_count ELSE 0 END) AS pending_apps,
                sum(CASE WHEN t1.status='rejected' THEN t1.application_count ELSE 0 END) AS rejected_apps,  
                sum(CASE WHEN t1.status='approved' THEN t1.application_count ELSE 0 END) AS approved_apps
                from
                (SELECT prv.name province,
                                   prv.id province_id,
                                   prj.id project_id,
                                   prj.name sector,
                                   mem.gender gender,
                                   app.status,
                                   count(app.id) application_count
                                   
                            FROM applications app
                            inner join members mem on mem.id=app.member_id
                            inner join projects prj on prj.id=app.project_id
                            inner join branches br on br.id=app.branch_id
                            inner join provinces prv on prv.id=br.province_id
                            
                            where prj.id in (105,106)
                            and app.application_date between $dateFrom and $dateTo
                            group by prv.id,prj.id,mem.gender,app.status) t1
                
                group by t1.province,t1.project_id,t1.gender) table1
                join
                
                (
                SELECT t1.province,t1.project_id,t1.sector,t1.gender,
                sum(CASE WHEN t1.status='pending' THEN t1.total_application_count ELSE 0 END) AS total_pending_apps,
                sum(CASE WHEN t1.status='rejected' THEN t1.total_application_count ELSE 0 END) AS total_rejected_apps,  
                sum(CASE WHEN t1.status='approved' THEN t1.total_application_count ELSE 0 END) AS total_approved_apps
                from
                (SELECT prv.name province,
                       prj.id project_id,
                       prj.name sector,
                       mem.gender gender,
                       app.status,
                       count(app.id) total_application_count
                       
                FROM applications app
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                
                where prj.id in (105,106)
                and app.application_date <= $dateTo
                group by prv.id,prj.id,mem.gender,app.status
                
                ) t1
                
                group by t1.province,t1.project_id,t1.gender
                ) table2
                
                join
                
                (
                SELECT prv.name province,
                       prj.id project_id,
                       prj.name sector,
                       mem.gender gender,
                       count(lon.id) month_loan_count,
                       sum(lon.loan_amount) month_loan_amount
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                
                where prj.id in (105,106)
                and lon.created_at between $dateFrom and $dateTo
                group by prv.id,prj.id,mem.gender
                ) table3
                
                join
                
                (
                SELECT prv.name province,
                       prj.id project_id,
                       prj.name sector,
                       mem.gender gender,
                       count(lon.id) total_loan_count,
                       sum(lon.loan_amount) total_loan_amount
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                
                where prj.id in (105,106)
                and lon.created_at <= $dateTo
                group by prv.id,prj.id,mem.gender
                ) table4
                
                join 
                
                (
                
                SELECT prv.name province,
                       prj.id project_id,
                       prj.name sector,
                       mem.gender gender,
                       count(lon.id) disb_loan_count,
                       sum(lon.loan_amount) disb_loan_amount
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                
                where prj.id in (105,106)
                and lon.created_at between $dateFrom and $dateTo
                and lon.status in ('collected','loan completed')
                group by prv.id,prj.id,mem.gender
                
                ) table5
                
                join
                
                (
                
                SELECT prv.name province,
                       prj.id project_id,
                       prj.name sector,
                       mem.gender gender,
                       count(lon.id) disb_total_loan_count,
                       sum(lon.loan_amount) disb_total_loan_amount
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                
                where prj.id in (105,106)
                and lon.created_at <= $dateTo
                and lon.status in ('collected','loan completed')
                group by prv.id,prj.id,mem.gender
                ) table6
                
                join
                
                (
                
                SELECT prv.name province,
                       prj.id project_id,
                       prj.name sector,
                       mem.gender gender,
                       count(lon.id) active_loan_count,
                       (sum(lon.loan_amount)-coalesce(SUM(rec.amount),0)) olp_amount
                        
                FROM loans lon
                inner join applications app on app.id=lon.application_id
                inner join members mem on mem.id=app.member_id
                inner join projects prj on prj.id=app.project_id
                inner join branches br on br.id=app.branch_id
                inner join provinces prv on prv.id=br.province_id
                left join recoveries rec on rec.loan_id=lon.id and rec.deleted=0
                
                where prj.id in (105,106)
                and lon.created_at <= $dateTo
                and lon.status='collected'
                group by prv.id,prj.id,mem.gender
                
                ) table7
                
                group by province_name,sector_name,gender_type
        ";
        $resultData = $connection->createCommand($applications_between_date_query)->queryAll();

        if (!empty($resultData) && $resultData != null) {
            foreach ($resultData as $key => $d) {
                $pmyp_data_array = [];

                if ($d['gender_type'] == 'f') {
                    $gender = "Female";
                } elseif ($d['gender_type'] == 'm') {
                    $gender = "Male";
                } else {
                    $gender = "Transgender";
                }

                $ReportingMonthApplicationReceived = $d['pending_apps'] + $d['rejected_apps'] + $d['approved_apps'];
                $CumulativeApplicationReceived = $d['total_pending_apps'] + $d['total_rejected_apps'] + $d['total_approved_apps'];

                $pmyp_data_array['province'] = $d['province_name'];
                $pmyp_data_array['sector'] = $d['sector_name'];
                $pmyp_data_array['gender'] = $gender;
                $pmyp_data_array['reporting_month_received'] = $ReportingMonthApplicationReceived;
                $pmyp_data_array['reporting_month_rejected'] = $d['rejected_apps'];
                $pmyp_data_array['cumulative_application_received'] = $CumulativeApplicationReceived;
                $pmyp_data_array['cumulative_application_rejected'] = $d['total_rejected_apps'];
                $pmyp_data_array['cumulative_application_pending'] = $d['total_pending_apps'];
                $pmyp_data_array['reporting_month_loan_count'] = $d['month_loan_count'];
                $pmyp_data_array['reporting_month_loan_amount'] = $d['month_loan_amount'];
                $pmyp_data_array['cumulative_loan_count'] = $d['total_loan_count'];
                $pmyp_data_array['cumulative_loan_amount'] = $d['total_loan_amount'];
                $pmyp_data_array['month_disbursed_loan_count'] = $d['disb_loan_count'];
                $pmyp_data_array['month_disbursed_loan_amount'] = $d['disb_loan_amount'];
                $pmyp_data_array['cumulative_disbursed_loan_count'] = $d['disb_total_loan_count'];
                $pmyp_data_array['cumulative_disbursed_loan_amount'] = $d['disb_total_loan_amount'];
                $pmyp_data_array['active_loan_count'] = $d['active_loan_count'];
                $pmyp_data_array['olp'] = $d['olp_amount'];

                fputcsv($fopenW, $pmyp_data_array);
            }

        }

    }


//    php yii pmyp/report-pmyb-als
    public function actionReportPmybAls(){
        $connection = Yii::$app->db;

        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 2000);

        $filepathW = ImageHelper::getAttachmentPath() . 'complete_data_extract/PMYP&ALS_30th_JUN_2024.csv';
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=PMYP&ALS_30th_JUN_2024.csv');


        $b = array("Prime Minister's Youth Business and Agriculture Loan Scheme (PMYB&ALS)");
        fputcsv($fopenW, $b);
        $c = array("(From January 2023 till July 03, 2024)");
        fputcsv($fopenW, $c);
        $d = array("Province", "Category", "Purpose of Loan",
            "Total disbursement",
            "Total No. of borrowers",
            "Total Outstanding",
            "Total No. of borrowers");
        fputcsv($fopenW, $d);
        $e = array(" ", " ", " ", "(Rs in million)","(Disbursement)","(Rs in million)","(Outstanding)");
        fputcsv($fopenW, $e);

        $applications_between_date_query = "
            SELECT
                    p.name AS Province,
                    CASE 
                        WHEN prj.id = 105 THEN 'Business/SME'
                        WHEN prj.id = 106 THEN 'Agriculture'
                    END AS Category,
                    a.bzns_cond AS 'Purpose_of_Loan',
                    SUM(l.disbursed_amount) / 1e6 AS 'Total_disbursement',
                    COUNT(DISTINCT CASE WHEN l.status IN ('collected', 'loan_completed') THEN a.id ELSE NULL END) AS 'Total_borrowers_disbursed',
                    SUM(l.disbursed_amount - COALESCE(r.total_recovered_amount, 0)) / 1e6 AS 'Total_Outstanding_amount',
                    COUNT(DISTINCT CASE WHEN l.status IN ('collected', 'loan_completed') AND (l.disbursed_amount - COALESCE(r.total_recovered_amount, 0)) > 0 THEN a.id ELSE NULL END) AS 'Total_borrowers_Outstanding'
                FROM
                    provinces p
                    JOIN branches b ON p.id = b.province_id
                    JOIN applications a ON b.id = a.branch_id
                    JOIN projects prj ON a.project_id = prj.id
                    JOIN loans l ON a.id = l.application_id
                    LEFT JOIN (
                        SELECT
                            loan_id,
                            SUM(amount) AS total_recovered_amount
                        FROM
                            recoveries
                        WHERE
                            deleted = 0 AND receive_date <= 1719790359
                        GROUP BY
                            loan_id
                    ) r ON l.id = r.loan_id
                WHERE
                    l.date_disbursed < 1719790359 AND
                    a.project_id IN (105, 106) AND
                    l.status IN ('collected', 'loan_completed')
                GROUP BY
                    p.name,
                    prj.id,
                    a.bzns_cond
                ORDER BY
                    p.name,
                    prj.id,
                    a.bzns_cond;
        ";
        $resultData = $connection->createCommand($applications_between_date_query)->queryAll();

        if (!empty($resultData) && $resultData != null) {
            foreach ($resultData as $key => $d) {
                $pmyp_data_array = [];

                $pmyp_data_array['province'] = $d['Province'];
                $pmyp_data_array['Category'] = $d['Category'];
                $pmyp_data_array['Purpose_of_Loan'] = $d['Purpose_of_Loan'];
                $pmyp_data_array['Total_disbursement_amount'] = $d['Total_disbursement'];
                $pmyp_data_array['Total_borrowers_disbursed'] = $d['Total_borrowers_disbursed'];
                $pmyp_data_array['Total_Outstanding_amount'] = $d['Total_Outstanding_amount'];
                $pmyp_data_array['Total_borrowers_Outstanding'] = $d['Total_borrowers_Outstanding'];

                fputcsv($fopenW, $pmyp_data_array);
            }

        }
    }


}