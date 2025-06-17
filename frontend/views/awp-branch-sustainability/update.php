<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AwpBranchSustainability */

$this->title = 'Update Awp Branch Sustainability: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Awp Branch Sustainabilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="awp-branch-sustainability-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
