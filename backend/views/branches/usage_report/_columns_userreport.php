<?php
use yii\helpers\Url;
use yii\helpers\Html;
use kartik\grid\GridView;
use common\models\Borrowers;
use common\components\LoanHelper;
use common\components\RbacHelper;
use yii\helpers\ArrayHelper;
return [
    /*[
        'class' => 'kartik\grid\CheckboxColumn',
        'width' => '20px',
    ],*/
    /*[
        'class' => 'yii\grid\SerialColumn',
    ],*/


    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'region_id',
        'value' => 'region.name',
        'label'=>'Regions',
        'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'area_id',
        'value' => 'area.name',
        'filter'=>$areas,
        'label'=>'Areas',
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'id',
        'label' => 'Branch',
        'filter'=>$branches,
        'value' => 'name',
    ],

    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_members',
        'label'=>'Members',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_members'),
        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_applications',
        'label'=>'Applications',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_applications'),

        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_social_appraisals',
        'label'=>'Social Appraisals',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_social_appraisals'),

        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_business_appraisals',
        'label'=>'Business Appraisals',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_business_appraisals'),

        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_groups',
        'label'=>'Groups',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_groups'),

        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_verifications',
        'label'=>'Verifications',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_verifications'),

        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_loans',
        'label'=>'Loans',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_loans'),

        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_fund_requests',
        'label'=>'Fund Requests',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_fund_requests'),

        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_disbursements',
        'label'=>'Disbusements',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_disbursements'),

        //  'filter'=>$regions,
    ],
    [
        'class'=>'\yii\grid\DataColumn',
        'attribute'=>'no_of_recoveries',
        'label'=>'Recoveries',
        'footer' => \common\components\Helpers\StructureHelper::getTotal($dataProvider->getModels(), 'no_of_recoveries'),

        //  'filter'=>$regions,
    ],


];   