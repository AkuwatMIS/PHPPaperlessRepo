<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Analytics */
?>
<div class="analytics-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'user.email',
            'api',
            'count',
            'description:ntext',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
