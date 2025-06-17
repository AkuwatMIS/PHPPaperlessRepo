<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AwpTargetVsAchievement */

$this->title = 'Update Awp Target Vs Achievement: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Awp Target Vs Achievements', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="awp-target-vs-achievement-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
