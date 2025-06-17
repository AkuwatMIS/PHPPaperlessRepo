<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Referrals */
?>
<div class="referrals-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'type',
            'name',
            'contact_no',
            'email:email',
            'description:ntext',
            'status',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
