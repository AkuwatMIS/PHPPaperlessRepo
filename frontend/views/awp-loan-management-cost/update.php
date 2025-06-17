<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AwpLoanManagementCost */

$this->title = 'Update Awp Loan Management Cost: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Awp Loan Management Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="awp-loan-management-cost-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
