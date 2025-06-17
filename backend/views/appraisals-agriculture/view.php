<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsAgriculture */
?>
<div class="appraisals-agriculture-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'application_id',
            'water_analysis',
            'soil_analysis',
            'laser_level',
            'irrigation_source',
            'other_source',
            'crop_year',
            'crop_production',
            'resources:ntext',
            'expenses',
            'available_resources',
            'required_resources',
            /*'agriculture_appraisal_address:ntext',
            'description:ntext',
            'latitude',
            'longitude',
            'status',
            'bm_verify_latitude',
            'bm_verify_longitude',
            'approved_by',
            'approved_on',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted',
            'platform',*/
        ],
    ]) ?>

</div>
