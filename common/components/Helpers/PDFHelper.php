<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

use common\models\Branches;
use common\models\ConnectionBanks;
use common\models\PDF;
use Yii;

class PDFHelper{
    public static function getInstlls($id,$params)
    {
        $due_date=date('Y-m-10',strtotime($params['DuelistSearch']['report_date']));
        $connection = Yii::$app->db;
        $select_query = 'SELECT count( * ) as recv_count FROM schedules WHERE loan_id = "'.$id.'" AND due_date <= "'.strtotime($due_date).'"';
        $count=$connection->createCommand($select_query)->queryAll();
        $installment_no=$count[0]['recv_count'];
        return $installment_no;
    }
    public static function DueList($data,$pdf_heading,$name)
    {
        $pdf = new  PDF();
        $pdf->heading=$pdf_heading;
        $header = array(
            'snaction_no'=>'Sanct No',
            'name'=>'Name',
            'parentage'=>'Parentage',
            'date_disburse'=>'Disb Date',
            'dis_amount'=>'Disb. Amount',
            'due_date'=>'Due Date',
            'due_amnt'=>'Due Amount',
            'install_no'=>'Install #',
            'recv_amnt'=>'Cum Recov',
            'balance'=>'Balance',
            'mobile'=>'Mobile',
            'team'=>'Team',
            'address'=>'Address'
        );
        $header_pr = array(
            'no_of_loans'=>'Total Loans',
            'family_loans'=>'Male Loans',
            'female_loans'=>'Female Loans',
            'active_loans'=>'Active Loans',
            'cum_disb'=>'	Cum. Disb',
            'cum_due'=>'Cum. Due',
            'cum_recv'=>'Cum. Recv',
            'overdue_borrowers'=>'OD Borrowers',
            'overdue_amount'=>'OD Amount',
            'overdue_percentage'=>'OD Percentage',
            'par_amount'=>'PAR',
            'par_percentage'=>'PAR Percentage',
            'not_yet_due'=>'Not Yet Due',
            'olp_amount'=>'OLP',
            'recovery_percentage'=>'Recovery Percentage'
        );
        $pdf->SetTitle('Duelist of the month '.date('F-Y'). ' of Branch '. $name['name']);
        $pdf->SetFont('Arial','',6);
        $pdf->SetMargins(3,2,0);
        $pdf->AddPage('L');
        $pdf->BasicTablePR($header_pr,$data['progress_report_new']);
        $pdf->BasicTable($header,$data['new_duelist1']);
        $pdf->SetDisplayMode('fullwidth', 'continuous');
        $filename = 'duelist_'.'1'.'_'.'pdf';
        $filename = 'duelist_'.$name['name'].'_'.date('F-Y', (strtotime ( date('Y-m') ))).'.pdf';

        //die($filename);
        $pdf->Output('D',$filename);

    }
}