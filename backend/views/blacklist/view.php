<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Blacklist */
?>
<div class="blacklist-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'member_id',
            'cnic',
            'reason:ntext',
            'description:ntext',
            'location',
            'type',
            'created_by',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
