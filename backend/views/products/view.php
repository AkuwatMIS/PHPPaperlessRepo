<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Products */
?>
<div class="products-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
           // 'id',
            'name',
            'code',
            'inst_type',
            'min',
            'max',
            'status',
            'assigned_to',
           /* 'created_by',
            'updated_by',
            'created_at',
            'updated_at',*/
        ],
    ]) ?>

</div>
<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>#</th>
            <th>Activities</th>
        </tr>
        </thead>
        <tbody>
        <?php $count=1; foreach($array['model_productwithactivity']->activity_ids as $id){ ?>
            <tr>
                <td><?= $count ?></td>
                <td><?= $array['activities'][$id] ?></td>
            </tr>
            <?php $count++; } ?>
        </tbody>
    </table>
</div>