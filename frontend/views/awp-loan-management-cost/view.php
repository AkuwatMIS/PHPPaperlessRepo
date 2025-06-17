<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AwpLoanManagementCost */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Awp Loan Management Costs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-loan-management-cost-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'branch_id',
            'area_id',
            'region_id',
            'date_of_opening',
            'opening_active_loans',
            'closing_active_loans',
            'average',
            'amount',
            'lmc',
        ],
    ]) ?>

</div>
