<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

$this->title = $model->id;
/*$this->params['breadcrumbs'][] = ['label' => 'Project Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;*/
\yii\web\YiiAsset::register($this);
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4><?=$model->name .' as on '.date('d M Y',$date)?></h4>
                </div>
            </div>
        </div>
    </header>
    <section class="card mb-3">
        <header class="card-header card-header-lg">
            Project Info
        </header>
        <div class="profile-info-item" style="margin-top: 15px;">
            <div class="row">
                <div class="col-md-4">
                    <p>
                        <b>Total Funds Allocated</b> : Rs. <?=\common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($model->total_fund)?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Total Funds Received</b> : Rs. <?= \common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($model->fund_received )?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Started Date</b> : <?= isset($model->started_date)?date('d M, Y',$model->started_date):'Not Set'; ?>
                    </p>
                </div>

                <div class="col-md-4">
                    <p>
                        <b>Period</b> : <?= isset($model->project_period)?($model->project_period):'Not Set'; ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Ending Date</b> : <?= ($model->ending_date > 0)?date('d M, Y',$model->ending_date):'Not Set'; ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Service Charges Rate</b> : <?= $model->sc_type ?>
                    </p>
                </div>

            </div>
        </div>
    </section>
    <section class="card mb-3">
        <div class="profile-info-item"  style="margin-top: 15px;">
            <div class="row">
                <div class="col-md-4">
                    <p >
                        <b>Total Amount Disbursed</b> : Rs. <?= isset($progress_report_data['cum_disb'])?\common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount(($progress_report_data['cum_disb'])):0; ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Outstanding Loan Portfolio</b> : Rs. <?= isset($progress_report_data['olp_amount'])?\common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount(($progress_report_data['olp_amount'])):0; ?>
                    </p>
                </div>
            </div>
        </div>
    </section>
    <section class="card mb-3">
        <header class="card-header card-header-lg">
            Service Charges Detail
        </header>
        <div class="profile-info-item"  style="margin-top: 15px;">
            <div class="row">
                <div class="col-md-4">
                    <p>
                        <b>Total Receivable</b> : <?= isset($model->serviceCharges->received_amount) ? \common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($model->serviceCharges->received_amount) : 0 ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Total Received</b> : <?= isset($model->serviceCharges->remaining_amount) ? \common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($model->serviceCharges->remaining_amount) : 0 ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Total Pending</b> : <?= isset($model->serviceCharges->pending_amount) ? \common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($model->serviceCharges->pending_amount) : 0 ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Last Receiving Date</b> : <?= ($model->serviceCharges->received_date > 0)?date('d M, Y',$model->serviceCharges->received_date):'Not Set'; ?>
                    </p>
                </div>
                <div class="col-md-4">
                    <p>
                        <b>Last Request Sent on</b> : <?= ($model->serviceCharges->request_date > 0)?date('d M, Y',$model->serviceCharges->request_date):'Not Set'; ?>
                    </p>
                </div>
            </div>

        </div>
    </section>
</div>