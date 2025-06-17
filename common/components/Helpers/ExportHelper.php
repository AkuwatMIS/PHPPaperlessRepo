<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

class ExportHelper{

    public static function ExportCSV($filename,$heading,$data,$progress='')
    {
        ini_set('memory_limit','2G');
        ini_set('max_execution_time', 3600);

        header('Content-Type:text/csv;charset=utf8');
        header('Content-Disposition:attachment;filename='.$filename.'.csv');
        $output=fopen("php://output","w");

        if(!empty($progress) || !isset($progress)){
            fputcsv($output,$progress['header']);
            fputcsv($output,$progress['data'][0]);
        }
        fputcsv($output,$heading);

        foreach ($data as $row)
        {
            fputcsv($output,$row);
        }

        fclose($output);
    }

    public static function parseProgressReportExportData($data)
    {
        $i = 0;
        $progress=[];
        foreach ($data as $d) {
            $progress[$i]['region_name'] = $d['region_name'];
            $progress[$i]['area_name'] = $d['area_name'];
            $progress[$i]['branch_code'] = $d['branch_code'];
            $progress[$i]['branch_name'] = $d['branch_name'];
            $progress[$i]['project_name'] = $d['project_name'];
            $progress[$i]['opening_active_loans'] = $d['opening_active_loans'];
            $progress[$i]['opening_olp'] = $d['opening_olp'];
            $progress[$i]['disb_loans'] = $d['disb_loans'];
            $progress[$i]['disb_amount'] = $d['disb_amount'];
            $progress[$i]['recv_amount'] = $d['recv_amount'];
            $progress[$i]['closing_active_loans'] = $d['closing_active_loans'];
            $progress[$i]['closing_olp'] = $d['closing_olp'];

            $i++;
        }
        return $progress;
    }
    static public function parseDueListPdfExportData($new_duelist2,$params){
        $new_duelist=[];
        foreach($new_duelist2 as $key => $duelist){
            $installment_no=PDFHelper::getInstlls($duelist['id'],$params);
            $array_one = array();
            $array_one['grpno'] = $duelist['grpno'];
            $array_one['grptype'] = $duelist['grptype'];
            $array_one['sanction_no'] = $duelist['sanction_no'];
            $array_one['name'] = $duelist['name'];
            $array_one['parentage'] = $duelist['parentage'];
            $array_one['date_disburse'] = date('Y-m-d',$duelist['date_disbursed']);
            $array_one['dis_amount'] = $duelist['loan_amount'];
            $array_one['due_date'] = isset($params['DuelistSearch']['report_date']) && !empty($params['DuelistSearch']['report_date']) ? ($params['DuelistSearch']['report_date']) : date('Y-m');
            //$array_one['install_no'] = ($installment_no == 0) ? 1 :$installment_no;
            $schedule_amount = $duelist['inst_amnt'];
            $credit = $duelist['credit'];
            $outstanding_balance = $duelist['outstanding_balance'];
            if ($schedule_amount > $outstanding_balance) {
                $array_one['due_amnt']  = number_format($outstanding_balance);
            } else {
                if (($schedule_amount - $credit) > 0) {
                    $array_one['due_amnt']= round($duelist['due_amount']);
                    //$array_one['due_amnt'] = number_format($schedule_amount - $credit);
                } else {
                    $array_one['due_amnt']  = number_format($schedule_amount);
                }
            }
            $array_one['install_no'] = ($installment_no == 0) ? 1 :$installment_no;
            $array_one['recv_amnt'] = number_format($duelist['credit']);
            $array_one['balance'] = number_format($duelist['outstanding_balance']);
            $array_one['mobile'] = $duelist['mobile'];
            $array_one['team'] = $duelist['team_name'];
            $array_one['address'] = $duelist['address'];
            //$total += $array_one['due_amnt'];
            $new_duelist[] = $array_one;
        }
        return $new_duelist;
    }
    static public function parseDueListCsvExportData($data,$date_disbursed){
        $i = 0;
        $groups=[];
        
        foreach ($data as $g) {
            $schedule_amount = $g['inst_amnt'];
            $credit = $g['credit'];
            $outstanding_balance = $g['outstanding_balance'];

            $groups[$i]['sanction_no'] = $g['sanction_no'];
            $groups[$i]['member_name'] = $g['name'];
            $groups[$i]['parentage'] = $g['parentage'];
            $groups[$i]['team_id'] = $g['team_name'];
            $groups[$i]['date_disburse'] = date('Y-m-d',$g['date_disbursed']);
            $groups[$i]['date_disbursed'] = $date_disbursed;
            $groups[$i]['loan_amount'] = $g['loan_amount'];
            $groups[$i]['tranch_amount'] = isset($g['tranch_amount'])? $g['tranch_amount'] : 0;
            $groups[$i]['tranch_no'] =  isset($g['tranch_no'])? $g['tranch_no'] : 0;
            if ($schedule_amount > $outstanding_balance) {
                $groups[$i]['due_amount'] = $outstanding_balance;
            } else {
                if (($schedule_amount - $credit) > 0) {
                    $groups[$i]['due_amount']=round($g['due_amount']);
                    //$groups[$i]['due_amount'] = ($schedule_amount - $credit);
                } else {
                    $groups[$i]['due_amount'] = $schedule_amount;
                }
            }

            $groups[$i]['credit'] = ($g['credit'] > 0) ? $g['credit'] : 0;
            $groups[$i]['outstanding_balance'] = ($g['outstanding_balance'] > 0) ? $g['outstanding_balance'] : 0;
            $groups[$i]['grpno'] = $g['grpno'];
            $groups[$i]['address'] = $g['address'];
            $groups[$i]['mobile'] = $g['mobile'];
            $i++;
        }
        return $groups;
    }

    static public function parseOverDueListCsvExportData($data){
        $i = 0;
        $groups=[];
        foreach ($data as $g) {
            $groups[$i]['sanction_no'] = $g['sanction_no'];
            $groups[$i]['member_name'] = $g['application']['member']['full_name'];
            $groups[$i]['parentage'] = $g['application']['member']['parentage'];
            $groups[$i]['date_diabursed'] = date('Y-M-d', $g['date_disbursed']);
            $groups[$i]['loan_amount'] = $g['loan_amount'];
            $groups[$i]['overdue_amount'] = $g['overdue_amount'];
            $groups[$i]['outstanding_balance'] = $g['outstanding_balance'];
            $i++;
        }
        return $groups;
    }
    static public function parseMegaDisbCsvExportData($data){
        $i = 0;
        $groups=[];
        foreach ($data as $g) {
            $groups[$i]['region'] = $g['region']['name'];
            $groups[$i]['area'] = $g['area']['name'];
            $groups[$i]['branch'] = $g['branch']['name'];
            $groups[$i]['branch_code'] = $g['branch']['code'];
            $groups[$i]['sanction_no'] = $g['sanction_no'];
            $groups[$i]['loan_amount'] = $g['loan_amount'];
            $groups[$i]['grp_no'] = $g['group']['grp_no'];
            $groups[$i]['name'] = $g['application']['member']['full_name'];
            $groups[$i]['parentage'] = $g['application']['member']['parentage'];
            $groups[$i]['cnic'] = $g['application']['member']['cnic'];
            $groups[$i]['gender'] = $g['application']['member']['gender'];
            $groups[$i]['religion'] = $g['application']['member']['religion'];
            $groups[$i]['dob'] = date('Y-m-d',$g['application']['member']['dob']);
            $groups[$i]['education'] =isset($g['application']['member']['education'])?$g['application']['member']['education']:'';
            $groups[$i]['marital_status'] = isset($g['application']['member']['marital_status'])?$g['application']['member']['marital_status']:'';
            $groups[$i]['mobile'] = isset($g['application']['member']['membersMobile']['phone'])?$g['application']['member']['membersMobile']['phone']:'';
            $groups[$i]['address'] = isset($g['application']['member']['homeAddress']['address'])?$g['application']['member']['homeAddress']['address']:'';
            $groups[$i]['project'] = isset($g['project']['name'])?$g['project']['name']:'';
            $groups[$i]['product'] = isset($g['product']['name'])?$g['product']['name']:'';
            $groups[$i]['activity'] = isset($g['activity']['name'])?$g['activity']['name']:'';
            $i++;
        }
        return $groups;
    }
    static public function parseDueVsRecCsvExportData($data){
        $i = 0;
        $groups=[];
        foreach ($data as $g) {
            $groups[$i]['sanction_no'] = $g['sanction_no'];
            $groups[$i]['member_name'] = $g['name'];
            $groups[$i]['parentage'] = $g['parentage'];
            $groups[$i]['team_name'] = $g['team_name'];
            $groups[$i]['date_disbursed'] = date('Y-m-d',$g['date_disbursed']);
            $groups[$i]['loan_amount'] = $g['loan_amount'];
            $groups[$i]['tranch_amount'] = $g['tranch_amount'];
            $groups[$i]['tranch_no'] = $g['tranch_no'];
            $groups[$i]['due_amount'] = ($g['due_amount']>0?$g['due_amount']:0);
            $groups[$i]['this_month_recovery'] = $g['this_month_recovery'];
            $groups[$i]['outstanding_balance'] = $g['outstanding_balance'];
            $groups[$i]['grpno'] = $g['grpno'];
            $groups[$i]['address'] = $g['address'];
            $groups[$i]['mobile'] = $g['mobile'];
            $i++;
        }
        return $groups;
    }
    static public function parsePortfolioCsvExportData($data){
        $i = 0;
        //$array = \common\components\Helpers\StructureHelper::getStructure('regions');
        $groups=[];
        foreach ($data as $g) {

            /*$array = \common\components\Helpers\StructureHelper::getStructureList('regions', 'id', $g['region_id']);
            $groups[$i]['region_id']=isset($array['0']['name'])?$array['0']['name']:'Not Set';

            $array = \common\components\Helpers\StructureHelper::getStructureList('areas', 'id', $g['area_id']);
            $groups[$i]['area_id']=isset($array['0']['name'])?$array['0']['name']:'Not Set';

            $array = \common\components\Helpers\StructureHelper::getStructureList('branches', 'id', $g['branch_id']);
            $groups[$i]['branch_id']=isset($array['0']['name'])?$array['0']['name']:'Not Set';


            $array = \common\components\Helpers\StructureHelper::getStructureList('projects', 'id', $g['project_id']);
            $groups[$i]['project_id']=isset($array['0']['name'])?$array['0']['name']:'Not Set';*/
            $cheque = isset($g['cheque_no'])?"'".$g['cheque_no']:'';
            $groups[$i]['region_id']=$g['region_name'];
            $groups[$i]['area_id']=$g['area_name'];
            $groups[$i]['branch_id']=$g['branch_name'];
            $groups[$i]['project_id']=$g['project_name'];
            $groups[$i]['sanction_no'] = isset($g['sanction_no'])?$g['sanction_no']:'';
            $groups[$i]['name'] = $g['name'];
            $groups[$i]['parentage'] = $g['parentage'];
            $groups[$i]['cnic'] = $g['cnic'];
            $groups[$i]['date_diabursed'] = isset($g['date_disbursed'])?date('Y-M-d', $g['date_disbursed']):'';
            $groups[$i]['loan_amount'] = isset($g['loan_amount'])?$g['loan_amount']:'';
            $groups[$i]['tranch_amount'] = isset($g['tranch_amount'])?$g['tranch_amount']:'';
            $groups[$i]['tranch_no'] = isset($g['tranch_no'])?$g['tranch_no']:'';
            $groups[$i]['grpno'] = $g['grpno'];
            $groups[$i]['gender'] = $g['gender'];
            $groups[$i]['loan_expiry'] = isset($g['loan_expiry'])?date('Y-M-d',$g['loan_expiry']):'';
            $groups[$i]['cheque_no'] = "'".$cheque."'";
            $groups[$i]['mobile'] = $g['mobile'];
            $groups[$i]['address'] = $g['address'];
            $groups[$i]['recovery'] = $g['recovery'];
            $groups[$i]['purpose'] = isset($g['activity_name'])?$g['activity_name']:'';
            $groups[$i]['status'] = isset($g['status'])?$g['status']:'';
            $groups[$i]['inst_amnt'] = isset($g['inst_amnt'])?$g['inst_amnt']:'';
            $groups[$i]['inst_months'] = isset($g['inst_months'])?$g['inst_months']:'';
            $groups[$i]['cnic_issue_date'] = isset($g['cnic_issue_date'])?$g['cnic_issue_date']:'';
            $groups[$i]['cnic_expiry_date'] = isset($g['cnic_expiry_date'])?$g['cnic_expiry_date']:'';
            /*if($params['PortfolioSearch']['project_id']==22){
                $application=Applications::find(['id'=>$g['application_id']])->one();
                foreach ($application->projectsDisabled as $a){
                    $groups[$i]['disability'] = $a->disability;
                    $groups[$i]['nature'] = $a->nature;
                    $groups[$i]['physical_disability'] = $a->physical_disability;
                    $groups[$i]['visual_disability'] = $a->visual_disability;
                    $groups[$i]['disability_instruments'] = $a->disabilities_instruments;
                }
            }
            if($params['PortfolioSearch']['project_id']==17){
                $application=Applications::find(['id'=>$g['application_id']])->one();
                foreach ($application->projectsTevta as $a){
                    $groups[$i]['institute_name'] = $a->institute_name;
                    $groups[$i]['type_of_diploma'] = $a->type_of_diploma;
                    $groups[$i]['duration_of_diploma'] = $a->duration_of_diploma;
                    $groups[$i]['pbte_or_ttb'] = $a->pbte_or_ttb;
                }
            }

            if($params['PortfolioSearch']['project_id']==3){
                $application=Applications::find(['id'=>$g['application_id']])->one();
                foreach ($application->projectsAgriculture as $a){
                    $groups[$i]['owner'] = $a->owner;
                    $groups[$i]['land_area_size'] = $a->land_area_size;
                    $groups[$i]['land_area_type'] = $a->land_area_type;
                    $groups[$i]['village_name'] = $a->village_name;
                    $groups[$i]['crop_type'] = $a->crop_type;
                    $groups[$i]['crops'] = $a->crops;

                }
            }*/
            $i++;
        }
        return $groups;
    }

    static public function parsePortfolioCsvExportDataKpp($data){
        $i = 0;
        $groups=[];
        foreach ($data as $g) {
            $cheque = isset($g['cheque_no'])?"'".$g['cheque_no']:'';
            $groups[$i]['region_id']=$g['region_name'];
            $groups[$i]['area_id']=$g['area_name'];
            $groups[$i]['branch_id']=$g['branch_name'];
            $groups[$i]['project_id']=$g['project_name'];
            $groups[$i]['sanction_no'] = isset($g['sanction_no'])?$g['sanction_no']:'';
            $groups[$i]['name'] = $g['name'];
            $groups[$i]['parentage'] = $g['parentage'];
            $groups[$i]['cnic'] = $g['cnic'];
            $groups[$i]['date_diabursed'] = isset($g['date_disbursed'])?date('Y-M-d', $g['date_disbursed']):'';
            $groups[$i]['loan_amount'] = isset($g['loan_amount'])?$g['loan_amount']:'';
            $groups[$i]['tranch_amount'] = isset($g['tranch_amount'])?$g['tranch_amount']:'';
            $groups[$i]['tranch_no'] = isset($g['tranch_no'])?$g['tranch_no']:'';
            $groups[$i]['grpno'] = $g['grpno'];
            $groups[$i]['gender'] = $g['gender'];
            $groups[$i]['loan_expiry'] = isset($g['loan_expiry'])?date('Y-M-d',$g['loan_expiry']):'';
            $groups[$i]['cheque_no'] = "'".$cheque."'";
            $groups[$i]['mobile'] = $g['mobile'];
            $groups[$i]['address'] = $g['address'];
            $groups[$i]['recovery'] = $g['recovery'];
            $groups[$i]['purpose'] = isset($g['activity_name'])?$g['activity_name']:'';
            $groups[$i]['status'] = isset($g['status'])?$g['status']:'';
            $groups[$i]['inst_amnt'] = isset($g['inst_amnt'])?$g['inst_amnt']:'';
            $groups[$i]['inst_months'] = isset($g['inst_months'])?$g['inst_months']:'';
            $groups[$i]['funding_source'] = isset($g['funding_source'])?$g['funding_source']:'';
            $i++;
        }
        return $groups;
    }

    static public function parseFamilyMemberCsvExportData($data){
        $i = 0;
        $groups=[];
        foreach ($data as $g) {
            $groups[$i]['region_id'] = $g['region']['name'];
            $groups[$i]['area_id'] = $g['area']['name'];
            $groups[$i]['branch_id'] = $g['branch']['name'];
            $groups[$i]['project_id'] = $g['project']['name'];
            $groups[$i]['member_name'] = $g['application']['member']['full_name'];
            $groups[$i]['member_parentage'] = $g['application']['member']['parentage'];
            $groups[$i]['member_cnic'] = $g['application']['member']['cnic'];
            $groups[$i]['family_member_cnic'] = $g['application']['member']['family_member_cnic'];
            $groups[$i]['loan_amount'] = $g['loan_amount'];
            $groups[$i]['sanction_no'] = $g['sanction_no'];
            $groups[$i]['report_date'] = date('Y-m-d',$g['date_disbursed']);

            $i++;
        }
        return $groups;
    }
    static public function parsePortfolioLwcCsvExportData($data){
        $i = 0;
        $groups=[];
        foreach ($data as $g) {
            $groups[$i]['project_id'] = isset($g['project']['name'])?$g['project']['name']:'';
            $groups[$i]['cnic'] = $g['cnic'];
            $groups[$i]['name'] = $g['name'];
            $groups[$i]['parentage'] = $g['parentage'];
            $groups[$i]['sanction_no'] = isset($g['sanction_no'])?$g['sanction_no']:'';
            $groups[$i]['gender'] = $g['gender'];
            $groups[$i]['marital_status'] = $g['marital_status'];
            $groups[$i]['dob'] = date('Y-m-d',$g['dob']);
            $groups[$i]['branch_id'] = isset($g['branch']['name'])?$g['branch']['name']:'';
            $groups[$i]['loan_amount'] = isset($g['loan_amount'])?$g['loan_amount']:'';
            $groups[$i]['inst_type'] = isset($g['inst_months'])?$g['inst_months']:'';
            $groups[$i]['date_diabursed'] = isset($g['date_disbursed'])?date('Y-M-d', $g['date_disbursed']):'';
            $i++;
        }
        return $groups;
    }
    static public function parseChequewiseCsvExportData($data){
        $i=0;
        $groups=[];
        foreach ($data as $g){
            $cheque = "'".$g['cheque_no']."'";
            $groups[$i]['region_id'] = $g['loan']['region']['name'];
            $groups[$i]['area_id'] = $g['loan']['area']['name'];
            $groups[$i]['branch_id'] = $g['loan']['branch']['name'];
            $groups[$i]['date_disbursed'] = date('Y-M-d',($g['date_disbursed']));
            $groups[$i]['cheque_no'] = ".$cheque." ;
            $groups[$i]['sanction_no'] = $g['loan']['sanction_no'];
            $groups[$i]['loan_amount'] = number_format($g['loan']['loan_amount']);
            $groups[$i]['tranch_amount'] = number_format($g['tranch_amount']);
            $groups[$i]['tranch_no'] = $g['tranch_no'];
            $groups[$i]['group_no'] = $g['loan']['application']['group']['grp_no'];
            $groups[$i]['member_name'] = $g['loan']['application']['member']['full_name'];
            $groups[$i]['member_cnic'] = $g['loan']['application']['member']['cnic'];
            $groups[$i]['member_parentage'] = $g['loan']['application']['member']['parentage'];
            $groups[$i]['project_id'] = $g['loan']['project']['name'];
            $groups[$i]['inst_type'] = $g['loan']['inst_type'];
            $groups[$i]['inst_months'] = $g['loan']['inst_months'];
            $i++;
        }
        return $groups;
    }

    static public function parseLoansListCsvExportData($data){
        $i=0;
        $groups=[];
        foreach ($data as $g){
            $groups[$i]['member_name'] = isset($g['application']['member']['full_name'])?$g['application']['member']['full_name']:'';
            $groups[$i]['member_cnic'] = isset($g['application']['member']['cnic'])?$g['application']['member']['cnic']:'';
            $groups[$i]['application_no'] = isset($g['application']['application_no'])?$g['application']['application_no']:'';
            $groups[$i]['sanction_no'] = isset($g['sanction_no'])?$g['sanction_no']:'';
            $groups[$i]['loan_amount'] = isset($g['loan_amount'])?$g['loan_amount']:'';
            $groups[$i]['inst_amnt'] = isset($g['inst_amnt'])?$g['inst_amnt']:'';
            $groups[$i]['inst_months'] = isset($g['inst_months'])?$g['inst_months']:'';
            $groups[$i]['date_disbursed'] = date('Y-M-d',isset($g['date_disbursed'])?$g['date_disbursed']:0);
            $groups[$i]['group_no'] = isset($g['group']['grp_no'])?$g['group']['grp_no']:'';
            $groups[$i]['status'] = isset($g['status'])?$g['status']:'';
            $groups[$i]['project_id'] = isset($g['project']['name'])?$g['project']['name']:'';
            $groups[$i]['region_id'] = isset($g->region->name)?$g->region->name:'';
            $groups[$i]['area_id'] =isset($g->area->name)?$g->area->name:'';
            $groups[$i]['branch_id'] = isset($g->branch->name)?$g->branch->name:'';
            $groups[$i]['team_id'] = isset($g->team->name)?$g->team->name:'';
            $groups[$i]['field_id'] = isset($g->field->name)?$g->field->name:'';
            $i++;
        }
        return $groups;
    }
    static public function parseChequePrintCsvExportData($data){
        $i = 0;
        $groups=[];
        foreach ($data as $g) {
            $groups[$i]['sanction_no'] = $g['loan']['sanction_no'];
            $groups[$i]['inst_type'] = $g['loan']['inst_type'];
            $groups[$i]['inst_months'] = $g['loan']['inst_months'];
            $groups[$i]['date'] = gmdate('Y-M-d');
            $groups[$i]['description'] = $g['loan']['application']['member']['full_name'].'('.$g['loan']['application']['member']['cnic'].')';
            $groups[$i]['approved_amount'] = $g['loan']['loan_amount'];
            $i++;
        }
        return $groups;
    }

    static public function parseCreditCsvExportData($data){
        $i = 0;
        $groups=[];
        foreach ($data as $g) {
            $groups[$i]['region_id']=$g['region'];
            $groups[$i]['area_id']=$g['area'];
            $groups[$i]['branch_id']=$g['branch'];
            $groups[$i]['application_date'] = isset($g['application_date'])?date('Y-M-d',$g['application_date']):'0';
            $groups[$i]['application_no']=$g['app_no'];
            $groups[$i]['name'] = $g['full_name'];
            $groups[$i]['cnic'] = $g['cnic'];
            $groups[$i]['status'] = isset($g['status'])?$g['status']:'';
            $groups[$i]['date_diabursed'] = isset($g['date_disbursed'])?date('Y-M-d', $g['date_disbursed']):'0';
            $groups[$i]['sanction_no'] = isset($g['sanction_no'])?$g['sanction_no']:'';
            $groups[$i]['grp_no'] = $g['grp_no'];
            $groups[$i]['cheque_no'] = $g['cheque_no'];
            $groups[$i]['member_count'] = $g['member_count'];
            $groups[$i]['project_id']=$g['project'];
            $i++;
        }
        return $groups;
    }
}