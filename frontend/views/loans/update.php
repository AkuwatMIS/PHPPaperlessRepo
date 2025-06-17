<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */
$this->title = $model->sanction_no;
$this->params['breadcrumbs'][] = ['label' => 'Loans', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Update Loan (<?= $model->sanction_no ?>)</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <table id="table-edit" class="table table-bordered table-hover">
            <tbody>
            <tr>
                <th>Name</th>
                <td><?= $application->member->full_name ?></td>
                <th>Parentage</th>
                <td><?= $application->member->parentage ?></td>
                <th>Branch</th>
                <td><?= $application->branch->name ?></td>
            </tr>
            <tr>
                <th>Application No</th>
                <td><?= $application->application_no ?></td>
                <th>CNIC</th>
                <td><?= $application->member->cnic ?></td>
                <th>Product</th>
                <td><?= $application->product->name ?></td>
            </tr>
            <tr>
                <th>Activity</th>
                <td><?= isset($application->activity->name)?$application->activity->name:''?></td>
                <th>Project</th>
                <td><?= $application->project->name ?></td>
                <th>Request Amount</th>
                <td><?= number_format($application->req_amount) ?></td>
            </tr>
            </tbody>
        </table>
        <br>
        <?= $this->render('_verify-form', [
            'model' => $application,
        ]) ?>
        <?= $this->render('_form', [
            'model' => $model,
            'application' => $application,
        ]) ?>

    </div>

</div>
