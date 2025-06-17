<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DisbursementRejected */

$this->title = 'Update Temporary Disbursement Rejected: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Temporary Disbursement Rejected', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="disbursement-rejected-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
