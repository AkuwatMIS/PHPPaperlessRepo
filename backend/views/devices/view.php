<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Devices */
?>
<div class="devices-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'uu_id',
            'imei_no',
            'os_version',
            'device_model',
            'push_id',
            'access_token',
            'status',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
