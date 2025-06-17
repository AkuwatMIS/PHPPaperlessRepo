<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AwpRecoveryPercentage */

$this->title = 'Update Awp Recovery Percentage: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Awp Recovery Percentages', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="awp-recovery-percentage-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
