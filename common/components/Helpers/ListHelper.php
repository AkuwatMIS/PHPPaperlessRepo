<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


use common\models\Activities;
use common\models\Lists;
use common\models\Referrals;
use yii\helpers\ArrayHelper;

class ListHelper
{
    public static function getListValue($list_name,$value){
        $list = Lists::find()->where(['list_name'=>$list_name, 'value'=>$value])->one();
        return isset($list->label) ? $list->label : '';
    }
    public static function getFundRequestDetailStatus()
    {
        $fund_request = ArrayHelper::map(Lists::find()->where(['list_name'=>'fund_request__detail_status'])->all(),'value','label');
        return $fund_request;
    }


    public static function getReferralsBackend(){
        $referred_by = ArrayHelper::map(Referrals::find()->all(),'id','name');
        return $referred_by;
    }

    public static function getVerification()
    {
        $verification = ArrayHelper::map(Lists::find()->where(['list_name'=>'verification'])->all(),'value','label');
        return $verification;
    }

    public static function getPlaceOfBusiness()
    {
        $verification = ArrayHelper::map(Lists::find()->where(['list_name'=>'place_of_business'])->all(),'value','label');
        return $verification;
    }


    public static function getReferralsList(){
        $list = Referrals::find()->all();
        return $list;
    }


    public static function getLists($list_name){
        $key = 'lists_'.$list_name;
        $list = CacheHelper::getFormCache($key);

        if (empty($list)) {
            $list = Lists::find()->where(['list_name'=>$list_name])->orderBy(['sort_order' => SORT_ASC])->all();
            CacheHelper::setFormCache($key,(ArrayHelper::map($list,'value','label')));
        }

        return CacheHelper::getFormCache($key);
    }

    public static function getListsData($list_name){

        $list = ArrayHelper::map(Lists::find()->where(['list_name'=>$list_name])->all(),'value','label');
        return $list;
    }

    public static function getBankfileStatus(){
        return $bank_files_status = [
            0 => 'Pending',
            1 => 'Completed',
            2 => 'Review',
            3 => 'Approved',
        ];
    }

    public static function getProjectList(){
        return $bank_files_status = [
            132 => 'Apni Chhat Apna Ghar',
            0 => 'Other'
        ];
    }

    public static function getActivities(){
        return ArrayHelper::map(Activities::find()->where(['status'=>1])->all(),'id','name');
    }

    public static function getActivityById($id){
        return Activities::find()->where(['id'=>$id])->select(['name'])->one();
    }

    public static function getDisbursementDetailStatus(){
        return $status = [
            0 => 'Pending',
            5 => 'InProcess',
            1 => 'Transaction Completed',
            2 => 'Transaction Rejected',
            3 => 'Disbursed',
            4 => 'Response Rejected',
            6 => 'Processed'
        ];
    }
    public static function getLoanWriteOffReason(){
            return $reason = [
                    'disable' => 'Permanently Disable',
                    'death' => 'Death'
                   ];
    }
    public static function getLoanWriteOffStatus(){
            return $status = [
                    0 => 'Pending',
                    1 => 'Approved',
                    2 => 'Rejected'
            ];
    }
    public static function getDisbursementDetailStatusView($status){
       if($status==0)
       {
           $status='Pending';
       }
       else if ($status==1)
       {
           $status='Transaction Completed';
       }
       else if ($status==2)
       {
           $status='Transaction Rejected';
       }
       else if ($status==3)
       {
           $status= 'Disbursed';
       }
       else if ($status==5)
       {
           $status= 'InProcess';
       }
       else if ($status==6)
       {
           $status= 'Processed';
       }
        return $status;
    }

}