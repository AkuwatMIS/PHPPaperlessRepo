<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsBusiness */
?>
<div class="appraisals-business-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'place_of_business',
            'fixed_business_assets:ntext',
            'fixed_business_assets_amount',
            'running_capital:ntext',
            'running_capital_amount',
            'business_expenses:ntext',
            'business_expenses_amount',
            'new_required_assets:ntext',
            'new_required_assets_amount',
            'latitude',
            'longitude',
            'business_appraisal_address:ntext',
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
