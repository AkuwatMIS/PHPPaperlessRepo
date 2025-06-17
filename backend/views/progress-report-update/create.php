<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ProgressReportUpdate */

?>
<div class="progress-report-update-create">
    <?= $this->render('_form', [
        'model' => $model,
        'progress_reports'=>$progress_reports,
        'regions'=>$regions,

    ]) ?>
</div>
