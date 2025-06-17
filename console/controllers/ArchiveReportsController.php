<?php

namespace console\controllers;

use common\components\Helpers\ArchiveReportsHelper;
use common\components\Helpers\DisbursementHelper;
use common\components\Helpers\ImageHelper;
use common\models\ArcAccountReports;
use common\models\ArchiveReports;
use common\models\Branches;
use common\models\ConnectionBanks;
use common\models\ProgressReports;
use common\models\Projects;
use common\models\search\DuelistSearch;
use common\models\search\PortfolioSearch;
use common\models\search\RecoveriesSearch;
use Yii;
use yii\web\NotFoundHttpException;
use yii\console\Controller;

class ArchiveReportsController extends Controller
{
    const PORTFOLIO_REPORT = 'portfolio-report';
    const RECOVERY_REPORT = 'recovery-report';

    /**
     *
     */

    public function actionCacheReports()
    {
        $report_date = strtotime("-1 days", strtotime(date("Y-m-d 23:59:59")));
        //products
        $sql_query = 'select p.id, p.name, p.code, p.inst_type, p.min, count(l.activity_id) as max, p.status from loans l inner join products p on l.product_id = p.id where  l.status != \'not collected\' AND l.date_disbursed>0 AND l.date_disbursed<='.$report_date.'  AND l.deleted = 0 group by l.product_id';
        $command = Yii::$app->db->createCommand($sql_query);
        $new_products = $command->queryAll();
        $date = date('Y-m-d H:i:s');
        foreach ($new_products as $p) {
            $insert = 'update cache_reports set value = "' . $p['max'] . '" , updated_at = "' . $date . '" where name = "' . $p['name'] . '" and type = "product"';
            $command = Yii::$app->db->createCommand($insert);
            $command->execute();
        }

        //activity
        $sql_query = 'select a.id, a.name, a.product_id, count(l.activity_id) as status from loans l inner join activities a on l.activity_id = a.id where l.product_id = 1 and l.status != \'not collected\' AND l.date_disbursed>0 AND l.date_disbursed<='.$report_date.' AND l.deleted = 0 group by l.activity_id';
        $command = Yii::$app->db->createCommand($sql_query);
        $new_activity = $command->queryAll();
        $date = date('Y-m-d H:i:s');
        foreach ($new_activity as $p){
            $insert = 'update cache_reports set value = "'.$p['status'].'" , updated_at = "'.$date.'" where name = "'.$p['name'].'" and type = "activity"';
            $command = Yii::$app->db->createCommand($insert);
            $command->execute();
        }
    }

    public function actionOverdue()
    {
        $sql = "SELECT `loans`.`id`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`, members.gender ,`loans`.`date_disbursed`, `loans`.`loan_amount`,
 @amountapproved:=(loans.loan_amount),
     @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '1530403199' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '1530403199' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance 

FROM `loans` LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE  (`loans`.`status` != 'not collected') and loans.date_disbursed <= '1530403199' HAVING schdl_amnt - credit > 0";

        $data = Yii::$app->db->createCommand($sql)->queryAll();
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $header = ['Region', 'Area', 'Branch', 'Branch Code', 'Project', 'Product', 'Sanction No', 'Inst Amnt', 'Inst Months', 'Name', 'Parentage', 'Gender', 'Date Disbursed', 'Loan Amount', 'Overdue Amount', 'Outstanding Balance'];
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/overdue_report/' . 'overdue_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
        $fopen = fopen($file_path, 'w');

        fputcsv($fopen, $header);
        $i = 0;
        foreach ($data as $g) {
            $arr = [];
            $arr[] = $g['region'];
            $arr[] = $g['area'];
            $arr[] = $g['branch'];
            $arr[] = $g['branch_code'];
            $arr[] = $g['project'];
            $arr[] = $g['product'];
            $arr[] = $g['sanction_no'];
            $arr[] = $g['inst_amnt'];
            $arr[] = $g['inst_months'];
            $arr[] = $g['name'];
            $arr[] = $g['parentage'];
            $arr[] = $g['gender'];
            $arr[] = date('Y-M-d', $g['date_disbursed']);
            $arr[] = $g['loan_amount'];
            $arr[] = $g['overdue_amount'];
            $arr[] = $g['outstanding_balance'];
            fputcsv($fopen, $arr);
            // $i++;
        }
        fclose($fopen);
    }

    /**
     *
     */

    public function actionOverdueAging($date)
    {
        $date = strtotime($date);
        $sql = "SELECT `loans`.`id`,`loans`.`inst_type`,`loans`.`inst_amnt`,`loans`.`inst_months`,regions.name as region , areas.name as area, branches.name as branch,
branches.code as branch_code,projects.name as project,products.name as product, `loans`.`application_id`, `loans`.`sanction_no`, 
`members`.`full_name` AS `name`, `members`.``.`parentage` AS `parentage`, members.gender ,`loans`.`date_disbursed`, `loans`.`loan_amount`,
 @amountapproved:=(loans.loan_amount),
 @schdl_amnt:=(select COALESCE(sum(schedules.schdl_amnt), 0) from schedules where (schedules.loan_id = loans.id and schedules.due_date <= '".$date."' and schedules.due_date > 0)) as schdl_amnt,
 @credit:=(select COALESCE(sum(recoveries.amount), 0) from recoveries where (recoveries.loan_id = loans.id and recoveries.deleted=0 
and recoveries.receive_date <= '".$date."' and recoveries.receive_date >0)) as credit, (@schdl_amnt-@credit) as overdue_amount, 
(@amountapproved-@credit) as outstanding_balance 

FROM `loans` LEFT JOIN `applications` ON `loans`.`application_id` = `applications`.`id`
 INNER JOIN `members` ON `applications`.`member_id` = `members`.`id` 
INNER JOIN `groups` ON `applications`.`group_id` = `groups`.`id`
INNER JOIN `regions` ON `loans`.`region_id` = `regions`.`id` 
INNER JOIN `areas` ON `loans`.`area_id` = `areas`.`id`
 INNER JOIN `branches` ON `loans`.`branch_id` = `branches`.`id` 
INNER JOIN `projects` ON `loans`.`project_id` = `projects`.`id` 
INNER JOIN `products` ON `loans`.`product_id` = `products`.`id` 
WHERE  (`loans`.`status` != 'not collected') and loans.date_disbursed <= '".$date."' HAVING schdl_amnt - credit > 0";

        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $new_data = [];
        foreach ($data as $d) {
            $schedule_count = floor($d['credit'] / $d['inst_amnt']);
            $date_disbursed = date('Y-m-d', $d['date_disbursed']);
            if ($date_disbursed > date("Y-m-10", strtotime($date_disbursed))) {
                $date_disbursed = date('Y-m-20', $d['date_disbursed']);
                $due_date = date("Y-m-10", strtotime('+1 month', strtotime($date_disbursed)));
            } else {
                $due_date = date("Y-m-10", strtotime($date_disbursed));
            }
            $months=DisbursementHelper::getSchdlMonths()[$d['inst_type']];
            $due_date = date("Y-m-10", strtotime("+".$months." months", strtotime($due_date)));
            if($d['inst_months'] == 1){
                $overdue_date = $due_date;
            }else{
                $overdue_date = date("Y-m-10", strtotime("+".$schedule_count." months", strtotime($due_date)));
            }
            $days = ($date - strtotime($overdue_date))/60/60/24;
            /*print_r($d);
            print_r($date_disbursed.',');
            print_r($due_date.',');
            print_r($overdue_date.',');
            print_r($schedule_count.',');
            print_r($days);
            die();*/
            if($days>1 && $days<=30){
                $d['overdue_aging'] = '1 to 30 Days';
            }else if($days>30 && $days<=60){
                $d['overdue_aging'] = '30  to 60 days';
            }else if($days>60 && $days<=90){
                $d['overdue_aging'] = '60  to 90 days';
            }else if($days>90 && $days<=180){
                $d['overdue_aging'] = '90  to 180 days';
            }else if($days>180 && $days<=545){
                $d['overdue_aging'] = 'Next One Year';
            }else if($days>545 && $days<=1315){
                $d['overdue_aging'] = 'Next Two Years';
            }else if($days>1315 && $days<=2410){
                $d['overdue_aging'] = 'Next Three Years';
            }else if($days>2410 && $days<=4235){
                $d['overdue_aging'] = 'Next Five Years';
            }else{
                $d['overdue_aging'] = $days;
            }
            $new_data[] = $d;
        }

        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $header = ['Region', 'Area', 'Branch', 'Branch Code', 'Project', 'Product', 'Sanction No', 'Inst Amnt', 'Inst Months', 'Name', 'Parentage', 'Gender', 'Date Disbursed', 'Loan Amount', 'Overdue Amount', 'Outstanding Balance','Overdue Aging'];
        $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/overdue_report/' . 'overdue_aging_report' . '_' . date('d-m-Y-H-i-s') . '.csv';
        $fopen = fopen($file_path, 'w');

        fputcsv($fopen, $header);
        $i = 0;
        foreach ($new_data as $g) {
            $arr = [];
            $arr[] = $g['region'];
            $arr[] = $g['area'];
            $arr[] = $g['branch'];
            $arr[] = $g['branch_code'];
            $arr[] = $g['project'];
            $arr[] = $g['product'];
            $arr[] = $g['sanction_no'];
            $arr[] = $g['inst_amnt'];
            $arr[] = $g['inst_months'];
            $arr[] = $g['name'];
            $arr[] = $g['parentage'];
            $arr[] = $g['gender'];
            $arr[] = date('Y-M-d', $g['date_disbursed']);
            $arr[] = $g['loan_amount'];
            $arr[] = $g['overdue_amount'];
            $arr[] = $g['outstanding_balance'];
            $arr[] = $g['overdue_aging'];
            fputcsv($fopen, $arr);
            // $i++;
        }
        fclose($fopen);
    }

    public function actionPortfolio()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $params = array();
        $dates = array(
            array('2000-01-01','2020-10-31'),
//            array('2000-01-01','2019-02-28'),
            /*array('1998-01-01', '2012-06-30'),
            array('2012-07-01', '2013-06-30'),
            array('2013-07-01', '2014-06-30'),
            array('2014-07-01', '2015-06-30'),
            array('2015-07-01', '2016-06-30'),
            array('2016-07-01', '2017-06-30'),
            array('2017-07-01', '2018-06-30'),
            array('2018-07-01', '2019-02-28')*/
        );
        foreach ($dates as $date) {
            $searchModel = new PortfolioSearch();
            $data = $searchModel->portfolio_psic($params, $date);
            $duelist = array();
            foreach ($data as $d) {
                /*print_r($d);
                die();*/
                $array = array();
                $array['region'] = $d->region_name;
                $array['area'] = $d->area_name;
                $array['branch'] = $d->branch_name;
                $array['code'] = $d->branch_code;
                $array['sanction_no'] = $d->sanction_no;
                $array['group'] = $d->grpno;
                $array['name'] = $d->name;
                $array['parenatge'] = $d->parentage;
                $array['cnic'] = $d->cnic;
                $array['gender'] = $d->gender;
                $array['dob'] = date('Y-m-d', $d->dob);
                $array['age'] = round($d->age);
                $array['project'] = $d->project;
                $array['loan_amount'] = $d->loan_amount;
                $array['product'] = $d->product;
                $array['purpose'] = $d->purpose;
                $array['datedisburse'] = date('Y-m-d', $d->date_disbursed);
                $array['chequeno'] = $d->cheque_no;
                $array['loanexpiry'] = date('Y-m-d', $d->loan_expiry);
                $array['recovery'] = $d->recovery;
                $array['mobile'] = $d->mobile;
                $array['address'] = $d->address;

                $duelist[] = $array;
            }
            /*print_r($duelist);
            die();*/
            $file_name = 'portfolio_report_' . $date[0] . '_' . $date[1] . '.csv';
            $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/exports/portfolio/' . $file_name;
            $fopen = fopen($file_path, 'w');

            $createColumn = array("Region", "Area", "Branch", "Branch Code", "Sanction No", "Group No", "Name", "Parentage", "CNIC", "Gender", "DOB", "Age", "Project", "Loan Amount", "Product", "Purpose", "Date Disburse", "Cheque No", "Loan Expiry", "Recovery", "Mobile", "Address");
            fputcsv($fopen, $createColumn);

            foreach ($duelist as $d) {
                fputcsv($fopen, $d);
            }

            fclose($fopen);
        }

        /*$archive_reports = ArchiveReports::find()->where(['report_name'=>self::PORTFOLIO_REPORT,'status'=>0])->all();
        $params = array();
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        foreach ($archive_reports as $p){

            $params['PortfolioSearch']['datedisburse'] = isset($p->date_filter) ? $p->date_filter : '';
            $params['PortfolioSearch']['region_id'] = ($p->region_id != 0) ? $p->region_id : '';
            $params['PortfolioSearch']['area_id'] = ($p->area_id != 0) ? $p->area_id : '';
            $params['PortfolioSearch']['branch_id'] = ($p->branch_id != 0) ? $p->branch_id : '';
            $params['PortfolioSearch']['project_id'] = ($p->project_id != 0) ? $p->project_id : '';
            /*print_r($params);
            die();
            $searchModel = new PortfolioSearch();
            $dataProvider = $searchModel->portfolio_psic($params);

            $columns = ArchiveReportsHelper::getPortfolioColumns();
            if(isset($searchModel->project->code)){
                if($searchModel->project->code == 'Kissan'){
                    $columns = ArchiveReportsHelper::getPortfolioKissanColumns();
                }else if ($searchModel->project->code == 'TEVTA'){
                    $columns = ArchiveReportsHelper::getPortfolioTevtaColumns();
                }else if ($searchModel->project->code == 'PSPA'){
                    $columns = ArchiveReportsHelper::getPortfolioPSPAColumns();
                }
            }

            /*print_r($dataProvider->count);
            die();
            $exporter = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => $columns,
                'maxEntriesPerFile' =>500000,
                'resultConfig' => [
                    'forceArchive' => true // always archive the results
                ],
            ]);
            $file_name = 'portfolio_report'.date('d-m-Y-H-i-s').'.zip';
            //$file_path = Yii::$app->basePath.'\exports\\'.$file_name;
            $file_path = Yii::getAlias('@anyname').'/frontend/web'.'/exports/portfolio/'.$file_name;
            /*echo ($file_path);
            die();
            $exporter->export()->saveAs($file_path); // output ZIP archive!
            if(file_exists($file_path)){

                $p->file_path = $file_name;
                /*print_r($p->save());
                die();
                if($p->save(false)){

                }else{
                    $p->getErrors();
                }

            }
        }*/

    }

    public function actionPortfolioWithoutRecovery()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $params = array();
        $dates = array(
            //array('2000-01-01','2019-02-28'),
            array('1998-01-01', '2012-06-30'),
            array('2012-07-01', '2013-06-30'),
            array('2013-07-01', '2014-06-30'),
            array('2014-07-01', '2015-06-30'),
            array('2015-07-01', '2016-06-30'),
            array('2016-07-01', '2017-06-30'),
            array('2017-07-01', '2018-06-30'),
            array('2018-07-01', '2019-03-31')
        );
        foreach ($dates as $date) {
            $searchModel = new PortfolioSearch();
            $data = $searchModel->portfolio_psic($params, $date);
            $duelist = array();
            foreach ($data as $d) {
                /*print_r($d);
                die();*/
                $array = array();
                $array['code'] = $d->branch_code;
                $array['sanction_no'] = $d->sanction_no;
                $array['group'] = $d->grpno;
                $array['name'] = $d->name;
                $array['parenatge'] = $d->parentage;
                $array['cnic'] = $d->cnic;
                $array['gender'] = $d->gender;
                $array['project'] = $d->project;
                $array['loan_amount'] = $d->loan_amount;
                $array['purpose'] = $d->purpose;
                $array['datedisburse'] = date('Y-m-d', $d->date_disbursed);
                $array['mobile'] = $d->mobile;
                $array['address'] = $d->address;

                $duelist[] = $array;
            }
            /*print_r($duelist);
            die();*/
            $file_name = 'portfolio_report_' . $date[0] . '_' . $date[1] . '.csv';
            $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/exports/portfolio/' . $file_name;
            $fopen = fopen($file_path, 'w');

            $createColumn = array("Branch Code", "Sanction No", "Group No", "Name", "Parentage", "CNIC", "Gender", "Project", "Loan Amount", "Purpose", "Date Disburse", "Mobile", "Address");
            fputcsv($fopen, $createColumn);

            foreach ($duelist as $d) {
                fputcsv($fopen, $d);
            }

            fclose($fopen);
        }

        /*$archive_reports = ArchiveReports::find()->where(['report_name'=>self::PORTFOLIO_REPORT,'status'=>0])->all();
        $params = array();
        Yii::setAlias('@anyname', realpath(dirname(__FILE__).'/../../'));
        foreach ($archive_reports as $p){

            $params['PortfolioSearch']['datedisburse'] = isset($p->date_filter) ? $p->date_filter : '';
            $params['PortfolioSearch']['region_id'] = ($p->region_id != 0) ? $p->region_id : '';
            $params['PortfolioSearch']['area_id'] = ($p->area_id != 0) ? $p->area_id : '';
            $params['PortfolioSearch']['branch_id'] = ($p->branch_id != 0) ? $p->branch_id : '';
            $params['PortfolioSearch']['project_id'] = ($p->project_id != 0) ? $p->project_id : '';
            /*print_r($params);
            die();
            $searchModel = new PortfolioSearch();
            $dataProvider = $searchModel->portfolio_psic($params);

            $columns = ArchiveReportsHelper::getPortfolioColumns();
            if(isset($searchModel->project->code)){
                if($searchModel->project->code == 'Kissan'){
                    $columns = ArchiveReportsHelper::getPortfolioKissanColumns();
                }else if ($searchModel->project->code == 'TEVTA'){
                    $columns = ArchiveReportsHelper::getPortfolioTevtaColumns();
                }else if ($searchModel->project->code == 'PSPA'){
                    $columns = ArchiveReportsHelper::getPortfolioPSPAColumns();
                }
            }

            /*print_r($dataProvider->count);
            die();
            $exporter = new CsvGrid([
                'dataProvider' => $dataProvider,
                'columns' => $columns,
                'maxEntriesPerFile' =>500000,
                'resultConfig' => [
                    'forceArchive' => true // always archive the results
                ],
            ]);
            $file_name = 'portfolio_report'.date('d-m-Y-H-i-s').'.zip';
            //$file_path = Yii::$app->basePath.'\exports\\'.$file_name;
            $file_path = Yii::getAlias('@anyname').'/frontend/web'.'/exports/portfolio/'.$file_name;
            /*echo ($file_path);
            die();
            $exporter->export()->saveAs($file_path); // output ZIP archive!
            if(file_exists($file_path)){

                $p->file_path = $file_name;
                /*print_r($p->save());
                die();
                if($p->save(false)){

                }else{
                    $p->getErrors();
                }

            }
        }*/

    }


    public function actionDuelistReport()
    {
        ini_set('memory_limit', '202348M');
        ini_set('max_execution_time', 15000);
        $archive_reports = ArchiveReports::find()->where(['report_name' => 'duelist-report', 'status' => 0])->all();
        $params = array();
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        foreach ($archive_reports as $p) {
            $connection_bank = ConnectionBanks::find()->where(['bank_code' => $p->source])->one();
            if ($p->source == 'omni') {

                $params['DuelistSearch']['report_date'] = isset($p->date_filter) ? $p->date_filter : '';
                $params['DuelistSearch']['region_id'] = ($p->region_id != 0) ? $p->region_id : '';
                $params['DuelistSearch']['area_id'] = ($p->area_id != 0) ? $p->area_id : '';
                $params['DuelistSearch']['branch_id'] = ($p->branch_id != 0) ? $p->branch_id : '';
                /*$params['DuelistSearch']['branch_ids'] = array(
                    '71',	'671',	'75',	'779',	'778',	'775',	'6',	'8',	'10',	'11',	'293',	'294',	'76',	'78',	'80',
                    '81',	'327',	'295',	'408',	'409',	'699',	'698',	'710',	'377',	'712',	'709',	'818',	'522',	'532',	'243',
                    '488',	'85',	'708',	'90',	'91',	'416',	'417',	'418',	'702',	'802',	'92',	'94',	'419',	'421',	'704',
                    '98',	'108',	'115',	'125',	'126',	'555',	'359',	'429',	'748',	'505',	'136',	'367',	'438',	'558',	'144',
                    '443',	'448',	'449',	'451',	'452',	'147',	'151',	'270',	'531',	'539',	'153',	'154',	'155',	'292',	'536',
                    '161',	'299',	'680',	'513',	'596',	'595',	'598',	'788',	'304',	'381',	'384',	'197',	'199',	'385',	'204',
                    '208',	'535',	'508',	'509',	'515',	'516',	'599',	'602',	'603',	'537',	'604',	'590',	'591',	'592',	'593',
                    '797',	'563',	'795',	'756',	'216',	'217',	'693',	'787',	'720',	'722',	'347',	'395',	'658',	'69',	'70',
                    '273',	'274',	'401',	'402',	'668',	'669',	'670',	'746',	'747',	'821'
                );*/
                $params['DuelistSearch']['branch_ids'] = ArchiveReportsHelper::branch_ids($p->branch_codes);
                $params['DuelistSearch']['project_id'] = ($p->project_id != 0) ? $p->project_id : '';
                $searchModel = new DuelistSearch();

                $data = $searchModel->due_list_search($params);
                $file_name = 'duelist_report_omni' . date('d-m-Y-H-i-s') . '.csv';
                //$file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/exports/duelists/' . $file_name;
                $file_path = ImageHelper::getAttachmentPath() . '/exports/duelists/' . $file_name;
                $date = explode(' - ', $p->date_filter);
                $duelist = array();
                $i = 1;
                $pattern = '/^((\+92)|(0092)|(92))-{0,1}\d{3}-{0,1}\d{7}$|^\d{11}$|^\d{4}-\d{7}$/';
                foreach ($data as $d) {
                    $array = array();
                    $array['serial_no'] = $i;
                    $array['name'] = $d['name'];
                    $array['sanction_no'] = str_replace('-', '', $d['cnic']);
                    $array['mobile'] = (substr($d['mobile'], 0, 2) === '92') ? '0' . ltrim($d['mobile'], '92') : $d['mobile'];
                    /*if(preg_match($pattern,$d['mobile'])){
                        $array['mobile'] = $d['mobile'];
                    }else{
                        $array['mobile'] = '0';
                    }*/
                    $array['cnic'] = str_replace('-', '', $d['cnic']);
                    if (isset($connection_bank)) {
                        $array['due_amount'] = $d['due_amount'] + $connection_bank->charges;
                    } else {
                        $array['due_amount'] = $d['due_amount'];
                    }
                    $array['due_date'] = date('m/25/Y', strtotime($date[0]));
                    $array['billing_month'] = date('m/2/Y', strtotime($date[0]));
                    $array['branch_name'] = $d['branch_name'];
                    $i++;
                    $duelist[] = $array;
                }
                /*print_r($data);
                die();*/
                $fileAndFilePath = 'csv/blog-content-' . strtotime("now") . ".csv";
                $fopen = fopen($file_path, 'w');

                $createColumn = array("S #", "Customer Name", "Customer ID", "Customer Mobile #", "Customer CNIC", "Collection Amount", "Due Date", "Billing Month", "Branch Name");
                fputcsv($fopen, $createColumn);

                foreach ($duelist as $d) {
                    fputcsv($fopen, $d);
                }

                fclose($fopen);
                /*$objPHPExcel = new \PHPExcel();
                $objPHPExcel->setActiveSheetIndex(0);

                $rowCount = 1;
                $i = 1;

                $date = explode(' - ', $p->date_filter);
                $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, 'S #');
                $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, 'Customer Name');
                $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, 'Customer ID');
                $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, 'Customer Mobile #');
                $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, 'Customer CNIC');
                $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, 'Collection Amount');
                $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, 'Due Date');
                $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, 'Billing Month');
                foreach ($data as $d) {
                    $rowCount++;
                    if(isset($connection_bank))
                    {
                        $due_amount = $d['due_amount'] + $connection_bank->charges;
                    } else {
                        $due_amount = $d['due_amount'];
                    }
                    $objPHPExcel->getActiveSheet()->SetCellValue('A' . $rowCount, $i++);
                    $objPHPExcel->getActiveSheet()->SetCellValue('B' . $rowCount, $d['name']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('C' . $rowCount, $d['sanction_no']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('D' . $rowCount, $d['mobile']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('E' . $rowCount, $d['cnic']);
                    $objPHPExcel->getActiveSheet()->SetCellValue('F' . $rowCount, $due_amount);
                    $objPHPExcel->getActiveSheet()->SetCellValue('G' . $rowCount, date('Y-m-10',strtotime($date[0])));
                    $objPHPExcel->getActiveSheet()->SetCellValue('H' . $rowCount, date('Y-m-10',strtotime($date[0])));
                }
                $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                $objWriter->save($file_path);*/

            } else if ($p->source == 'telenor') {
                header('Content-type: text/plain');
                $params['DuelistSearch']['report_date'] = isset($p->date_filter) ? $p->date_filter : '';
                $params['DuelistSearch']['region_id'] = ($p->region_id != 0) ? $p->region_id : '';
                $params['DuelistSearch']['area_id'] = ($p->area_id != 0) ? $p->area_id : '';
                $params['DuelistSearch']['branch_id'] = ($p->branch_id != 0) ? $p->branch_id : '';
                /*$params['DuelistSearch']['branch_ids'] = array(
                    '706',	'707',	'95',	'96',	'102',	'106',	'112',	'113',	'120',	'121',	'498',	'810',	'360',	'755'
                );*/
                $params['DuelistSearch']['branch_ids'] = ArchiveReportsHelper::branch_ids($p->branch_codes);
                $params['DuelistSearch']['project_id'] = ($p->project_id != 0) ? $p->project_id : '';
                $searchModel = new DuelistSearch();

                $data = $searchModel->due_list_search($params);
                $file_name = 'duelist_report' . date('d-m-Y-H-i-s') . '.txt';
                //$file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/exports/duelists/' . $file_name;
                $file_path = ImageHelper::getAttachmentPath() . '/exports/duelists/' . $file_name;
                $myfile = fopen($file_path, "w+");
                $str = 'Sr,Group id,Group name,billing month,Within due date amount,Due Date,After due date amount,Sanction No';
                $txt = $str . "\r\n";
                fwrite($myfile, $txt);
                $i = 1;
                $date = explode(' - ', $p->date_filter);
                foreach ($data as $d) {
                    $d['name'] = str_replace(',', '', $d['name']);
                    $d['cnic'] = str_replace('-', '', $d['cnic']);
                    if (isset($connection_bank)) {
                        $due_amount = $d['due_amount'] + $connection_bank->charges;
                    } else {
                        $due_amount = $d['due_amount'];
                    }
                    if (strlen($d['name']) > 20) {
                        $d['name'] = substr($d['name'], 0, 20);
                    }

                    $str = ($i++ . ',' . $d['cnic'] . ',' . $d['name'] . ',' . date('Ym', strtotime($date[0])) . ',' . str_pad(round($due_amount . '00'), 14, '0', STR_PAD_LEFT) . ',' . date('Ym25', strtotime($date[0])) . ',' . str_pad(round($due_amount . '00'), 14, '0', STR_PAD_LEFT)) . ',' . $d['sanction_no'];
                    $txt = $str . "\r\n";
                    fwrite($myfile, $txt);
                }
                fclose($myfile);

            } else if ($p->source == 'bi') {
                $params['DuelistSearch']['report_date'] = isset($p->date_filter) ? $p->date_filter : '';
                $params['DuelistSearch']['region_id'] = ($p->region_id != 0) ? $p->region_id : '';
                $params['DuelistSearch']['area_id'] = ($p->area_id != 0) ? $p->area_id : '';
                $params['DuelistSearch']['branch_id'] = ($p->branch_id != 0) ? $p->branch_id : '';
                $params['DuelistSearch']['project_id'] = ($p->project_id != 0) ? $p->project_id : '';
                /*$params['DuelistSearch']['branch_ids'] = array(
                    '403',	'777',	'77',	'79',	'823',	'410',	'237',	'83',	'350',	'411',	'486',	'525',	'243',	'488',	'248',
                    '88',	'269',	'252',	'324',	'254',	'93',	'420',	'517',	'782',	'96',	'97',	'99',	'100',	'105',	'106',
                    '107',	'109',	'111',	'119',	'122',	'124',	'424',	'427',	'498',	'499',	'520',	'810',	'685',	'148',	'150',
                    '157',	'158',	'159',	'160',	'771',	'686',	'166',	'167',	'168',	'170',	'172',	'175',	'187',	'303',	'195',
                    '198',	'200',	'201',	'202',	'203',	'205',	'206',	'510',	'599',	'600',	'212',	'664',	'373',	'519',	'752',
                    '581',	'374',	'223',	'258',	'259',	'328',	'330',	'495',	'583',	'584',	'758',	'226',	'22',	'229',	'231',
                    '346',	'333',	'233',	'676'
                );*/
                $params['DuelistSearch']['branch_ids'] = ArchiveReportsHelper::branch_ids($p->branch_codes);

                $searchModel = new DuelistSearch();

                $data = $searchModel->due_list_search($params);

                $file_name = 'duelist_report_bi_' . date('d-m-Y-H-i-s') . '.csv';
                //$file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/exports/duelists/' . $file_name;
                $file_path = ImageHelper::getAttachmentPath(). '/exports/duelists/' . $file_name;
                $myfile = fopen($file_path, "w+");
                $i = 1;
                $date = explode(' - ', $p->date_filter);
                fputcsv($myfile, array('Member CNIC', 'Member Name', 'Member Parentage', 'Akhuwat Sanction_no', 'Due Amount', 'Member Mobile Number', 'Due Date'));

                if (!empty($data)) {
                    foreach ($data as $d) {

                        if (isset($connection_bank)) {
                            $due_amount = $d['due_amount'] + $connection_bank->charges;
                        } else {
                            $due_amount = $d['due_amount'];
                        }
                        $arr = array($d['cnic'], $d['name'], $d['parentage'], $d['sanction_no'], $due_amount, $d['mobile'], date('25-m-Y', strtotime($date[0])));
                        fputcsv($myfile, $arr);
                    }
                }

                fclose($myfile);

            }

            if (file_exists($file_path)) {

                $p->file_path = $file_name;
                $p->status = 1;
                if ($p->save(false)) {
                } else {
                    $p->getErrors();
                }

            }
        }
    }

    public function actionCreateReport($code, $date, $period)
    {
        if ($code == 'recv') {
            $name = 'Recovery Summary';

        } elseif ($code == 'don') {
            $name = 'Donation Summary';

        } else {
            $name = 'Disbursement Summary';
        }
        $model = new ArcAccountReports();
        $model->report_name = $name;
        $model->code = $code;
        $model->report_date = strtotime($date);
        $model->period = $period;
        $model->project_id = 0;
        $model->assigned_to = 0;
        $model->save();
        $projects = Projects::find()->where(['deleted' => 0])->all();
        foreach ($projects as $project) {
            $model = new ArcAccountReports();
            $model->report_name = $name;
            $model->code = $code;
            $model->report_date = strtotime($date);
            $model->period = $period;
            $model->project_id = $project->id;
            $model->assigned_to = 0;
            $model->save();
        }
    }

    public function actionCreateReportYearly($code)
    {
        if ($code == 'recv') {
            $name = 'Recovery Summary';

        } elseif ($code == 'don') {
            $name = 'Donation Summary';

        } else {
            $name = 'Disbursement Summary';
        }
        $months = array("31-08-2017", "30-09-2017", "31-10-2017", "30-11-2017", "31-12-2017");
        foreach ($months as $date) {
            $model = new ArcAccountReports();
            $model->report_name = $name;
            $model->code = $code;
            $model->report_date = strtotime($date);
            $model->period = 'monthly';
            $model->project_id = 0;
            $model->assigned_to = 0;
            $model->save();
            $projects = Projects::find()->where(['deleted' => 0])->all();
            foreach ($projects as $project) {
                $model = new ArcAccountReports();
                $model->report_name = $name;
                $model->code = $code;
                $model->report_date = strtotime($date);
                $model->period = 'monthly';
                $model->project_id = $project->id;
                $model->assigned_to = 0;
                $model->save();
            }
        }
    }

    /**
     * Finds the Loans model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ProgressReports the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ArchiveReports::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}