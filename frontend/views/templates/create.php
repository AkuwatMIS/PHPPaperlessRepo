<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Templates */

?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Create Template</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_form', [
            'model' => $model,
            'placeholders'=>$placeholders
        ]) ?>
    </div>
</div>
