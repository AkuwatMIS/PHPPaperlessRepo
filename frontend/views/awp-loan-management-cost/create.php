<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AwpLoanManagementCost */

$this->title = 'Create Awp Loan Management Cost';
$this->params['breadcrumbs'][] = ['label' => 'Awp Loan Management Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-loan-management-cost-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
