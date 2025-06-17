<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\RandomMembers */
?>
<div class="random-members-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'full_name',
            'cnic',
            'province_id',
            'city_id',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
