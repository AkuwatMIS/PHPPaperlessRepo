<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MobileScreens */

$this->title = 'Update Mobile Screens: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Mobile Screens', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mobile-screens-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
