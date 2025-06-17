<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ConfigRules */
?>
<div class="config-rules-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'group',
            'priority',
            'key',
            'value',
            'parent_type',
            'parent_id',
            'project_id',
        ],
    ]) ?>

</div>
