<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AwpRecoveryPercentage */

$this->title = 'Create Awp Recovery Percentage';
$this->params['breadcrumbs'][] = ['label' => 'Awp Recovery Percentages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-recovery-percentage-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
