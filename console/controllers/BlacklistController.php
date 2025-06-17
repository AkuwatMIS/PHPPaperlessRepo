<?php

namespace console\controllers;

use common\components\Helpers\BlacklistHelper;
use common\components\Helpers\ImageHelper;
use common\models\Applications;
use common\models\Blacklist;
use common\models\BlacklistFiles;
use common\models\Loans;
use common\models\Members;
use common\models\Provinces;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\console\Controller;

class BlacklistController extends Controller
{

    public $cnic;

//   * * * * * php /var/www/paperless_web/yii blacklist/post
    public function actionPost()
    {
        ini_set('memory_limit', '200048M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $blacklist_file_records = BlacklistFiles::find()->where(['status' => '0'])->all();

        $flag = false;
        $cnic_array=[];
        foreach ($blacklist_file_records as $blacklist_file_record) {
            $errors = [];
            $cnic_array = [];
            $file_name = $blacklist_file_record['file_name'];
            $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/blacklist/' . $file_name;
            $ext = pathinfo($file_path, PATHINFO_EXTENSION);


            if (file_exists($file_path) && strtolower($ext)=="json") {
                $json = file_get_contents($file_path);
                $data=json_decode($json);
                $i = 1;

                if (json_last_error() === JSON_ERROR_NONE) {
                    foreach ($data as $key => $row) {
                        $row=(array)$row;
                        $cnic_invalid="";
                        $cnic=preg_replace("/[^\d]/", "", $row['CNIC']);

                        if(strlen($cnic)==15){
                            $cnic=$cnic;
                        }
                        elseif(strlen($cnic)==13){
                            $cnic= vsprintf('%s%s%s%s%s-%s%s%s%s%s%s%s-%s', str_split($cnic));
                        }else{
                            $cnic_invalid=$cnic;
                        }

                        $row['CNIC']=$cnic;
                        $existing_record=null;
                        $existing_record = Blacklist::find()->where(['cnic' => $row['CNIC']])->andWhere(['deleted'=>0])->one();

                        if(!empty($existing_record) && $existing_record!=null){

                            if(!empty($row['Name'])){
                                $existing_record->name=$row['Name'];
                            }
                            if(!empty($row['FatherName'])){
                                $existing_record->parentage=$row['FatherName'];
                            }
                            if(!empty($row['Province'])){
                                $existing_record->province=$row['Province'];
                            }
                            if(!empty($row['District'])){
                                $existing_record->location=$row['District'];
                            }                            
                            if(!empty($row['description'])){
                                $existing_record->description=$row['description'];
                            }

                            $existing_record->created_by = $blacklist_file_record->created_by;
                            $existing_record->type='hard';
                            $existing_record->save(false);

                            if($row['CNIC']!=null && !empty($row['CNIC']) && $row['CNIC']!=""){
                               $cnic_array[]=$row['CNIC'];  
                            }


                        }else{

                            $model = new Blacklist();
                            $model->member_id = NULL;
                            $model->cnic_invalid = $cnic_invalid;
                            $model->name=$row['Name'];
                            $model->parentage=$row['FatherName'];
                            $model->reason = 'NA';
                            $model->reject_reason = 'NA';
                            $model->cnic=$row['CNIC'];
                            $model->province=$row['Province'];
                            $model->location=$row['District'];
                            $model->description=$row['description'];
                            $model->type='hard';
                            $model->created_by = $blacklist_file_record->created_by;

                            if ( !$model->validate() || !$model->save(false) ) {
                                $errors[$i] = $model->getErrors();
                            }
                            $i++;
                            if($row['CNIC']!=null && !empty($row['CNIC']) && $row['CNIC']!=""){
                               $cnic_array[]=$row['CNIC'];  
                            }
                        }
                    }   

                }

            }

            if (($handle = fopen($file_path, "r")) !== FALSE && strtolower($ext)!="json") {
                $header = fgetcsv($handle);
                $i = 2;
                $existing_record=null;
                while (($row = fgetcsv($handle)) !== FALSE) {
                    //print_r($row);die();
                    if(strlen($row[2])==15){
                        $row[2]=$row[2];
                    }
                    elseif(strlen($row[2])==13){
                        $row[2]= vsprintf('%s%s%s%s%s-%s%s%s%s%s%s%s-%s', str_split($row[2]));
                    }else{
                        $row[2]=$row[2];
                    }

                    $existing_record = Blacklist::find()->where(['cnic' => $row[2]])->andWhere(['deleted'=>0])->one();
                    
                    if($existing_record==null){

                            $model = new Blacklist();
                            $model->member_id = NULL;
                            $model->cnic_invalid = $row[3];
                            $model->name=$row[0];
                            $model->parentage=$row[1];
                            $model->reason = $row[4];
                            $model->reject_reason = $row[6];
                            $model->cnic=$row[2];
                            $model->province=$row[5];
                            $model->location=$row[8];
                            $model->description=$row[7];
                            $model->type=$row[9];
                            $model->created_by = $blacklist_file_record->created_by;

                            if ( !$model->validate() || !$model->save(false) ) {
                                $errors[$i] = $model->getErrors();
                            }

                            $i++;
                          $cnic_array[]=$row[2];   
                    }else{
                            $existing_record->cnic_invalid = $row[3];
                            $existing_record->name=$row[0];
                            $existing_record->parentage=$row[1];
                            $existing_record->reason = $row[4];
                            $existing_record->reject_reason = $row[6];
                            $existing_record->province=$row[5];
                            $existing_record->location=$row[8];
                            $existing_record->description=$row[7];
                            $existing_record->type=$row[9];

                            $existing_record->save(false);

                            $cnic_array[]=$row[2];
                    }


                }   

            }


            $loans = Applications::find()->select(['applications.id','full_name','parentage','cnic','applications.application_no','applications.application_date', 'cnic as matched_cnic'])
                ->join('inner join','members','members.id=applications.member_id')->where(['in','cnic',$cnic_array])->asArray()->all();

            $loans_family_member_cnic = Applications::find()->select(['applications.id','full_name','parentage','cnic','applications.application_no','applications.application_date', 'family_member_cnic as matched_cnic'])
                ->join('inner join','members','members.id=applications.member_id')->where(['in','family_member_cnic',$cnic_array])->asArray()->all();

            $other_cnic = Applications::find()->select(['applications.id','full_name','parentage','cnic','applications.application_no','applications.application_date', 'applications.other_cnic as matched_cnic'])
                ->join('inner join','members','members.id=applications.member_id')->where(['in','applications.other_cnic',$cnic_array])->asArray()->all();

            $guarantor_cnic = Applications::find()->select(['applications.id','members.full_name','members.parentage','members.cnic','applications.application_no','applications.application_date', 'guarantors.cnic as matched_cnic'])
                ->join('inner join','members','members.id=applications.member_id')
                ->join('inner join','guarantors','guarantors.group_id=applications.group_id')
                ->where(['in','guarantors.cnic',$cnic_array])->asArray()->all();


            $file_name = 'blacklist_data_'.$blacklist_file_record->id. '_'. date('d-m-Y-H-i-s') . '.csv';
            $file_path = Yii::getAlias('@anyname') . '/frontend/web' . '/blacklist/mis_blacklist/'. $file_name;
            $header_data = 'matched,matched_cnic,full_name,parentage,cnic,application_no,application_date,sanction_no,status,date_disbursed';
            
            $header_list = explode(',',$header_data);
            $createColumn=[];
            foreach ($header_list as $header) {
                $createColumn[] = ucwords(str_replace('_', ' ', $header));
            }
            $fopen = fopen($file_path,'w');
            fputcsv($fopen,$createColumn);

            if(!empty($loans))
            {


                foreach ($loans as $d)
                {
                    $da = [];

                    $da['matched']='Member CNIC';
                    $da['matched_cnic']=$d['matched_cnic'];
                    $da['full_name']=$d['full_name'];
                    $da['parentage']=$d['parentage'];
                    $da['cnic']=$d['cnic'];
                    $da['application_no']=$d['application_no'];
                    $da['application_date']=date('Y-m-d',$d['application_date']);

                    $loan_mod=Loans::find()->where(['application_id'=>$d['id']])->one();
                    if(!empty($loan_mod)){
                        $da['sanction_no']=$loan_mod->sanction_no;
                        $da['status']=$loan_mod->status;
                        $da['date_disbursed']=date('Y-m-d',$loan_mod->date_disbursed);
                    }else{
                        $da['sanction_no']=null;
                        $da['status']=null;
                        $da['date_disbursed']=null;
                    }
                    fputcsv($fopen,$da);
                }

            }


             if(!empty($loans_family_member_cnic))
            {

                foreach ($loans_family_member_cnic as $d)
                {
                    $da = [];
                    $da['matched']='Family Member CNIC';
                    $da['matched_cnic']=$d['matched_cnic'];
                    $da['full_name']=$d['full_name'];
                    $da['parentage']=$d['parentage'];
                    $da['cnic']=$d['cnic'];
                    $da['application_no']=$d['application_no'];
                    $da['application_date']=date('Y-m-d',$d['application_date']);

                    $loan_mod=Loans::find()->where(['application_id'=>$d['id']])->one();
                    if(!empty($loan_mod)){
                        $da['sanction_no']=$loan_mod->sanction_no;
                        $da['status']=$loan_mod->status;
                        $da['date_disbursed']=date('Y-m-d',$loan_mod->date_disbursed);
                    }else{
                        $da['sanction_no']=null;
                        $da['status']=null;
                        $da['date_disbursed']=null;
                    }

                    fputcsv($fopen,$da);
                }

            }
             if(!empty($other_cnic))
            {

                foreach ($other_cnic as $d)
                {
                    $da = [];
                    $da['matched']='Other CNIC';
                    $da['matched_cnic']=$d['matched_cnic'];
                    $da['full_name']=$d['full_name'];
                    $da['parentage']=$d['parentage'];
                    $da['cnic']=$d['cnic'];
                    $da['application_no']=$d['application_no'];
                    $da['application_date']=date('Y-m-d',$d['application_date']);

                    $loan_mod=Loans::find()->where(['application_id'=>$d['id']])->one();
                    if(!empty($loan_mod)){
                        $da['sanction_no']=$loan_mod->sanction_no;
                        $da['status']=$loan_mod->status;
                        $da['date_disbursed']=date('Y-m-d',$loan_mod->date_disbursed);
                    }else{
                        $da['sanction_no']=null;
                        $da['status']=null;
                        $da['date_disbursed']=null;
                    }

                    fputcsv($fopen,$da);
                }

            }


             if(!empty($guarantor_cnic))
            {

                foreach ($guarantor_cnic as $d)
                {
                    $da = [];
                    $da['matched']='Guarantor CNIC';
                    $da['matched_cnic']=$d['matched_cnic'];
                    $da['full_name']=$d['full_name'];
                    $da['parentage']=$d['parentage'];
                    $da['cnic']=$d['cnic'];
                    $da['application_no']=$d['application_no'];
                    $da['application_date']=date('Y-m-d',$d['application_date']);

                    $loan_mod=Loans::find()->where(['application_id'=>$d['id']])->one();
                    if(!empty($loan_mod)){
                        $da['sanction_no']=$loan_mod->sanction_no;
                        $da['status']=$loan_mod->status;
                        $da['date_disbursed']=date('Y-m-d',$loan_mod->date_disbursed);
                    }else{
                        $da['sanction_no']=null;
                        $da['status']=null;
                        $da['date_disbursed']=null;
                    }
                    fputcsv($fopen,$da);
                }

            }


            fclose($fopen);



            $blacklist_file_record->result_file_name = $file_name;

            $blacklist_file_record->file = 'abc.csv';
            $blacklist_file_record->status = 1;
            if(!empty($errors))
            {
                $blacklist_file_record->blacklist_errors = json_encode($errors);
            }

            if(!$blacklist_file_record->save())
            {
                print_r($blacklist_file_record->getErrors());
                die();
            }

        }

    }

    public function actionWriteOffLoans()
    {
        $loans=[];
        foreach ($loans as $loan){
            $loan_model=Loans::find()->where(['id'=>$loan])->one();

            if (!empty($loan_model)){

                $black_list=Blacklist::find()->where(['cnic'=>$loan_model->application->member->cnic])->one();
                if(empty($black_list)){
                    $b_model=new Blacklist();
                    $b_model->cnic=$loan_model->application->member->cnic;
                    $b_model->name=$loan_model->application->member->full_name;
                    $b_model->type='soft';
                    $b_model->reason='write-off';
                    $b_model->created_by=1;
                    $b_model->save();
                }
            }
        }

    }

    // nohup php yii blacklist/nic-blacklist   done
    public function actionNicBlacklist(){
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $nicResultArray = [];
        $familyResultArray = [];
        $otherResultArray = [];

        $filename = 'nic_blacklist.csv';
        $filepathW = ImageHelper::getAttachmentPath() . 'blacklist/' . $filename;
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        $createColumn = array("CNIC", "Member_Id", "Application_Id", "Type");
        fputcsv($fopenW, $createColumn);

        $blackListData = Blacklist::find()->orderBy([
            'id' => SORT_DESC
        ])->all();

        foreach ($blackListData as $m=>$blackList){
            $nic  = (isset($blackList->cnic)&&!empty($blackList->cnic))?$blackList->cnic:'';
            if($nic!=''){
                $member_nic = Applications::find()
                    ->join('inner join','members','members.id=applications.member_id')
                    ->where(['members.cnic'=>$nic])
                    ->select(['applications.id','applications.member_id'])
                    ->one();
                if(!empty($member_nic) && $member_nic!=null){
                    $nicResultArray[$m]['cnic'] = $nic;
                    $nicResultArray[$m]['member_id'] = $member_nic['member_id'];
                    $nicResultArray[$m]['app_id'] = $member_nic['id'];
                    $nicResultArray[$m]['member_data'] = 'member cnic';

                    print_r($nicResultArray);
                    echo '------member cnic------';
                }else{
                    echo 'Not Exists';
                }
            }

        }

        foreach ($blackListData as $f=>$blackList){
            $nic  = (isset($blackList->cnic)&&!empty($blackList->cnic))?$blackList->cnic:'';
            if($nic!=''){
                $family_member_nic = Applications::find()
                    ->join('inner join','members','members.id=applications.member_id')
                    ->where(['members.family_member_cnic'=>$nic])
                    ->select(['applications.id','applications.member_id'])
                    ->one();
                if(!empty($family_member_nic) && $family_member_nic!=null){
                    $familyResultArray[$f]['cnic'] = $nic;
                    $familyResultArray[$f]['member_id'] = $family_member_nic['member_id'];
                    $familyResultArray[$f]['app_id'] = $family_member_nic['id'];
                    $familyResultArray[$f]['family_data'] = 'Family cnic';

                    print_r($familyResultArray);
                    echo '------Family cnic------';
                }else{
                    echo 'Not Exists';
                }
            }

        }

        foreach ($blackListData as $o=>$blackList){
            $nic  = (isset($blackList->cnic)&&!empty($blackList->cnic))?$blackList->cnic:'';
            if($nic!=''){
                $other_nic = Applications::find()
                    ->join('inner join','members','members.id=applications.member_id')
                    ->where(['applications.other_cnic'=>$nic])
                    ->select(['applications.id','applications.member_id'])
                    ->one();
                if(!empty($other_nic) && $other_nic!=null){
                    $otherResultArray[$o]['cnic'] = $nic;
                    $otherResultArray[$o]['member_id'] = $other_nic['member_id'];
                    $otherResultArray[$o]['app_id'] = $other_nic['id'];
                    $otherResultArray[$o]['other_data'] = 'Other cnic';

                    print_r($otherResultArray);
                    echo '------Other cnic------';
                }else{
                    echo 'Not Exists';
                }
            }

        }
        $resultArray = array_merge($nicResultArray,$familyResultArray,$otherResultArray);
        if(!empty($resultArray) && $resultArray!=null){
            foreach ($resultArray as $d){
                fputcsv($fopenW, $d);
                print_r($d);echo '<--->';
            }
        }
    }

    // nohup php yii blacklist/member-blacklist   done
    public function actionMemberBlacklist(){
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));

        $filename = 'member_details_blacklist.csv';
        $filepathW = ImageHelper::getAttachmentPath() . 'blacklist/' . $filename;
        $fopenW = fopen($filepathW, 'w');
        header('Content-type: application/csv');
        header('Content-Disposition: attachment; filename=' . $filename);
        $createColumn = array("CNIC", "Member_Id", "Application_Id", "Name", 'Parentage', 'Province');
        fputcsv($fopenW, $createColumn);

        $blackListData = Blacklist::find()->orderBy([
            'id' => SORT_DESC
        ])->all();
        $resultArray = [];

        foreach ($blackListData as $k=>$blackList){

            $name = trim($blackList->name);
            $parentage   = trim($blackList->parentage);
            $province    = trim($blackList->province);
            $provinceModel    = Provinces::find()->where(['name'=>$province])->select('id')->one();

            $member = BlacklistHelper::VerifyBlacklist($name,$parentage,'member',$provinceModel->id);
            if(!empty($member) && $member!=null){
                $resultArray[$k]['cnic'] = $member['cnic'];
                $resultArray[$k]['member_id'] = $member['member_id'];
                $resultArray[$k]['app_id'] = $member['id'];
                $resultArray[$k]['name'] = $name;
                $resultArray[$k]['parentage'] = $parentage;
                $resultArray[$k]['province'] = $province;

                print_r($resultArray);
                echo '------------';
            }else{
                echo 'Not Exists';
            }

        }

        if(!empty($resultArray) && $resultArray!=null){
            foreach ($resultArray as $d){
                fputcsv($fopenW, $d);
                print_r($d);echo '<--->';
            }
        }
    }

    public function actionFamilyMemberBlacklist(){
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $blackListData = Blacklist::find()->orderBy([
            'id' => SORT_DESC
        ])->all();


        foreach ($blackListData as $blackList){

            $name = trim($blackList->name);
            $parentage   = trim($blackList->parentage);
            $province    = trim($blackList->province);
            $province    = Provinces::find()->where(['name'=>$province])->select('id')->one();

            $family_member_nic = BlacklistHelper::VerifyBlacklist($name,$parentage,'family',$province->id);
            if(!empty($family_member_nic) && $family_member_nic!=null){

            }else{

            }
        }

    }

    public function actionOtherMemberBlacklist(){
        ini_set('memory_limit', '1024M');
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $blackListData = Blacklist::find()->orderBy([
            'id' => SORT_DESC
        ])->all();

        foreach ($blackListData as $blackList){

            $name = trim($blackList->name);
            $parentage   = trim($blackList->parentage);
            $province    = trim($blackList->province);
            $province    = Provinces::find()->where(['name'=>$province])->select('id')->one();

            $other_nic = BlacklistHelper::VerifyBlacklist($name,$parentage,'other',$province->id);
            if(!empty($other_nic) && $other_nic!=null){

            }else{

            }
        }
    }

    protected function findModel($id)
    {
        if (($model = BlacklistFiles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}