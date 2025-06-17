<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Members */
$this->title = $members->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $members->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3>Update Member</h3>
                </div>
            </div>
        </div>
    </header>

    <div class="box-typical box-typical-padding">

        <?= $this->render('_form', [
            'members' => $members,
            'membersAddress' => $membersAddress,
            'membersPhone' => $membersPhone,
            'membersEmail' => $membersEmail,
            'membersAccount' => $membersAccount,
            'branches' => $branches,
            'member_info' => $member_info,
        ]) ?>

    </div>
</div>
