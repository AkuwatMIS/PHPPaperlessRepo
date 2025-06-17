<?php

use yii\widgets\DetailView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Applications */
?>
<div class="applications-view">

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            ['attribute' => 'member_id',
                'value' => function ($data) {
                    return $data->member->full_name;
                }
            ],
            [
                'attribute' => 'application_id',
                'value' => function ($data) {
                    return $data->application->application_no;
                }
            ],
            [
                'attribute' => 'document_type',
            ],
            [
                'attribute' => 'document_name',
            ],
            'deleted',
            [
                'attribute' => 'application_id',
                'value' => function ($data) {
                    if ($data->status == 1) {
                        return 'Nadra Verisys Completed';
                    } else {
                        return 'Nadra Verisys pending';
                    }
                }
            ],

        ],
    ]) ?>

</div>
