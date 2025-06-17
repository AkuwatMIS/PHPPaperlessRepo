<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\FilesAccounts */
?>
<div class="files-accounts-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'file_path',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return \common\components\Helpers\StructureHelper::getFilesaccountsstatus($model->status);
                },
            ],
            //'status',
            'total_records',
            'updated_records',
            'error_description:ntext',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',
        ],
    ]) ?>

</div>
