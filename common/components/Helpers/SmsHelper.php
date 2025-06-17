<?php

/**
 * Created by PhpStorm.
 * User: Junaid Fayyaz
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


use common\models\SmsLogs;

class SmsHelper
{
    public static function Sendsms($number,$message){
        // Configuration variables
        $type = "json";
        $id = "cd1129akhuwat";
        $pass = "Akhuwat@1129#";
        $lang = "English";
        $mask = "Akhuwat";
        // Data for text message
        $message = urlencode($message);
        // Prepare data for POST request
        $data ="id=".$id."&pass=".$pass."&msg=".$message."&to=".$number."&lang=".$lang."&mask=".$mask."&type=".$type;
        // Send the POST request with cURL
        $ch = curl_init('http://opencodes.pk/api/medver.php/sendsms/url');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $result = curl_exec($ch); //This is the result from Outreach
        curl_close($ch);
        return json_decode($result);
    }

    public static function SendUrdusms($number,$message){

        // Configuration variables
        $type = "json";
        $id = "cd1129akhuwat";
        $pass = "Akhuwat@1129#";
        $lang = "URDU";
        $mask = "Akhuwat";
        // Data for text message
        $message = urlencode($message);
        // Prepare data for POST request
        $data ="id=".$id."&pass=".$pass."&msg=".$message."&to=".$number."&lang=".$lang."&mask=".$mask."&type=".$type;
        // Send the POST request with cURL
        $ch = curl_init('http://opencodes.pk/api/medver.php/sendsms/url');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch); //This is the result from Outreach
        curl_close($ch);
        return json_decode($result);
    }

    public static function getSmsCode(){
        return rand(11111,99999);
    }

    public static function getRecoveryText_($recovery){
        $msg = "محترم ممبر:";
        $msg .= "مندرجہ ذیل رقم اخوت کو موصول ہو گئی ہے:";
        $msg .= isset($recovery->amount) ? " روپے ".number_format($recovery->amount)." ریکوری: " : '';
        $msg .= isset($recovery->receipt_no) ? $recovery->receipt_no." رسید نمبر: " : '';
        $msg .= isset($recovery->receive_date) ? date('d-M-Y',$recovery->receive_date)."تاریخ: " : '';
        return $msg;
    }

    public static function getRecoveryText1($recovery){
        $month = ['01' => 'جنوری', '02' => 'فروری','03' => 'مارچ','04'=> 'اپریل', '05' => 'مئی', '06' => 'جون', '07' => 'جولائی', '08' => 'اگست',
            '09' => 'ستمبر', '10' => 'اکتوبر', '11' => 'نومبر','12' => 'دسمبر'];
        $msg = "محترم ";
        $msg .= isset($recovery->application->member->full_name) ? $recovery->application->member->full_name : '';
        $msg = "آپکی قسط کی رقم مبلغ ";
        $msg .= isset($recovery->amount) ? number_format($recovery->amount)." روپے " : '';
        //$msg .= "بمعہ عطیہ ";
        $msg .= isset($recovery->mdp) ?  "بمعہ عطیہ ".number_format($recovery->mdp)." روپے " : '';
        $msg .= isset($recovery->receive_date) ? date('d',$recovery->receive_date) : '';
        $msg .= isset($recovery->receive_date) ? $month[date('m',$recovery->receive_date)] : '';
        $msg .= "،";
        $msg .= isset($recovery->receive_date) ? date('Y',$recovery->receive_date) : '';
        $msg .= " کو موصول ہو گئے ہیں۔ شکریہ ";
        $msg .= isset($recovery->application->member->cnic) ? 'CNIC No.' .$recovery->application->member->cnic : '';
        $msg .= isset($recovery->receipt_no) ? 'Transaction No.' .$recovery->receipt_no : '';

        return $msg;
    }

    public static function getRecoveryTextOld($recovery){
        $month = ['01' => 'جنوری', '02' => 'فروری','03' => 'مارچ','04'=> 'اپریل', '05' => 'مئی', '06' => 'جون', '07' => 'جولائی', '08' => 'اگست',
            '09' => 'ستمبر', '10' => 'اکتوبر', '11' => 'نومبر','12' => 'دسمبر'];
        $msg = "Muhtram ";
        $msg .= isset($recovery->application->member->full_name) ? $recovery->application->member->full_name : '';
        $msg = " Aap ki qist ki raqam mubaligh";
        $msg .= isset($recovery->amount) ? " Rs. " .number_format($recovery->amount) : '';
        $msg .= isset($recovery->mdp) ?  " bama atiya Rs. ".number_format($recovery->mdp): '';
        $msg .= isset($recovery->receive_date) ? " " .date('d M Y',$recovery->receive_date) : '';
        $msg .= " ko mosool ho gay hain. Shukriya ";
        $msg .= isset($recovery->application->member->cnic) ? 'CNIC No. ' .$recovery->application->member->cnic : '';
        $msg .= isset($recovery->receipt_no) ? ' Transaction No. ' .$recovery->receipt_no : '';

        return $msg;
    }
    public static function getRecoveryTextEnglish($recovery){
        $month = ['01' => 'جنوری', '02' => 'فروری','03' => 'مارچ','04'=> 'اپریل', '05' => 'مئی', '06' => 'جون', '07' => 'جولائی', '08' => 'اگست',
            '09' => 'ستمبر', '10' => 'اکتوبر', '11' => 'نومبر','12' => 'دسمبر'];
        $msg = "Muaziz Wazir e Azam Housing Scheme Customer, ";
        $msg .= "CNIC ";
        $msg.=isset($recovery->application->member->cnic) ? $recovery->application->member->cnic : '';
        $msg.=" apki amount ";
        $msg.=isset($recovery->amount) ? " Rs. " .number_format($recovery->amount+$recovery->charges_amount+$recovery->credit_tax) : '';
        $msg.='/- ';
        $msg.=isset($recovery->receive_date) ? " " .date('d M Y',$recovery->receive_date) : '';
        $msg.=" ko wasool hogai hai. ";
        $msg.=" Shukriya.";
        return $msg;
    }

    public static function getRecoveryTextCopy($recovery){
        $month = ['01' => 'جنوری', '02' => 'فروری','03' => 'مارچ','04'=> 'اپریل', '05' => 'مئی', '06' => 'جون', '07' => 'جولائی', '08' => 'اگست',
            '09' => 'ستمبر', '10' => 'اکتوبر', '11' => 'نومبر','12' => 'دسمبر'];
        $msg = " ";
       // $msg. = "Trx. ID ";
       // $msg .= isset($recovery->receipt_no) ? $recovery->receipt_no: '';
        $msg.= "محترم جناب  ";
        $msg.=isset($recovery->application->member->full_name) ? $recovery->application->member->full_name : '';
        if($recovery->project_id == 61) {
            $msg.= "، اخوت عبادت لو کاسٹ ";
        }elseif ($recovery->project_id == 62){
            $msg.= "، اخوت نعمت لوکاسٹ ";
        }elseif ($recovery->project_id == 52){
            $msg.= "، وزیراعظم ";
        }elseif ($recovery->project_id == 67){
            $msg.= "، اخوت ایمپلائز لوکاسٹ ";
        }elseif ($recovery->project_id == 76){
            $msg.= "، پھاٹا لو کاسٹ ";
        }elseif ($recovery->project_id == 77){
            $msg.= "، کامیاب پاکستان لو کاسٹ ";
        }elseif ($recovery->project_id == 64){
            $msg.= "، نعمت ایمپلائز لوکاسٹ ";
        }

        $msg.="ہاؤسنگ سکیم کے عوض آپکے ماہانہ کرایہ";

        $msg.=" کی رقم ";
        $msg.="RS. ";
        $msg.= isset($recovery->amount) ? number_format($recovery->amount+$recovery->charges_amount+$recovery->credit_tax) : ' ';
        $msg.= ' مورخہ ';
        $msg.= isset($recovery->receive_date) ? date('d',$recovery->receive_date) : ' ';
        $msg.= isset($recovery->receive_date) ? $month[date('m',$recovery->receive_date)] : ' ';
        $msg.= "،";
        $msg.= isset($recovery->receive_date) ? date('Y',$recovery->receive_date) : ' ';
        $msg.=" کو اخوت اسلامک مائیکرو فائنانس کو موصول ہوگئی ہے۔ کسی بھی قسم کی شکایت کی صورت میں درج ذیل نمبر پر رابطہ کریں۔ ";
        $msg.="464-448-111-042";
        $msg.="  شکریہ ۔     ";
        $msg.= "Trx. ID ";
        $msg.= isset($recovery->receipt_no) ? $recovery->receipt_no: '';
        return $msg;
    }

    public static function getRecoveryText($recovery){
        $month = ['01' => 'January', '02' => 'February','03' => 'March','04'=> 'April', '05' => 'May', '06' => 'Jun', '07' => 'July', '08' => 'August',
            '09' => 'September', '10' => 'October', '11' => 'November','12' => 'December'];
        $msg = " ";

        if($recovery->project_id == 61) {
            $msg.= "Akhuwat Low Cost";
        }elseif ($recovery->project_id == 62){
            $msg.= "Akhuwat Naimat Low Cost";
        }elseif ($recovery->project_id == 52){
            $msg.= "Prime Minister";
        }elseif ($recovery->project_id == 67){
            $msg.= "Akhuwat Employee Low Cost";
        }elseif ($recovery->project_id == 76){
            $msg.= "Phata Low Cost";
        }elseif ($recovery->project_id == 77){
            $msg.= "Kamyaab Pakistan Low Cost";
        }elseif ($recovery->project_id == 64){
            $msg.= "Naimat Employee Low Cost";
        }

        $msg.=" Housing ";
        $msg.=isset($recovery->application->member->cnic) ? $recovery->application->member->cnic : '';
        $msg.= ' apki amount ';
        $msg.="RS. ";
        $msg.= isset($recovery->amount) ? number_format($recovery->amount+$recovery->charges_amount+$recovery->credit_tax) : ' ';
        $msg.= '/- ';
        $msg.= isset($recovery->receive_date) ? date('d',$recovery->receive_date) : ' ';
        $msg.= "-";
        $msg.= isset($recovery->receive_date) ? $month[date('m',$recovery->receive_date)] : ' ';
        $msg.= "-";
        $msg.= isset($recovery->receive_date) ? date('Y',$recovery->receive_date) : ' ';

        $msg.=" ko wasool hogai ha. ";
        $msg.= "Trx. ID ";
        $msg.= isset($recovery->receipt_no) ? $recovery->receipt_no: '';
        $msg.=" Complain #";
        $msg.="042-111-448-464";
        return $msg;
    }

    public static function getApplicationText1($application){
        $month = ['01' => 'جنوری', '02' => 'فروری','03' => 'مارچ','04'=> 'اپریل', '05' => 'مئی', '06' => 'جون', '07' => 'جولائی', '08' => 'اگست',
            '09' => 'ستمبر', '10' => 'اکتوبر', '11' => 'نومبر','12' => 'دسمبر'];
        $msg = "محترم ";
        $msg .= isset($application->member->full_name) ? $application->member->full_name : '';
        $msg .= "آپکی درخواست نمبر ";
        $msg .= isset($application->application_no) ? $application->application_no : '';
        $msg .= "بمعہ";
        //$msg .= isset($application->fee) ? number_format($application->fee)." روپے " : '';
        $msg .= "200" ." روپے ";
        $msg .= isset($application->created_at) ? date('d',$application->created_at) : '';
        $msg .= isset($application->created_at) ? $month[date('m',$application->created_at)] : '';
        $msg .= "،";
        $msg .= isset($application->created_at) ? date('Y',$application->created_at) : '';
        $msg .= " کو موصول ہوگئی ہے۔  شکریہ ";

        return $msg;
    }

    public static function getApplicationText($application){
        $msg = "Muhtram ";
        $msg .= isset($application->member->full_name) ? $application->member->full_name : '';
        $msg .= " Aap ki application no ";
        $msg .= isset($application->application_no) ? $application->application_no : '';
        $msg .= ", ";
        //$msg .= isset($application->fee) ? number_format($application->fee)." روپے " : '';
        $msg .= "Rs.200 ";
        if(isset($application->cib) && !empty($application->cib)){
            $msg.=' bama CIB fee (';
            $msg.=$application->cib->receipt_no.') ';
            $msg.="Rs.".$application->cib->fee." ";
        }
        $msg .= isset($application->created_at) ? date('d M Y',$application->created_at) : '';
        $msg .= " ko mosool ho gai hai. Shukriya ";

        return $msg;
    }
    public static function getDisbursementText1($loan){
        $msg = "آپ کو قرضہ نمبر ";
        $msg .= isset($loan->sanction_no) ? $loan->sanction_no : '';
        $msg .= " ،چیک نمبر ";
        $msg .= isset($loan->cheque_no) ? $loan->cheque_no : '';
        $msg .= " کے ساتھ ";
        $msg .= isset($loan->loan_amount) ? $loan->loan_amount : '';
        $msg .= " روپے قرضہ جاری کر دیا گیا ہے۔ ";
        $msg .= "شکریہ";
        return $msg;
    }

    public static function getDisbursementText($loan){
        $msg = "Aap ko qarza no ";
        $msg .= isset($loan->sanction_no) ? $loan->sanction_no : '';
        $msg .= " ,cheque no ";
        $msg .= isset($loan->cheque_no) ? $loan->cheque_no : '';
        $msg .= " key sath Rs. ";
        $msg .= isset($loan->loan_amount) ? $loan->loan_amount : '';
        $msg .= " qarza jari kr dya gaya hai. Shukriya";
        return $msg;
    }

    public static function getTakafulText_($recovery){
        $msg = "محترم ";
        $msg .= isset($recovery->application->member->full_name) ? $recovery->application->member->full_name : '';
        $msg .= "آپکی  باہمی امدادی فنڈ کی رقم ";
        $msg .= isset($recovery->amount) ? number_format($recovery->amount)." روپے " : '';
        $msg .= "موصول ہوگئی ہے۔   شکریہ ";
        $msg .= isset($recovery->receipt_no) ? 'Transaction No.' .$recovery->receipt_no : '';

        return $msg;
    }

    public static function getTakafulText1($recovery){
        $msg = "Muhtram ";
        $msg .= isset($recovery->application->member->full_name) ? $recovery->application->member->full_name : '';
        $msg .= " aap ki bahmi imdadi fund ki raqam ";
        $msg .= isset($recovery->amount) ? number_format($recovery->amount)." روپے " : '';
        $msg .= " mosool ho gai hai. Shukriya ";
        $msg .= isset($recovery->receipt_no) ? 'Transaction No. ' .$recovery->receipt_no : '';

        return $msg;
    }

    public static function getTakafulText($operation){
        $msg = "Muhtram ";
        $msg .= isset($operation->application->member->full_name) ? $operation->application->member->full_name : '';
        $msg .= " aap ki bahmi imdadi fund ki raqam ";
        $msg .= isset($operation->credit) ? number_format($operation->credit)." Rs. " : '';
        $msg .= " mosool ho gai hai. Shukriya ";
        $msg .= isset($operation->receipt_no) ? 'Transaction No. ' .$operation->receipt_no : '';

        return $msg;
    }

    public static function getCodeText($code){
        return "Your Akhuwat paperless password is ".$code;
    }

    public static function getCodeTextMIS($code){
        return "Your Akhuwat MIS password is ".$code;
    }

    public static function getPassCodeText($code,$signature){
        return "<#> ". $code . " is your Akhuwat Reports password ". $signature;
    }

    public static function getDonationPostText($donation){
        $msg = "Thank you for your generosity; your ";
        if ($donation->payment_method_id!=1){
            $msg .= isset($donation->paymentmethods->name) ? strtolower($donation->paymentmethods->name) : ' ';
            $msg .="(";
            $msg .= isset($donation->cheque_draft_no) ? $donation->cheque_draft_no : '';
            $msg .=")";
        }
        else {
            $msg .= isset($donation->paymentmethods->name) ? strtolower($donation->paymentmethods->name) : ' ';
        }
        $msg .= " of Rs ";
        $msg .= isset($donation->credit) ? number_format($donation->credit) : '';
        $msg .= " as ";
        $msg .= isset($donation->donationType->donation_type) ? $donation->donationType->donation_type : ' ';
        $msg .= " for the ";
        $msg .= isset($donation->purpose->purpose) ? $donation->purpose->purpose : '';
        $msg .= " programme has been received by Akhuwat against Transaction ID. ";
        $msg .= isset($donation->receipt_no) ? $donation->receipt_no : '';
        $msg .= ". UAN:042-111-448-464";

        return $msg;
    }

    public static function SmsLogs($sms_type,$type){
        $model = new SmsLogs();
        $model->sms_type = $sms_type;
        if($model->sms_type == 'register'){
            $model->user_id = $model->type_id = $type->id;
            $model->number = $type->mobile;
        }else if($model->sms_type == 'donation'){
            $model->user_id = $type->created_by;
            $model->type_id = $type->id;
            $model->number = $type->donor->mobile;
        }else if($model->sms_type == 'application'){
            $model->user_id = $type->created_by;
            $model->type_id = $type->id;
            $model->number = isset($type->member->membersMobile->phone) ? $type->member->membersMobile->phone : '';
        }else if($model->sms_type == 'operation'){
            $model->user_id = $type->created_by;
            $model->type_id = $type->id;
            $model->number = isset($type->application->member->membersMobile->phone) ? $type->application->member->membersMobile->phone : '';
        }else if($model->sms_type == 'takaful'){
            $model->user_id = $type->created_by;
            $model->type_id = $type->id;
            $model->number = isset($type->application->member->membersMobile->phone) ? $type->application->member->membersMobile->phone : '';
        }else if($model->sms_type == 'recovery'){
            $model->user_id = $type->created_by;
            $model->type_id = $type->id;
            $model->number = isset($type->application->member->membersMobile->phone) ? $type->application->member->membersMobile->phone : '';
        }

        if($model->save()){
            return true;
        }else{
            return false;
        }
    }

}