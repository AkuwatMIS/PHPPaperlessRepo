<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\StructureTransfer */
?>
<div class="structure-transfer-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'obj_type',
            'old_value',
            'new_value',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
