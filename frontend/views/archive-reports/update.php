<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ArchiveReports */
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Update Archive Report</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">

        <?= $this->render('_form', [
            'model' => $model,
            'report_names' => $report_names,
            'regions' => $regions,
            'projects' => $projects,
            'products' => $products,
            'activities' => $activities,
            //'gender' => $gender,
            'sources' => $sources,
        ]) ?>

    </div>
</div>
