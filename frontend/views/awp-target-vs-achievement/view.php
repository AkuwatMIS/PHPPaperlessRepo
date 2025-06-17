<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AwpTargetVsAchievement */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Awp Target Vs Achievements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-target-vs-achievement-view">

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
            'region_id',
            'area_id',
            'branch_id',
            'project_id',
            'month',
            'target_loans',
            'target_amount',
            'achieved_loans',
            'achieved_amount',
            'loans_dif',
            'amount_dif',
        ],
    ]) ?>

</div>
