<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\VigaLoans */
?>
<div class="viga-loans-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'loan_id',
            'status',
            [
                'attribute'=>'users.username',
                'label'=>'Created By',
            ],
            [
                'attribute'=>'users.username',
                'label'=>'Updated By',
            ],
            [
                'attribute' => 'created_at',
                'value'=>function ($model) {
                    return date('d M Y',$model->created_at);
                },
            ],
            [
                'attribute' => 'updated_at',
                'value'=>function ($model) {
                    return date('d M Y',$model->updated_at);
                },
            ],
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>
