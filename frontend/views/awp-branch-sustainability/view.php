<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AwpBranchSustainability */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Awp Branch Sustainabilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-branch-sustainability-view">

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
            'branch_code',
            'region_id',
            'area_id',
            'month',
            'amount_disbursed',
            'percentage',
            'income',
            'actual_expense',
            'surplus_deficit',
        ],
    ]) ?>

</div>
