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
        ],
    ]) ?>

</div>
