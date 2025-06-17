<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Applications */
?>
<div class="applications-update">

    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array
    ]) ?>

</div>
