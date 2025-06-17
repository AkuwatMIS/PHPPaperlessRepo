<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Teams */

?>
<div class="teams-create">
    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array
    ]) ?>
</div>
