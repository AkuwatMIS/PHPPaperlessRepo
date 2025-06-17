<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AwpOverdue */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Awp Overdues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-overdue-view">

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
            'month',
            'date_of_opening',
            'overdue_numbers',
            'overdue_amount',
            'awp_active_loans',
            'awp_olp',
            'active_loans',
            'olp',
            'diff_active_loans',
            'diff_olp',
        ],
    ]) ?>

</div>
