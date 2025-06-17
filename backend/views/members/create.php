<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Members */

?>
<div class="members-create">
    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array
    ]) ?>
</div>
