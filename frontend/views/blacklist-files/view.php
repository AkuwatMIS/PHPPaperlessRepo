<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\BlacklistFiles */
?>
<div class="blacklist-files-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'file_name',
            'result_file_name',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
