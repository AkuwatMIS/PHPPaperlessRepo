<?php
namespace console\controllers;

use common\components\Helpers\AccountsReportHelper;
use common\components\Helpers\DataCheckHelper;
use common\components\Helpers\ExportHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\KamyabPakistanHelper;
use common\models\Actions;
use common\models\Applications;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Branches;
use common\models\DynamicReports;
use common\models\Loans;
use common\models\ProgressReports;
use common\models\Schedules;
use common\models\Users;
use common\widgets\LedgerPdf\LedgerPdf;
use Ratchet\App;
use Yii;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\console\Controller;
use yii2tech\csvgrid\CsvGrid;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;

class DynamicReportsController extends Controller
{

    public function actionPortfolio()
    {
        $sql = "SELECT `regions`.`name` AS `region`, `areas`.`name` AS `area`, `branches`.`name` AS `branch`, `branches`.`code` AS `branch_code`,`loans`.`id`, `loans`.`area_id`, 
`loans`.`branch_id`, `loans`.`application_id`, `loans`.`sanction_no`, `loans`.`inst_amnt`, `loans`.`region_id`, `loans`.`project_id`,
 `members`.`full_name` AS `name`, `members`.`parentage` AS `parentage`, `members`.`cnic` AS `cnic`,`members`.`education` AS `education`, `members`.`dob` AS `dob`, CEILING((`loans`.`date_disbursed` - `members`.`dob`)/31536000) AS age, `members`.`gender` AS `gender`,
`groups`.`grp_no` AS `grpno`, FROM_UNIXTIME(`loans`.`date_disbursed`) AS `date`, `loans`.`cheque_no`, `inst_months`, `loans`.`loan_amount`, 
FROM_UNIXTIME(`loans`.`loan_expiry`) AS `loan_expiry`, 
(select address from members_address where is_current=1 and member_id=members.id and address_type=\"home\" limit 1) as address, 
(select phone from members_phone where is_current=1 and member_id=members.id and phone_type=\"Mobile\" limit 1) as mobile, `groups`.`grp_type` AS `grptype`,
 `applications`.`activity_id`, 
(SELECT  COALESCE(SUM(`recoveries`.`amount`),0) FROM recoveries WHERE recoveries.loan_id = loans.id AND recoveries.deleted = 0 AND recoveries.receive_date <= :date)  AS `recovery_amount`, 
`projects`.`name` AS `project`,`activities`.`name` AS `activity`,`products`.`name` AS `product`  
FROM `loans` INNER JOIN `applications` ON `loans`.`application_id` = `applications`.`id` 
INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `loans`.`group_id` = `groups`.`id`  
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id` 
INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
LEFT JOIN `activities` ON `loans`.`activity_id` = `activities`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` WHERE (`loans`.`status` not in ('not collected','pending','grant')) AND (`loans`.`deleted` = 0) AND (branches.deleted = 0) AND members.cnic in ('36601-2881223-3','32102-4576616-5','35202-4724931-5','37203-6048379-3','36602-0238835-7','31201-7538735-9','71503-3987676-9',
'34301-7044668-1',
'14301-1222689-7',
'31202-1964699-9');";
        $date1=strtotime('now');
        $date2=date('Y-m-t', strtotime('now'));
        $date3=date('Y-m-d',strtotime('first day of last month',strtotime('now')));;
        $query = Yii::$app->db->createCommand($sql)
            ->bindParam(':date', $date1);
        $data = $query->queryAll();
        $header = 'region,area,branch,branch_code,sanction_no,name,parentage,cnic,education,dob,age,gender,grpno,date,cheque_no,loan_amount,loan_expiry,address,mobile,recovery_amount,project,activity,product';
      $createColumn = [];
            $header_list = explode(',',$header);
            foreach ($header_list as $header) {
                $createColumn[] = ucwords(str_replace('_', ' ', $header));
            }
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        $file_name =  'my portfolio'.'_'. date('d-m-Y-H-i-s') . '.csv';
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/dynamic_reports/'. $file_name;
        $fopen = fopen($file_path,'w');

            fputcsv($fopen,$createColumn);

            foreach ($data as $d)
            {
                $da = [];
                foreach ($d as $k =>$val)
                {
                    if(in_array($k , $header_list))
                    {
                        if($k == 'dob')
                        {
                            $da[$k] = date('Y-m-d',$val);
                        } else {
                            $da[$k] = $val;
                        }
                    }
                }
                fputcsv($fopen,$da);
            }

            fclose($fopen);

    }
//nohup php yii dynamic-reports/generate
    public function actionGenerate()
    {
        ini_set('memory_limit', '2004004M');
        ini_set('max_execution_time', 1500000);
        $dynamic_reports = DynamicReports::find()->where(['status' => 0, 'deleted' => 0, 'is_approved' => 1])
            ->andWhere(['not in', 'report_defination_id', [9, 10,15,16,40,44]])->all();
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        foreach ($dynamic_reports as $dynamic_report) {
            $reportArrayOut = [28,29,30,31,38];
            $sql = $dynamic_report->report->sql_query;
            if (in_array($dynamic_report->report_defination_id, $reportArrayOut)) {

            }else{
                $sql .= $dynamic_report->sql_filters;
            }

            $reportArray = [22,24,12,14,22,28,29,30,31,32,33,34,35,36,37,38,42,43,21,45];
            if (!in_array($dynamic_report->report_defination_id, $reportArray)) {
                $sql .= ' ORDER BY date';
            }

            $createColumn = [];
            $header_list = explode(',', $dynamic_report->report->header);
            foreach ($header_list as $header) {
                $createColumn[] = ucwords(str_replace('_', ' ', $header));
            }

            if(isset($dynamic_report->report_date) && !empty($dynamic_report->report_date))
            {
                $date = explode(' - ', $dynamic_report->report_date);
                $date = strtotime(date('Y-m-d 23:59:59', strtotime($date[1])));
            } else {
                $date = strtotime(date('Y-m-d 23:59:59'));
            }

            $reportsIdB = [5,7];
            if (in_array($dynamic_report->report_defination_id, $reportsIdB)) {
                $sql = $dynamic_report->report->sql_query;
                $report_date = explode(' - ', $dynamic_report->report_date);
                $date1 = $report_date[1];
                $date2 = date('Y-m-t', strtotime($report_date[1]));
                $date3 = date('Y-m-d', strtotime('first day of last month', strtotime($report_date[1])));
                $loan_complete_start = date('Y-m-d', strtotime($report_date[0]));
                $loan_complete_end = date('Y-m-d', strtotime($report_date[1]));
                $query = Yii::$app->db->createCommand($sql)
                    ->bindParam(':date_disbursed', $date1)->bindParam(':last_day', $date2)->bindParam(':loan_completed_date', $loan_complete_start)->bindParam(':loan_completed_date_end', $loan_complete_end);
                $data = $query->queryAll();
                $createColumn = $header_list;
                $data = DataCheckHelper::actionDataCheckUpdate($data, $date1, $dynamic_report->report_defination_id);
            }else{
                if ($dynamic_report->report_defination_id != 11) {
                    $reportArrayIn = [22,28,29,30,31,32,33,38,42,21];
                    if (in_array($dynamic_report->report_defination_id, $reportArrayIn)) {
                        if($dynamic_report->report_defination_id == 42){
                            $controller = new PmypController(Yii::$app->controller->id, Yii::$app);
                            $data = $controller->actionProgressReport($dynamic_report->report_date,$dynamic_report->pmt_score);
                        }else{
                            $query = Yii::$app->db->createCommand($sql)
                                ->bindParam(':date', $date);
                            $data = $query->queryAll();
                        }
                    }else{
                        $reportArrayDateDisbursed = [34,35,36,37];
                        if (in_array($dynamic_report->report_defination_id, $reportArrayDateDisbursed)) {
                            $report_date = explode(' - ', $dynamic_report->report_date);
                            $date_to = $report_date[1];
                            $schedule_next_date_init = strtotime(date('Y-m-10', strtotime("+1 months", strtotime($date_to))));
                            $schedule_next_to_next_date_init = strtotime(date('Y-m-10', strtotime("+2 months", strtotime($date_to))));

                            $recovery_till_date = strtotime(date('Y-m-t', strtotime($date_to)));
                            $schedule_till_date = strtotime(date('Y-m-10', strtotime($date_to)));

                            if ($dynamic_report->report_defination_id == 34){
                                $query = Yii::$app->db->createCommand($sql)
                                    ->bindParam(':rec_to_date', $recovery_till_date)
                                    ->bindParam(':schd_to_date', $schedule_till_date)
                                    ->bindParam(':schd_next_date', $schedule_next_date_init)
                                    ->bindParam(':schd_next_to_next_date', $schedule_next_to_next_date_init);
                            }else{
                                $query = Yii::$app->db->createCommand($sql)
                                    ->bindParam(':rec_to_date', $recovery_till_date)
                                    ->bindParam(':schd_to_date', $schedule_till_date);
                            }

                            $data = $query->queryAll();


                        }else{
                            if($dynamic_report->report_defination_id == 41){
                                $date = explode(' - ', $dynamic_report->report_date);
                                $dateFinal = strtotime(date('Y-m-d 23:59:59', strtotime($date[1])));
                                $query = Yii::$app->db->createCommand($sql)
                                    ->bindParam(':date', $dateFinal);
                                $data = $query->queryAll();
                            }elseif ($dynamic_report->report_defination_id == 45){
                                $date = explode(' - ', $dynamic_report->report_date);
                                $dateFinal = strtotime(date('Y-m-d 23:59:59', strtotime($date[1])));
                                try {
                                    Yii::$app->db->createCommand("SET @row_number := 0")->execute();
                                    Yii::$app->db->createCommand("SET @prev_loan := NULL")->execute();
                                    $query = Yii::$app->db->createCommand($sql)
                                        ->bindParam(':date', $dateFinal);
                                    $data = $query->queryAll();
                                } catch (\PDOException $e) {
                                    echo "<pre>";
                                    echo "PDO SQL Error:\n";
                                    echo $e->getMessage();
                                    echo "\n\nRaw SQL:\n";
                                    echo $sql;
                                    echo "\n\nTrace:\n";
                                    echo $e->getTraceAsString();
                                    echo "</pre>";
                                    exit;
                                } catch (\yii\db\Exception $e) {
                                    echo "<pre>";
                                    echo "Yii DB Error:\n";
                                    echo $e->getMessage();
                                    echo "\n\nRaw SQL:\n";
                                    echo $sql;
                                    echo "\n\nTrace:\n";
                                    echo $e->getTraceAsString();
                                    echo "</pre>";
                                    exit;
                                }
                            }else{
                                $query = Yii::$app->db->createCommand($sql)
                                    ->bindParam(':date', $date);
                                $data = $query->queryAll();
                            }

                        }
                    }

                }
            }

            $reportsIdB = [4,6];
            if (in_array($dynamic_report->report_defination_id, $reportsIdB)) {
                $createColumn = $header_list;
                $data = DataCheckHelper::actionDataCheckAdd($data, $dynamic_report->report_defination_id);
            }

            if ($dynamic_report->report_defination_id == 11) {
                ini_set("pcre.backtrack_limit", "105000000");
                $content='
                    <div style="page-break-after: always; width: 100%; height: 100%; background-color: lightgray; border: 2px solid gray; text-align: center">
                        <div style="width: 100%; height: 30%"></div>
                        <h1>Ledgers</h1><br />
                        <br />
                        <b>complete Ledgers of specified sanction no</b>
                    </div>';
                $file_path = ImageHelper::getAttachmentPath() .'/dynamic_reports/' . 'ledger' . '/' . $dynamic_report->uploaded_file;
                $myfile = fopen($file_path, "r");
                $flag = true;
                while (($fileop = fgetcsv($myfile)) !== false) {
                    if ($flag) {
                        $sanction_no=$fileop[0];
                        $model=Loans::find()->where(['sanction_no'=>$sanction_no])->one();
                        if(!empty($model)){
                            $content.=LedgerPdf::widget(['model' => $model]);
                        }
                    }
                }
                $pdf = new Pdf([
                    'mode' => Pdf::MODE_CORE,
                    'format' => Pdf::FORMAT_LEGAL,
                    'orientation' => Pdf::ORIENT_PORTRAIT,
                    'destination' => 'F',
                    'content' => $content,
                    'cssInline' => ' .table td {
                        height: 05px;
                    }
                    .padding-0 {
                        padding-right: 0;
                        padding-left: 0;
                    }
                    #printOnly {
                        display : none;
                    }',
                    'options' => ['title' => 'Ledgers'],
                    'methods' => [
                        'SetHeader'=>['Ledgers'],
                        'SetFooter'=>['{PAGENO}'],
                    ]
                ]);
                $file_name = $dynamic_report->report->name . '_' . $dynamic_report->id . '_' . date('d-m-Y-H-i-s') . '.pdf';
                $pdf->filename =ImageHelper::getAttachmentPath() . '/dynamic_reports/ledger/'.$file_name;
                $pdf->render();
                $dynamic_report->status = 1;
                $dynamic_report->file_path = $file_name;
                $dynamic_report->save();
            } else {
                $file_name = $dynamic_report->report->name . '_' . $dynamic_report->id . '_' . date('d-m-Y-H-i-s') . '.csv';
                $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . $dynamic_report->report->name . '/' . $file_name;

                $file_name_zip = $dynamic_report->report->name . '_' . $dynamic_report->id . '_' . date('d-m-Y-H-i-s') . '.zip';
                $file_path_zip = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . $dynamic_report->report->name . '/' . $file_name_zip;


                $fopen = fopen($file_path, 'w');
                fputcsv($fopen, $createColumn);

                foreach ($data as $d) {
                    $da = [];
                    if ($dynamic_report->report_defination_id == 37) {
                        $disbursed_amount = $d['Amount_disbursed'];
                        $project = $d['Scheme'];
                        $sanction = $d['loan_no'];
                        $tenor = $d['Tenor'];
                        if($disbursed_amount!=0 && $project=='Housing'){
                            $report_date = explode(' - ', $dynamic_report->report_date);
                            $date_to = $report_date[1];
                            $recovery_till_date = strtotime(date('Y-m-t', strtotime($date_to)));
                            $amountFixRent = KamyabPakistanHelper::KppHousingReport($sanction,$disbursed_amount,$tenor,$recovery_till_date);
                        }else{
                            $amountFixRent=0;
                        }

                        foreach ($d as $k => $val) {
                            if (in_array($k, $header_list)) {
                                if ($k == 'dob' || $k == 'date_of_birth_of_borrower') {
                                    $da[$k] = date('Y-m-d', $val);
                                } elseif($k == 'cheque_no') {
                                    $da[$k] = "'$val'";
                                }elseif($k == 'Markup_Amount_Outstanding_against_the_facility') {
                                    $da[$k] = $amountFixRent;
                                }else{
                                    $da[$k] = $val;
                                }
                            }
                        }
                        fputcsv($fopen, $da);
                    }else{
                        
                        foreach ($d as $k => $val) {
                            if (in_array($k, $header_list)) {
                                if ($k == 'dob' || $k == 'date_of_birth_of_borrower') {
                                    $da[$k] = date('Y-m-d', $val);
                                } elseif($k == 'cheque_no') {
                                    $da[$k] = "'$val'";
                                }else{
                                    $da[$k] = $val;
                                }
                            }
                        }
                        fputcsv($fopen, $da);
                    }
                }
                fclose($fopen);

                $zip = new \ZipArchive();
                if ($zip->open($file_path_zip, \ZipArchive::CREATE) === TRUE) {
                    // Add files to the zip file
                    $zip->addFile($file_path, $file_name);
                    // All files are added, so close the zip file.
                    $zip->close();
                }
                unlink($file_path);
                $dynamic_report->status = 1;
                $dynamic_report->file_path = $file_name_zip;
                $dynamic_report->save();
            }

        }
    }

//    nohup php yii dynamic-reports/export-due-list
    public function actionExportDueList()
    {
        ini_set('memory_limit', '202584M');
        ini_set('max_execution_time', 30000);
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));

        $dynamic_reports = DynamicReports::find()->where(['status' => 0, 'deleted' => 0])->andWhere(['in', 'report_defination_id', [40,44]])->all();

        $connection = Yii::$app->db;
        foreach ($dynamic_reports as $report) {
            $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . 'duelist' . '/' . $report->uploaded_file;

            $myfile = fopen($file_path, "r");

            $dispatchBranches = [];
            $receipt_date = 0;
            $due_date = 0;
            $disburse_date = 0;

            $count = 0;
            while (($fileop = fgetcsv($myfile)) !== false) {
                if($fileop[0]!='branch_code'){
                    $dispatchBranches[$count] = $fileop[0];
                }
                $receipt_date = strtotime($fileop[1]);
                $due_date = strtotime($fileop[2]);
                $disburse_date = strtotime($fileop[3]);
                $count++;
            }

            $data_Array = [];
            foreach ($dispatchBranches as $key=>$branch){
                $data_Array[$key] = "'$branch'";
            }
            $branch_Array = implode(",", $data_Array);

            $due_report = "
                    select
                    m.full_name,
                    m.parentage,
                    m.cnic,
                    mphon.phone,
                    pr.name 'project_name',
                    grp.grp_no,
                    DATE_FORMAT(FROM_UNIXTIME(ln.date_disbursed), '%Y-%m-%d') 'date_disbursed',
                    br.code 'branch_code',
                    if(ln.loan_completed_date>0,1,0) 'is_completed',
                    ln.sanction_no,
                    rg.name 'region_name',
                    ar.name 'area_name',
                    br.name 'branch_name',
                    ln.loan_amount,
                    ln.disbursed_amount,
                    ln.inst_amnt,
                    
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
                    on mphon.member_id = m.id and mphon.is_current and LENGTH(mphon.phone) >= 11
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
                    ln.status in ('collected','loan completed')
                    and
                    ln.date_disbursed <= $disburse_date
                    AND br.code in ($branch_Array)
                    group by ln.sanction_no
                    having olp>0 and due_amount > 0";

            $due_report = $connection->createCommand($due_report)->queryAll();


            $file_name = $report->report->name . '_' . $report->id . '_' . date('d-m-Y-H-i-s') . '.csv';
            $filePath = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . $report->report->name . '/' . $file_name;

            $fopenW = fopen($filePath, 'w');
            header('Content-type: application/csv');
            header('Content-Disposition: attachment; filename=active_loans_30th_Sep_2022.csv');

            $createColumn = array('full_name', 'parentage', 'cnic', 'phone', 'project_name', 'grp_no',
                'date_disbursed', 'branch_code', 'is_completed', 'sanction_no', 'region_name', 'area_name',
                'branch_name', 'loan_amount', 'disbursed_amount', 'inst_amnt','recovery','olp','due_amount',
                'due_charges_amount','due_amount_tax','total_due');
            fputcsv($fopenW, $createColumn);
            foreach ($due_report as $d) {
                $e = [];
                $e['full_name'] = $d['full_name'];
                $e['parentage'] = $d['parentage'];
                $e['cnic'] = $d['cnic'];
                $e['phone'] = $d['phone'];
                $e['project_name'] = $d['project_name'];
                $e['grp_no'] = $d['grp_no'];
                $e['date_disbursed'] = $d['date_disbursed'];
                $e['branch_code'] = $d['branch_code'];
                $e['is_completed'] = $d['is_completed'];
                $e['sanction_no'] = $d['sanction_no'];
                $e['region_name'] = $d['region_name'];
                $e['area_name'] = $d['area_name'];
                $e['branch_name'] = $d['branch_name'];
                $e['loan_amount'] = $d['loan_amount'];
                $e['disbursed_amount'] = $d['disbursed_amount'];
                $e['inst_amnt'] = $d['inst_amnt'];
                $e['recovery'] = $d['recovery'];
                $e['olp'] = $d['olp'];
                $e['due_amount'] = $d['due_amount'];
                $e['due_charges_amount'] = $d['due_charges_amount'];
                $e['due_amount_tax'] = $d['due_amount_tax'];
                $e['total_due'] = $d['total_due'];

//                $d['due_amount'] =  $d['temp_due'];
//                if($d['olp'] > $d['inst_amnt']){
//                    $d['monthly_due'] =  $d['inst_amnt'];
//                    $d['due_amount'] =  $d['inst_amnt'];
//                }else{
//                    $d['monthly_due'] =  $d['olp'];
//                    $d['due_amount'] =  $d['olp'];
//                }
//                $d['sch']='test';
//                if(in_array($d['project_id'],[52,61,62,64,67,76,77,83,90,103,109,97,100,110,114,119,118])){
//                    $monthlyDue = Schedules::find()->where(['loan_id'=>$d['id']])->andWhere(['due_date'=>$due_date])->one();
//                    $d['monthly_due'] = $monthlyDue->schdl_amnt+$monthlyDue->charges_schdl_amount;
//                    $d['due_amount'] = $monthlyDue->schdl_amnt+$monthlyDue->charges_schdl_amount;
//                    $d['sch']=$monthlyDue->id;
//
//                }
                fputcsv($fopenW, $e);
            }

            $file_name_zip = $report->report->name . '_' . $report->id . '_' . date('d-m-Y-H-i-s') . '.zip';
            $file_path_zip = ImageHelper::getAttachmentPath() . '/dynamic_reports/' . $report->report->name . '/' . $file_name_zip;

            $zip = new \ZipArchive();
            if ($zip->open($file_path_zip, \ZipArchive::CREATE) === TRUE) {
                // Add files to the zip file
                $zip->addFile($filePath, $file_name);
                // All files are added, so close the zip file.
                $zip->close();
            }
            unlink($filePath);

            $report->status=1;
            $report->file_path = $file_name_zip;
            $report->save();
        }
    }

    public function actionGenerateAccounts()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        $dynamic_reports = DynamicReports::find()->where(['status' => 0,'deleted'=>0,'is_approved'=>1])->andWhere(['in','report_defination_id',[9,10]])->all();
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        foreach ($dynamic_reports as $dynamic_report)
        {
            if($dynamic_report->report_defination_id==9){
                $data = AccountsReportHelper::ProgressSummary(unserialize($dynamic_report->sql_filters),true,$dynamic_report->created_by);
                $headers=[ 'Region','Area','Branch Code', 'Branch Name','Project Name', 'Recovery Amount','Disbursement Amount','OLP'];
            }else{
                $data = AccountsReportHelper::ProgressSummaryDetails(unserialize($dynamic_report->sql_filters),true,$dynamic_report->created_by);
                $headers=['Region','Area','Branch Code', 'Branch Name', 'Project Name','Opening Active Loans','Opening OLP','No. of loans disbursed','Disbursement Amount','Recovery Amount','Closing Active Loans','Closing OLP'];
            }

            $file_name = $dynamic_report->report->name .'_'.$dynamic_report->id .'_'. date('d-m-Y-H-i-s') . '.csv';
            $file_path = ImageHelper::getAttachmentPath() . '/dynamic_reports/'.$dynamic_report->report->name.'/'. $file_name;

            $file_name_zip = $dynamic_report->report->name .'_'.$dynamic_report->id .'_'. date('d-m-Y-H-i-s') . '.zip';
            $file_path_zip = ImageHelper::getAttachmentPath() . '/dynamic_reports/'.$dynamic_report->report->name.'/'. $file_name_zip;

            $fopen = fopen($file_path,'w');
            fputcsv($fopen,$headers);
            if($dynamic_report->report_defination_id==10){
                foreach ($data as $d) {
                    foreach ($d as $k => $val) {
                        if($k == 'disb')
                        {
                            $array = explode('+', $val);
                            $a['disb_amount'] = isset($array[0]) ? ($array[0]) : 0;
                            $a['disb_loans'] = isset($array[1]) ? ($array[1]) : 0;
                        } else if ($k == 'opening') {
                            $array = explode('+', $val);
                            $a['opening_olp']  = isset($array[0]) ? ($array[0]) : 0;
                            $a['opening_active_loans']  = isset($array[1]) ? ($array[1]) : 0;
                        } else if ($k == 'closing') {
                            $array = explode('+', $val);
                            $a['closing_olp']  = isset($array[0]) ? ($array[0]) : 0;
                            $a['closing_active_loans']  = isset($array[1]) ? ($array[1]) : 0;
                        } else {
                            $a[$k] = $val;
                        }

                    }
                    $arr[] = $a;
                }
                //$data=$arr;
                $data = ExportHelper::parseProgressReportExportData($arr);
            }
            foreach ($data as $d)
            {
                $da = [];
                foreach ($d as $k =>$val)
                {
                    $da[$k] = $val;
                }
                fputcsv($fopen,$da);
            }

            fclose($fopen);

            $zip = new \ZipArchive();
            if ($zip->open($file_path_zip, \ZipArchive::CREATE) === TRUE)
            {
                // Add files to the zip file
                $zip->addFile($file_path, $file_name);
                // All files are added, so close the zip file.
                $zip->close();
            }
            unlink($file_path);
            $dynamic_report->status = 1;
            $dynamic_report->file_path = $file_name_zip;
            $dynamic_report->save();
        }
    }
     public function actionPortfolioDisability($date1,$date2,$project_id =26)
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        $sql = 'SELECT `regions`.`name` AS `region`, `areas`.`name` AS `area`, `branches`.`name` AS `branch`, `branches`.`code` AS `branch_code`,`loans`.`id`, `loans`.`area_id`, 
                `loans`.`branch_id`, `loans`.`application_id`, `loans`.`sanction_no`, `loans`.`inst_amnt`, `loans`.`region_id`, `loans`.`project_id`,
                 `members`.`full_name` AS `name`, `members`.`parentage` AS `parentage`, `members`.`cnic` AS `cnic`,`members`.`education` AS `education`, `members`.`dob` AS `dob`, CEILING((`loans`.`date_disbursed` - `members`.`dob`)/31536000) AS age, `members`.`gender` AS `gender`,
                `groups`.`grp_no` AS `grpno`, FROM_UNIXTIME(`loans`.`date_disbursed`) AS `date`, `loans`.`cheque_no`, `inst_months`, `loans`.`loan_amount`, 
                FROM_UNIXTIME(`loans`.`loan_expiry`) AS `loan_expiry`, 
                (select address from members_address where is_current=1 and member_id=members.id and address_type="home" limit 1) as address, 
                (select phone from members_phone where is_current=1 and member_id=members.id and phone_type="Mobile" ORDER by id DESC limit 1) as mobile, `groups`.`grp_type` AS `grptype`,
                 `applications`.`activity_id`, 
                (SELECT  COALESCE(SUM(`recoveries`.`amount`),0) FROM recoveries WHERE recoveries.loan_id = loans.id AND recoveries.deleted = 0 AND recoveries.receive_date > 0 AND recoveries.receive_date <= :date)  AS `recovery_amount`, 
                `projects`.`name` AS `project`,`activities`.`name` AS `activity`,`products`.`name` AS `product`
                 ,is_khidmat_card_holder,disability,nature ,physical_disability,visual_disability,communicative_disability,disabilities_instruments
                FROM `loans` INNER JOIN `applications` ON `loans`.`application_id` = `applications`.`id` 
                INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
                INNER JOIN `groups` ON `loans`.`group_id` = `groups`.`id`  
                INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
                INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id` 
                INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
                INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
                LEFT JOIN `activities` ON `loans`.`activity_id` = `activities`.`id` 
                LEFT JOIN `projects_disabled` ON projects_disabled.application_id= applications.id 
                INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` WHERE (`loans`.`status` not in (\'not collected\',\'pending\',\'grant\')) AND (`loans`.`deleted` = 0) AND (branches.deleted = 0) AND (branches.status = 1)';

        $sql .= ' AND loans.project_id = "'. $project_id.'" HAVING date >= "'.$date1.'" AND date <= "'.$date2.'"  ORDER BY date';

        $createColumn = [];
        $header_data = 'region,area,branch,branch_code,sanction_no,name,parentage,cnic,education,dob,age,gender,grpno,date,cheque_no,loan_amount,loan_expiry,address,mobile,recovery_amount,project,activity,product,is_khidmat_card_holder,disability,nature ,physical_disability,visual_disability,communicative_disability,disabilities_instruments';

        $header_list = explode(',',$header_data);
        foreach ($header_list as $header) {
            $createColumn[] = ucwords(str_replace('_', ' ', $header));
        }

        $date = strtotime(date('Y-m-d 23:59:59',strtotime($date2)));


        $query = Yii::$app->db->createCommand($sql)
            ->bindParam(':date', $date);
        $data = $query->queryAll();

        $file_name = 'pspa_'. date('d-m-Y-H-i-s') . '.csv';
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/dynamic_reports/'. $file_name;


        $fopen = fopen($file_path,'w');

        fputcsv($fopen,$createColumn);

        foreach ($data as $d)
        {
            $da = [];
            foreach ($d as $k =>$val)
            {
                if(in_array($k , $header_list))
                {
                    if($k == 'dob')
                    {
                        $da[$k] = date('Y-m-d',$val);
                    } else {
                        $da[$k] = $val;
                    }
                }
            }
            fputcsv($fopen,$da);
        }

        fclose($fopen);
    }



    public function actionPortfolioDisabilityData()
    {
        ini_set('memory_limit', '204878M');
        ini_set('max_execution_time', 300);
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        $sql = 'SELECT `regions`.`name` AS `region`, `areas`.`name` AS `area`, `branches`.`name` AS `branch`, `branches`.`code` AS `branch_code`,`loans`.`id`, `loans`.`area_id`, 
                `loans`.`branch_id`, `loans`.`application_id`, `loans`.`sanction_no`, `loans`.`inst_amnt`, `loans`.`region_id`, `loans`.`project_id`,
                 `members`.`full_name` AS `name`, `members`.`parentage` AS `parentage`, `members`.`cnic` AS `cnic`,`members`.`education` AS `education`, `members`.`dob` AS `dob`, CEILING((`loans`.`date_disbursed` - `members`.`dob`)/31536000) AS age, `members`.`gender` AS `gender`,
                `groups`.`grp_no` AS `grpno`, FROM_UNIXTIME(`loans`.`date_disbursed`) AS `date`, `loans`.`cheque_no`, `inst_months`, `loans`.`loan_amount`, 
                FROM_UNIXTIME(`loans`.`loan_expiry`) AS `loan_expiry`, 
                (select address from members_address where is_current=1 and member_id=members.id and address_type="home" limit 1) as address, 
                (select phone from members_phone where is_current=1 and member_id=members.id and phone_type="Mobile" ORDER by id DESC limit 1) as mobile, `groups`.`grp_type` AS `grptype`,
                 `applications`.`activity_id`, 
                (SELECT  COALESCE(SUM(`recoveries`.`amount`),0) FROM recoveries WHERE recoveries.loan_id = loans.id AND recoveries.deleted = 0 AND recoveries.receive_date > 0)  AS `recovery_amount`, 
                `projects`.`name` AS `project`,`activities`.`name` AS `activity`,`products`.`name` AS `product`
                 ,is_khidmat_card_holder,disability,nature ,physical_disability,visual_disability,communicative_disability,disabilities_instruments
                FROM `loans` INNER JOIN `applications` ON `loans`.`application_id` = `applications`.`id` 
                INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
                INNER JOIN `groups` ON `loans`.`group_id` = `groups`.`id`  
                INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
                INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id` 
                INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
                INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
                LEFT JOIN `activities` ON `loans`.`activity_id` = `activities`.`id` 
                LEFT JOIN `projects_disabled` ON projects_disabled.application_id= applications.id 
                INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` WHERE (`loans`.`status` not in (\'not collected\',\'pending\',\'grant\')) AND (`loans`.`deleted` = 0) AND (branches.deleted = 0) AND (branches.status = 1)';

        $sql .= ' AND loans.project_id = 26 ORDER BY date';

        $createColumn = [];
        $header_data = 'region,area,branch,branch_code,sanction_no,name,parentage,cnic,education,dob,age,gender,grpno,date,cheque_no,loan_amount,loan_expiry,address,mobile,recovery_amount,project,activity,product,is_khidmat_card_holder,disability,nature ,physical_disability,visual_disability,communicative_disability,disabilities_instruments';

        $header_list = explode(',',$header_data);
        foreach ($header_list as $header) {
            $createColumn[] = ucwords(str_replace('_', ' ', $header));
        }

        $query = Yii::$app->db->createCommand($sql);
        $data = $query->queryAll();

        $file_name = 'custom_pspa.csv';
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/'. $file_name;


        $fopen = fopen($file_path,'w');

        fputcsv($fopen,$createColumn);

        foreach ($data as $d)
        {
            $da = [];
            foreach ($d as $k =>$val)
            {
                if(in_array($k , $header_list))
                {
                    if($k == 'dob')
                    {
                        $da[$k] = date('Y-m-d',$val);
                    } else {
                        $da[$k] = $val;
                    }
                }
            }
            fputcsv($fopen,$da);
        }

        fclose($fopen);
    }
}