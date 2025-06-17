<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Appraisals */
?>
<div class="appraisals-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'appraisal_table',
            'status',
            'created_at',
            'updated_at',
            'created_by',
            'updated_by',
        ],
    ]) ?>

</div>
