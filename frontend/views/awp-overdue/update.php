<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AwpOverdue */

$this->title = 'Update Awp Overdue: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Awp Overdues', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="awp-overdue-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
