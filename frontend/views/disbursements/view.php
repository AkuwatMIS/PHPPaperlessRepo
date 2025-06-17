<?php

use yii\widgets\DetailView;
use \yii\helpers\Url;
/* @var $this yii\web\View */
/* @var $model common\models\Disbursements */
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Disbursements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Dibursement</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="row">

            <div class="col-md-6">
                <article class="profile-info-item">
                    <header class="profile-info-item-header">
                        <i class="font-icon font-icon-help"></i>
                        <b>Disbursement Information</b>
                    </header>
                    <div class="box-typical-inner">
                        <p>
                            <b>Disbursement Date</b> : <?= date('d M Y',$model->date_disbursed); ?>
                        </p>
                        <p>
                            <b>Venue</b> : <?= $model->venue ?>
                        </p>
                        <p>
                            <b>Loans Disbursed</b> : <?= count($model->tranches) ?>
                        </p>
                        <p>
                            <b>Amount Disbursed</b> : <?= number_format($disb_amount) ?>
                        </p>

                    </div>
                <!--</article>--><!--.profile-info-item-->

            </div>
            <div class="col-md-4">
                <article class="profile-info-item">
                    <header class="profile-info-item-header">
                        <i class="font-icon font-icon-view-rows"></i>
                        <b>Credit Structure Information</b>
                    </header>
                    <div class="box-typical-inner">
                        <p>
                            <b>Region</b>
                            : <?= isset($model->region->name) ? $model->region->name : 'Not Set'; ?>
                        </p>
                        <p>
                            <b>Area</b>
                            : <?= isset($model->area->name) ? $model->area->name : 'Not Set'; ?>
                        </p>
                        <p>
                            <b>Branch</b>
                            : <?= isset($model->branch->name) ? $model->branch->name : 'Not Set'; ?>
                        </p>

                    </div>
                </article><!--.profile-info-item-->
            </div>
        </div>
    <!--<div class="disbursements-view">

        <?/*= DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'id',
                ['attribute' => 'date_disbursed',
                    'value' => function ($data) {
                        return date('Y-m-d', $data->date_disbursed);
                    }
                ],
                'venue',
                //'assigned_to',
                //'created_by',
                //'updated_by',
                //'created_at',
                //'updated_at',
                //'deleted',
            ],
        ]) */?>

    </div>-->
        <div class="col-md-12">
            <div class="table-responsive">
                <h6 class="address-heading"><span class="fa fa-tasks"></span>
                    Loans Listing</h6>
                <?= \yii\grid\GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => $model->tranches,
                        'pagination' => [
                            'pageSize' => 50,
                        ],
                    ]),
                    'columns' => [
                        [
                            'class' => 'yii\grid\SerialColumn',
                        ],
                        [
                            //'class'=>'\kartik\grid\DataColumn',
                            'attribute'=>'application_id',
                            'value'=>'loan.application.member.full_name',
                            'label'=>'Member Name',
                            'class' => \dimmitri\grid\ExpandRowColumn::class,
                            'ajaxErrorMessage' => 'Oops',
                            'ajaxMethod' => 'GET',
                            'url' => Url::to(['/applications/member-details']),
                            'submitData' => function ($model, $key, $index) {
                                return ['id' => $model->loan->application->member->id];
                            },
                            'enableCache' => false,
                            'format' => 'raw',
                            'expandableOptions' => [
                                'title' => 'Click me!',
                                'class' => 'my-expand',
                            ],

                        ],

                        //[
                        //'class'=>'\kartik\grid\DataColumn',
                        //'attribute'=>'fee',
                        // ],
                        [
                            //'class'=>'\kartik\grid\DataColumn',
                            'value'=>'loan.sanction_no',
                            'label'=>'Sanction No',
                            'class' => \dimmitri\grid\ExpandRowColumn::class,
                            'ajaxErrorMessage' => 'Oops',
                            'ajaxMethod' => 'GET',
                            'url' => Url::to(['/loans/loan-details']),
                            'submitData' => function ($model, $key, $index) {
                                return ['id' => $model->loan->id];
                            },
                            'enableCache' => false,
                            'format' => 'raw',
                            'expandableOptions' => [
                                'title' => 'Click me!',
                                'class' => 'my-expand',
                            ],


                        ],
                        [
                            'attribute'=>'tranch_no',
                            'value'=>function ($data) {
                                return number_format($data->tranch_no);}
                        ],
                        [
                            'attribute'=>'tranch_amount',
                            'value'=>function ($data) {
                                  return number_format($data->tranch_amount);}
                        ]
                    ],
                    'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{count}</strong> items.
                              ',
                    'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                ]); ?>

            </div>
        </div>
</div>
