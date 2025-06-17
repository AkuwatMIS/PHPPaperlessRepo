<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsSocial */
?>
<div class="appraisals-social-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'poverty_index',
            'house_ownership',
            'house_rent_amount',
            'land_size',
            'total_family_members',
            'no_of_earning_hands',
            'ladies',
            'gents',
            'source_of_income',
            'total_household_income',
            'utility_bills',
            'educational_expenses',
            'medical_expenses',
            'kitchen_expenses',
            'monthly_savings',
            'amount',
            'date_of_maturity',
            'other_expenses',
            'total_expenses',
            'other_loan',
            'loan_amount',
            'economic_dealings',
            'social_behaviour',
            'fatal_disease',
            'business_income',
            'job_income',
            'house_rent_income',
            'other_income',
            'expected_increase_in_income',
            'description:ntext',
            'description_image',
            'latitude',
            'longitude',
            'social_appraisal_address:ntext',
            'status',
            'bm_verify_latitude',
            'bm_verify_longitude',
            'is_lock',
            'approved_by',
            'approved_on',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted',
            'platform',
        ],
    ]) ?>

</div>
