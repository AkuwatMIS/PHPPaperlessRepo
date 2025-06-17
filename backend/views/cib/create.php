<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ApplicationsCib */

$this->title = 'Create Applications Cib';
$this->params['breadcrumbs'][] = ['label' => 'Applications Cibs', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applications-cib-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
