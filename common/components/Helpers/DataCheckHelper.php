<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components\Helpers;

use common\components\Helpers\ReportsHelper;
use common\models\ArcAccountReports;
use common\models\Branches;
use common\models\ProgressReports;
use Yii;
use yii\data\SqlDataProvider;
use yii\helpers\ArrayHelper;

class DataCheckHelper
{
    public static function actionDataCheckAdd($data,$report_type)
    {
        if($report_type==4) {

            foreach ($data as $key => $datacheck) {

                $name = explode(' ', str_replace(",", "", str_replace(array("\r\n", "\r", "\n"), "", trim($datacheck['full_name']))));
                $father_name = explode(' ', str_replace(",", "", str_replace(array("\r\n", "\r", "\n"), "", trim($datacheck['parentage']))));
                $first_name = $middle_name = $last_name = '';
                $f_first_name = $f_middle_name = $f_last_name = '';
                if (!isset($name[2])) {
                    $first_name = $name[0];
                    $middle_name = '';
                    $last_name = isset($name[1]) ? $name[1] : '';
                } else {
                    $first_name = $name[0];
                    $middle_name = $name[1];
                    $last_name = $name[2];
                }
                if (!isset($father_name[2])) {
                    $f_first_name = $father_name[0];
                    $f_middle_name = '';
                    $f_last_name = isset($father_name[1]) ? $father_name[1] : '';
                } else {
                    $f_first_name = $father_name[0];
                    $f_middle_name = $father_name[1];
                    $f_last_name = isset($father_name[2]) ? $father_name[2] : '';
                }
                if ($datacheck['tehsil_name'] != $datacheck['city_name']) {
                    /*$datacheck['address1'] = $datacheck['address1'] . ' ' . (trim($datacheck['tehsil_name']) == 'NA' || trim($datacheck['tehsil_name']) == 'Other') ? $datacheck['tehsil_name'] : '';*/
                    if (trim($datacheck['tehsil_name']) == 'NA' || trim($datacheck['tehsil_name']) == 'Other') {
                        $datacheck['address1'] = $datacheck['address1'] . ' ' . $datacheck['tehsil_name'];
                    }
                }

                $dob_array = array('01/01/1970', '01/30/1970', '01/31/1970', '01/01/1970');
                $array_one = array();
                $array_one['FileNo'] = '';
                $array_one['Branch'] = $datacheck['code'];
                $array_one['NIC'] = '';
                $array_one['CNIC'] = preg_replace('/\D+/', '', $datacheck['cnic']);
                $array_one['First_Name'] = preg_replace('/[^A-Za-z0-9\-]/', '', trim($first_name));
                $array_one['Middle_Name'] = preg_replace('/[^A-Za-z0-9\-]/', '', trim($middle_name));
                $array_one['Last_Name'] = preg_replace('/[^A-Za-z0-9\-]/', '', trim($last_name));
                $array_one['Applicants_Birth_Name'] = '';
                $array_one['Date_of_Birth'] = $datacheck['dob'];
                if (in_array($datacheck['dob'], $dob_array)) {
                    // $array_one['Date_of_Birth'] = '0';
                    $array_one['Date_of_Birth'] = date('d/m/Y', (strtotime($datacheck['dob'])));
                } else {
                    if (date('Y-m-d', (strtotime($datacheck['dob']))) > '2002-01-01') {
                        $array_one['Date_of_Birth'] = '0';
                    } else {
                        $array_one['Date_of_Birth'] = date('d/m/Y', (strtotime($datacheck['dob'])));
                    }
                }
                $array_one['Gender'] = $datacheck['gender'];
                $array_one['Spouse_First_Name'] = '';
                $array_one['Spouse_Middle_Name'] = '';
                $array_one['Spouse_Last_Name'] = '';
                $array_one['Spouses_Birth_Name'] = '';
                $array_one['Father_First_Name'] = $f_first_name;
                $array_one['Father_Middle_Name'] = $f_middle_name;
                $array_one['Father_Last_Name'] = $f_last_name;
                $array_one['Fathers_Birth_Name'] = '';
                $array_one['Mother_First_Name'] = '';
                $array_one['Mother_Middle_Name'] = '';
                $array_one['Mother_Last_Name'] = '';
                $array_one['Mothers_Birth_Name'] = '';
                $array_one['Address'] = preg_replace('/[^0-9a-zA-Z_\s]/', '', trim(str_replace(",", "", $datacheck['address1'])));
                //$array_one['Address'] = $datacheck['address1'];
                $array_one['Village_Name'] = '';
                $array_one['Chak'] = '';


                if ($datacheck['uc'] == 'NA' || $datacheck['uc'] == 'NA ' || $datacheck['uc'] == 'N/A' || $datacheck['uc'] == 'other' || $datacheck['uc'] == 'Other') {
                    $array_one['UC'] = '';

                } else {
                    $array_one['UC'] = str_replace(",", "", $datacheck['uc']);
                }
                $array_one['TehsilTown'] = '';
                $array_one['CityDistrict'] = str_replace(",", "", $datacheck['city_name']);
                $array_one['Elec_ConsumerRefNo'] = '';
                $array_one['SUI_Gas_CustomerNo'] = '';
                $array_one['PhoneNo'] = preg_replace('/\D+/', '', $datacheck['landline']);
                $array_one['CellNo'] = preg_replace('/\D+/', '', $datacheck['mobile']);
                $array_one['Doing_Business_As'] = '';
                $array_one['Ownership_Status'] = '';
                $array_one['Business_Category'] = '';
                $array_one['Business_Address'] = '';
                $array_one['Village_Name_B'] = '';
                $array_one['Chak_B'] = '';
                $array_one['UC_B'] = '';
                $array_one['TehsilTown_B'] = '';
                $array_one['CityDistrict_B'] = '';
                $array_one['PhoneNo_B'] = '';
                $array_one['CellNo_B'] = '';
                $array_one['Account_No'] = $datacheck['sanction_no'];
                $array_one['Account_Type'] = 'IN';
                $array_one['Account_Opening_Date'] = date('t/m/Y', (strtotime($datacheck['datedisburse'])));
                $array_one['Maturity_Date'] = date('t/m/Y', (strtotime($datacheck['loanexpiry'])));
                $array_one['Amount_Distributed'] = $datacheck['loan_amount'];
                $array_one['Terms'] = 'INT';
                $array_one['Elec_Alert'] = 'Y';
                $array_one['Association_Type'] = $datacheck['grp_type'] == 'GRP' ? 'GRP' : 'PRN';
                $array_one['Group_Id'] = $datacheck['grp_type'] == 'GRP' ? $datacheck['grp_no'] : '';
                $new_datacheck[] = $array_one;
            }
            return $new_datacheck;
        }
        else{
            foreach ($data as $key => $datacheck) {
                $name = explode(' ', str_replace(",", "", str_replace(array("\r\n", "\r", "\n"), "", trim($datacheck['full_name']))));
                $father_name = explode(' ', str_replace(",", "", str_replace(array("\r\n", "\r", "\n"), "", trim($datacheck['parentage']))));
                $first_name = $middle_name = $last_name = '';
                $f_first_name = $f_middle_name = $f_last_name = '';
                if (!isset($name[2])) {
                    $first_name = $name[0];
                    $middle_name = '';
                    $last_name = isset($name[1]) ? $name[1] : '';
                } else {
                    $first_name = $name[0];
                    $middle_name = $name[1];
                    $last_name = $name[2];
                }
                if (!isset($father_name[2])) {
                    $f_first_name = $father_name[0];
                    $f_middle_name = '';
                    $f_last_name = isset($father_name[1]) ? $father_name[1] : '';
                } else {
                    $f_first_name = $father_name[0];
                    $f_middle_name = $father_name[1];
                    $f_last_name = isset($father_name[2]) ? $father_name[2] : '';
                }
                if ($datacheck['tehsil_name'] != $datacheck['city_name']) {
                    /*$datacheck['address1'] = $datacheck['address1'] . ' ' . (trim($datacheck['tehsil_name']) == 'NA' || trim($datacheck['tehsil_name']) == 'Other') ? $datacheck['tehsil_name'] : '';*/
                    if (trim($datacheck['tehsil_name']) == 'NA' || trim($datacheck['tehsil_name']) == 'Other') {
                        $datacheck['address1'] = $datacheck['address1'] . ' ' . $datacheck['tehsil_name'];
                    }
                }

                $dob_array = array('01/01/1970', '01/30/1970', '01/31/1970', '01/01/1970');
                $array_one = array();
                $array_one['File No'] = '';
                $array_one['Branch'] = $datacheck['code'];
                $array_one['NIC No'] = '';
                $array_one['CNIC No'] = "'".preg_replace('/\D+/', '', $datacheck['cnic'])."'";
                $array_one['First Name'] = preg_replace('/[^A-Za-z0-9\-]/', '', trim($first_name));
                $array_one['Middle Name'] = preg_replace('/[^A-Za-z0-9\-]/', '', trim($middle_name));
                $array_one['Last Name'] = preg_replace('/[^A-Za-z0-9\-]/', '', trim($last_name));
                $array_one['Applicant Birth Name'] = '';
                $array_one['Date of Birth'] = $datacheck['dob'];
                if (in_array($datacheck['dob'], $dob_array)) {
                    // $array_one['Date_of_Birth'] = '0';
                    $array_one['Date of Birth'] = date('d/m/Y', (strtotime($datacheck['dob'])));
                } else {
                    if (date('Y-m-d', (strtotime($datacheck['dob']))) > '2002-01-01') {
                        $array_one['Date of Birth'] = '0';
                    } else {
                        $array_one['Date of Birth'] = date('d/m/Y', (strtotime($datacheck['dob'])));
                    }
                }
                $array_one['Gender'] = $datacheck['gender'];
                $array_one['Spouse First Name'] = '';
                $array_one['Spouse Middle Name'] = '';
                $array_one['Spouse Last Name'] = '';
                $array_one['Spouse Birth Name'] = '';
                $array_one['Father/Husband First Name'] = $f_first_name;
                $array_one['Father/Husband Middle Name'] = $f_middle_name;
                $array_one['Father/Husband Last Name'] = $f_last_name;
                $array_one['Father/Husband Birth Name'] = '';
                $array_one['Mother First name'] = '';
                $array_one['Mother Middle Name'] = '';
                $array_one['Mother Last Name'] = '';
                $array_one['Mother Birth Name'] = '';
                $array_one['Address'] = preg_replace('/[^0-9a-zA-Z_\s]/', '', trim(str_replace(",", "", $datacheck['address1'])));
                //$array_one['Address'] = $datacheck['address1'];
                $array_one['Village Name'] = '';
                $array_one['Chak'] = '';


                if ($datacheck['uc'] == 'NA' || $datacheck['uc'] == 'NA ' || $datacheck['uc'] == 'N/A' || $datacheck['uc'] == 'other' || $datacheck['uc'] == 'Other') {
                    $array_one['UC'] = '';

                } else {
                    $array_one['UC'] = str_replace(",", "", $datacheck['uc']);
                }
                $array_one['Tehsil/Town'] = '';
                $array_one['City/District'] = str_replace(",", "", $datacheck['city_name']);
                $array_one['ELEC Consumer/ Ref No'] = '';
                $array_one['Suigass Customer No'] = '';
                $array_one['Phone No'] = preg_replace('/\D+/', '', $datacheck['landline']);
                $array_one['Cell No'] = preg_replace('/\D+/', '', $datacheck['mobile']);
                $array_one['Doing Business as'] = '';
                $array_one['Ownership Status'] = '';
                $array_one['Business Category'] = '';
                $array_one['Business Address'] = '';
                $array_one['Village1'] = '';
                $array_one['Chak1'] = '';
                $array_one['UC1'] = '';
                $array_one['Tehsil/Town1'] = '';
                $array_one['City/Distt'] = '';
                $array_one['Phone No1'] = '';
                $array_one['Cell No1'] = '';
                $array_one['Account No/Sanction No'] = $datacheck['sanction_no'];
                $array_one['Account Type'] = 'IN';
                $array_one['Loan Dsb Date/Acct Opening Date'] = date('t/m/Y', (strtotime($datacheck['datedisburse'])));
                $array_one['Loan End date / Maturity'] = date('t/m/Y', (strtotime($datacheck['loanexpiry'])));
                $array_one['Amount Distributed'] = $datacheck['loan_amount'];
                $array_one['Term'] = 'INT';
                $array_one['Electronic ELEC Alert'] = 'Y';
                $array_one['Association Type'] = $datacheck['grp_type'] == 'GRP' ? 'GRP' : 'PRN';
                $array_one['GroupID'] = $datacheck['grp_type'] == 'GRP' ? $datacheck['grp_no'] : '';

                $array_one['Account Status'] = $datacheck['dsb_status'] == 'collected' ? 'Open' : 'Close';
                $array_one['Balance'] = $datacheck['loan_amount']-$datacheck['recv'];;
                $array_one['overdue'] = $datacheck['overdue'];
                $new_datacheck[] = $array_one;
            }
            return $new_datacheck;
        }
    }

    public static function actionDataCheckUpdate($data,$disb_date,$report_type)
    {
        if ($report_type == 5) {
            foreach ($data as $key => $datacheck) {
                $array_one = array();
                $array_one['Branch'] = $datacheck['code'];
                $array_one['File_No'] = '';
                $array_one['CNIC_No'] =  preg_replace('/\D+/', '', $datacheck['cnic']);
                $array_one['Account_No'] = $datacheck['sanction_no'];
                $array_one['New_Account_No'] = $datacheck['sanction_no'];
                $array_one['Account_Type'] = 'IN';
                $array_one['Account_Status'] = ($datacheck['balance'] != 0) ? 'Open' : 'Close';
                $array_one['Status_Date'] = date('Y-m-t', strtotime($disb_date));
                $array_one['New_Amount_Distributed'] = $datacheck['loan_amount'];
                $array_one['New_Maturity_Date'] = date('t/m/Y', (strtotime($datacheck['loanexpiry'])));
                $array_one['New_Terms'] = 'INT';
                $array_one['Last_Payment'] = $datacheck['recv'];
                $array_one['Balance'] = ($datacheck['recv_total'] == 0) ? $datacheck['loan_amount'] : $datacheck['balance'];
                $array_one['Over_Due_Balance'] = ($datacheck['overdue'] > 0) ? $datacheck['overdue'] : '0';
                if ($datacheck['overdue'] <= 0) {
                    $array_one['Payment_Status'] = 'OK';
                } else {
                    $array_one['Payment_Status'] = 'X';
                }
                $array_one['New_Association_Type'] = $datacheck['grp_type'] == 'GRP' ? 'GRP' : 'PRN';
                $array_one['New_Group_Id'] = $datacheck['grp_type'] == 'GRP' ? $datacheck['grp_no'] : '';
                $new_datacheck[] = $array_one;
            }
            return $new_datacheck;
        }
        else{
            foreach ($data as $key => $datacheck) {
                $array_one = array();
                $array_one['Branch'] = $datacheck['code'];
                $array_one['File No'] = '';
                $array_one['CNIC No'] = "'" . preg_replace('/\D+/', '', $datacheck['cnic']) . "'";
                $array_one['Account No/Sanction No'] = $datacheck['sanction_no'];
                $array_one['New Account No'] = $datacheck['sanction_no'];
                $array_one['Account Status'] = ($datacheck['balance'] != 0) ? 'Open' : 'Close';
                $array_one['Account Type'] = 'IN';
                $array_one['Status Date'] = date('Y-m-t', strtotime($disb_date));
                $array_one['Amount Distributed'] = $datacheck['loan_amount'];
                $array_one['Maturity Date'] = date('t/m/Y', (strtotime($datacheck['loanexpiry'])));
                $array_one['Terms'] = 'INT';
                $array_one['Last Payment'] = $datacheck['recv'];
                $array_one['Outstanding Balance'] = ($datacheck['recv_total'] == 0) ? $datacheck['loan_amount'] : $datacheck['balance'];
                $array_one['Overdue Balance'] = ($datacheck['overdue'] > 0) ? $datacheck['overdue'] : '0';
                if ($datacheck['overdue'] <= 0) {
                    $array_one['Payment Status'] = 'OK';
                } else {
                    $array_one['Payment Status'] = 'X';
                }
                $array_one['Association Type'] = $datacheck['grp_type'] == 'GRP' ? 'GRP' : 'PRN';
                $array_one['GroupID'] = $datacheck['grp_type'] == 'GRP' ? $datacheck['grp_no'] : '';
                $new_datacheck[] = $array_one;
            }
            return $new_datacheck;
        }
    }
}