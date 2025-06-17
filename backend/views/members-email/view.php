<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MembersEmail */
?>
<div class="members-email-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'member_id',
            'email:email',
            'status',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
