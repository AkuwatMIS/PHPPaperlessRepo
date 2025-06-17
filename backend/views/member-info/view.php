<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MemberInfo */
?>
<div class="member-info-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'member_id',
            'cnic_expiry_date',
            'cnic_issue_date',
            'mother_name',
            'created_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
