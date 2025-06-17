<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsBusiness */
?>
<div class="business-appraisal-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            [
                'label'=>'Member Name',
                'value'=>function($data){return isset($data->application->member->full_name)?$data->application->member->full_name:'';}
            ],
            //'business_type',
            //'business_name',
            'place_of_business',
            'fixed_business_assets',
            'fixed_business_assets_amount',
            'running_capital_amount',
            'business_expenses',
            'business_expenses_amount',
            'new_required_assets',
            'new_required_assets_amount',
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
        ],
    ]) ?>

</div>
