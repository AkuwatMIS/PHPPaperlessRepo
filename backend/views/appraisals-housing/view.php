<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsHousing */
?>
<div class="appraisals-housing-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'property_type',
            'ownership',
            'land_area',
            'residential_area',
            'living_duration',
            'duration_type',
            'no_of_rooms',
            'no_of_kitchens',
            'no_of_toilets',
            'purchase_price',
            'current_price',
            'address:ntext',
            'estimated_figures:ntext',
            'estimated_start_date',
            'estimated_completion_time:datetime',
            'housing_appraisal_address:ntext',
            'description:ntext',
            'description_image',
            'latitude',
            'longitude',
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
