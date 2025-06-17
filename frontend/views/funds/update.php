<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Funds */
/* @var $projects common\models\Projects */
?>
<div class="funds-update">

    <?= $this->render('update_form', [
        'model' => $model,
        'projects' => $projects,
    ]) ?>

</div>
