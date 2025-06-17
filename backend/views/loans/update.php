<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */
?>
<div class="loans-update">

    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array,
    ]) ?>

</div>
