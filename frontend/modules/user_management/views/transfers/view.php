<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserTransfers */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">

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
            'user_id',
            'type',
            'division_id',
            'region_id',
            'area_id',
            'branch_id',
            'team_id',
            'field_id',
            'status',
            'remarks:ntext',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
            'deleted',
        ],
    ]) ?>

</div>
