<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ApplicationActions */
?>
<div class="application-actions-update">

    <?= $this->render('_form', [
        'model' => $model,
        'action_list' => $action_list,
    ]) ?>

</div>
