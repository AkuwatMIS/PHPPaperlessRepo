<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MembersPhone */
?>
<div class="members-phone-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'member_id',
            'phone',
            'mobile',
            'status',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
