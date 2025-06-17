<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MembersAccount */
?>
<div class="members-account-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'member_id',
            'account_type',
            'bank_name',
            'title',
            'account_no',
            'is_current',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'status',
            'verified_at',
            'verified_by',
            'deleted',
        ],
    ]) ?>

</div>
