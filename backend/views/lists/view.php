<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Lists */
?>
<div class="lists-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'list_name',
            'value',
            'label',
            'sort_order',
        ],
    ]) ?>

</div>
