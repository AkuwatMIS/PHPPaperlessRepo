<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Donations */
?>
<div class="donations-update">

    <?= $this->render('_form', [
        'model' => $model,
        'projects' => $projects,
    ]) ?>

</div>
