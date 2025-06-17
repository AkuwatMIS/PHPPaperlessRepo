<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Projects */

?>
<div class="projects-create">
    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array
    ]) ?>
</div>
