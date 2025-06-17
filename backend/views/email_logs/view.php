<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\EmailLogs */
?>
<div class="email-logs-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'sender_email:email',
            'receiver_email:email',
            'created_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
