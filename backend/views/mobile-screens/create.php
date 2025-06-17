<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MobileScreens */

$this->title = 'Create Mobile Screens';
$this->params['breadcrumbs'][] = ['label' => 'Mobile Screens', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mobile-screens-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
