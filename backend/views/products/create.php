<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Products */

?>
<div class="products-create">
    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array

    ]) ?>
</div>
