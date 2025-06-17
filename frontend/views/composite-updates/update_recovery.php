<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Recoveries */
?>
<div class="recoveries-update">

    <?= $this->render('_form_recovery', [
        'model' => $model,
        'projects' => $projects,
    ]) ?>

</div>
