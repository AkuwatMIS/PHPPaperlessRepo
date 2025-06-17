<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MembersAddress */
?>
<div class="members-address-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'member_id',
            'address',
            'address_type',
            'status',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
