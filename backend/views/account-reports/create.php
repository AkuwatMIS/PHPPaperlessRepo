<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProgressReports */

?>
<div class="progress-reports-create">
    <?= $this->render('_form', [
        'model' => $model,
        'status' => $status,
        'flags' => $flags,
        'projects' => $projects,
        'period' => $period,
    ]) ?>
</div>
