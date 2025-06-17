<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Cities */

?>
<div class="cities-create">
    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array
    ]) ?>
</div>
