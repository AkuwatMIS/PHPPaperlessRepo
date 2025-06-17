<?php

use common\models\MemberInfo;
use common\models\Members;
use common\models\RejectedNadraVerisys;
use yii\helpers\Url;


return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    [
        'class' => 'kartik\grid\SerialColumn',
        'width' => '30px',
    ],
    // [
    // 'class'=>'\kartik\grid\DataColumn',
    // 'attribute'=>'id',
    // ],


    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'cnic',
        'label' => 'CNIC',
        'value' => 'member.cnic',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'app_no',
        'label' => 'Application No',
        'value' => 'application_no',
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'app_date',
        'label' => 'Application Date',
        'value'=>function ($data, $key, $index) {
            return \common\components\Helpers\StringHelper::dateFormatter($data->application_date);
        }
    ],
    [
        'class' => '\kartik\grid\DataColumn',
        'attribute' => 'nadra_status',
        'label' => 'Nadra Verysis Status',
        'value' => function ($model, $key, $index) {

//            $member = Members::find()->where(['cnic' => $model->cnic])->one();
//            $member_info = MemberInfo::find()->where(['member_id' => $member->id])->one();
            $rejected_nadra = RejectedNadraVerisys::find()->where(['application_id' => $model->id])->one();
            if (!empty($rejected_nadra) && $rejected_nadra != null) {
                if ($rejected_nadra->status == 0) {
                    $nadra_verysis_status = ' Nadra Verysis Rejected';
                } elseif ($rejected_nadra->status == 1) {
                    $nadra_verysis_status = 'Nadra Verysis Resubmitted';
                } elseif ($rejected_nadra->status == 2) {
                    $nadra_verysis_status = 'Nadra Verysis Completed';
                } else {
                    $nadra_verysis_status = ' Nadra Verysis Pending';
                }
            } elseif (isset($rejected_nadra) == null) {
                if (isset($model->nadra->document_name) && $model->nadra->status == 1) {
                    $nadra_verysis_status = 'Nadra Verysis Completed';
                } else {
                    $nadra_verysis_status = 'Nadra Verysis Pending';
                }

            }
            return $nadra_verysis_status;

        }
    ],


];