<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\FundRequests */
$this->title = 'Create Fund Request';
$this->params['breadcrumbs'][] = ['label' => 'Fund Request', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Create Fund Request</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_search', [
            'model' => $model,
            'regions' => $regions,
        ]) ?>
    </div>
    <?php if(!empty($fund_request_detail)){ ?>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_form', [
            'model' => $model,
            'fund_requests_details' => $fund_requests_details,
            'fund_request_detail'=>$fund_request_detail
        ]) ?>
    </div>
    <?php } ?>
</div>
