<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\SocialAppraisalCopy */
?>
<div class="social-appraisal-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            [
                'label'=>'Member Name',
                'value'=>function($data){return isset($data->application->member->full_name)?$data->application->member->full_name:'';}
            ],
            'poverty_index',
            'total_family_members',
            'ladies',
            'gents',
            'source_of_income',
            'utility_bills',
            'educational_expenses',
            'medical_expenses',
            'kitchen_expenses',
            'other_expenses',
            'total_expenses',
            'economic_dealings',
            'social_behaviour',
            'latitude',
            'longitude',
            'status',
            'approved_by',
            'approved_on',
//            'assigned_to',
//            'created_by',
//            'updated_by',
//            'created_at',
//            'updated_at',
        ],
    ]) ?>

</div>
