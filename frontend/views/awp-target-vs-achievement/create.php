<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AwpTargetVsAchievement */

$this->title = 'Create Awp Target Vs Achievement';
$this->params['breadcrumbs'][] = ['label' => 'Awp Target Vs Achievements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-target-vs-achievement-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
