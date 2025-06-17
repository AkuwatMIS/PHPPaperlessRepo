<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Versions */
?>
<div class="versions-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'version_no',

            /*'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted',*/
        ],
    ]) ?>

</div>
