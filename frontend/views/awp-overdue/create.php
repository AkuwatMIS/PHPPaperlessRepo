<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AwpOverdue */

$this->title = 'Create Awp Overdue';
$this->params['breadcrumbs'][] = ['label' => 'Awp Overdues', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-overdue-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
