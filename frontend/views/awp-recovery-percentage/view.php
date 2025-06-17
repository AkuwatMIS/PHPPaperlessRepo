<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\AwpRecoveryPercentage */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Awp Recovery Percentages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="awp-recovery-percentage-view">

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
            'month',
            'branch_id',
            'area_id',
            'region_id',
            'branch_code',
            'recovery_count',
            'recovery_one_to_ten',
            'recovery_eleven_to_twenty',
            'recovery_twentyone_to_thirty',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
