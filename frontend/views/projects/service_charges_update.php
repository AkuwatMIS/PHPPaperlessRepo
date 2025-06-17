<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ProjectCharges */

$this->title = 'Update Project Charges: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Project Charges', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

    <div class="container-fluid">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4><?=$project_model->name .' as on '.date('d M Y',$date)?></h4>
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
                    <div class="col-md-6">
                        <p>
                            <b>Total Funds Allocated</b> : Rs. <?=\common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($project_model->total_fund)?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>Total Funds Received</b> : Rs. <?= \common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($project_model->fund_received )?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>Started Date</b> : <?= isset($project_model->started_date)?date('Y-m-d',$project_model->started_date):'Not Set'; ?>
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p>
                            <b>Period</b> : <?= isset($project_model->project_period)?($project_model->project_period):'Not Set'; ?>
                        </p>
                    </div>
<!--                    <div class="col-md-4">-->
<!--                        <p>-->
<!--                            <b>Ending Date</b> : --><?//= ($project_model->ending_date > 0)?date('Y-m-d',$project_model->ending_date):'Not Set'; ?>
<!--                        </p>-->
<!--                    </div>-->
<!--                    <div class="col-md-4">-->
<!--                        <p>-->
<!--                            <b>Service Charges Rate</b> : --><?//= $project_model->sc_type ?>
<!--                        </p>-->
<!--                    </div>-->
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
        <div class="box-typical box-typical-padding">

            <?= $this->render('_form', [
                'model' => $model,
                'projects' => $projects,
            ]) ?>

        </div>
    </div>
