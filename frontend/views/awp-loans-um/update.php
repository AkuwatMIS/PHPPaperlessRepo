<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AwpLoansUm */

$this->title = 'Update Awp Loans Um: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Awp Loans Ums', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="awp-loans-um-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
