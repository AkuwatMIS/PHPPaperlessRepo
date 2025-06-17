<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsBusiness */
$this->title = 'Create Business Appraisal';
$this->params['breadcrumbs'][] = ['label' => 'Business Appraisal', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Create Business Appraisal</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_form', [
            'model' => $model,
            'ba_details'=>$ba_details
        ]) ?>
    </div>
</div>
