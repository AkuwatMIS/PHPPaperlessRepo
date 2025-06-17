<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectFundDetail */
?>
<div class="project-fund-detail-update">
    <?= $this->render('_form', [
        'model' => $model,
        'projects' => $projects,
        'funds' => $funds,
        'fundLine' => $fundLine,
    ]) ?>

</div>
