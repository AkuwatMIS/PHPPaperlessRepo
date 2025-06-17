<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DynamicReports */
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Update Recovery File</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">

        <?= $this->render('_form', [
            'model' => $model,
            'reports_list' => $reports_list,
        ]) ?>
    </div>
</div>
