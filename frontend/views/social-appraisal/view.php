<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SocialAppraisal */
?>
<div class="social-appraisal-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            //'application_id',
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'member_name',
                'label'=>'Member Name',
                'value'=>function($data){
                    return isset($data->application->member->full_name)?$data->application->member->full_name:'N/A';

                }
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'member_cnic',
                'label'=>'Member CNIC',
                'value'=>function($data){
                    return isset($data->application->member->cnic)?$data->application->member->cnic:'N/A';

                }
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'application_no',
                'label'=>'Application No',
                'value'=>function($data){
                    return isset($data->application->application_no)?$data->application->application_no:'N/A';

                }
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'region_id',
                'label'=>'Region Name',
                'value'=>function($data){
                    return isset($data->application->region->name)?$data->application->region->name:'N/A';

                }
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'area_id',
                'label'=>'Area Name',
                'value'=>function($data){
                    return isset($data->application->area->name)?$data->application->area->name:'N/A';

                }
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'branch_id',
                'label'=>'Branch Name',
                'value'=>function($data){
                    return isset($data->application->branch->name)?$data->application->branch->name:'N/A';

                }
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'team_id',
                'label'=>'Team Name',
                'value'=>function($data){
                    return isset($data->application->team->name)?$data->application->team->name:'N/A';

                }
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'field_id',
                'label'=>'Field Name',
                'value'=>function($data){
                    return isset($data->application->field->name)?$data->application->field->name:'N/A';

                }
            ],
            'poverty_index',
            'house_ownership',
            'land_size',
            'total_family_members',
            'no_of_earning_hands',
            'ladies',
            'gents',
            'source_of_income',
            //'total_income',
            'utility_bills',
            //'house_amount',
            'educational_expenses',
            'medical_expenses',
            'kitchen_expenses',
            'monthly_savings',
            'amount',
            //'date_of_committee',
            //'bank_name',
            'other_expenses',
            'total_expenses',
            //'borrowed_amount',
            //'self_character',
            'economic_dealings',
            'social_behaviour',
            'fatal_disease',
            //'child',
            //'disease_type',
            'latitude',
            'longitude',
            'status',
            'approved_by',
            'approved_on',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
