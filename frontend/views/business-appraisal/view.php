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
            'business_type',
            'place_of_business',
            'business_details',
            'business_income',
            'job_income',
            'house_rent_income',
            'other_income',
            'estimated_business_capital',
            'business_expenses',
            'income_before_business',
            'total_business_income',
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
