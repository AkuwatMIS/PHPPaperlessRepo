<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model app\models\ProjectCharges */
/* @var $modelFiles app\models\ProjectFiles[] */

$this->title = $model->id;
\yii\web\YiiAsset::register($this);
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4><?= Html::encode($model->name . ' as on ' . date('d M Y', $model->started_date)) ?></h4>
                </div>
            </div>
        </div>
    </header>

    <section class="card mb-3">
        <header class="card-header card-header-lg">Project Info</header>
        <div class="profile-info-item" style="margin-top: 15px;">
            <div class="row">
                <div class="col-md-6">
                    <p><b>Total Funds Allocated</b> : Rs. <?= \common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($model->total_fund) ?></p>
                </div>
                <div class="col-md-6">
                    <p><b>Total Funds Received</b> : Rs. <?= \common\components\Helpers\ReportsHelper\NumberHelper::getFormattedNumberAmount($model->fund_received) ?></p>
                </div>
                <div class="col-md-6">
                    <p><b>Started Date</b> : <?= isset($model->started_date) ? date('d M, Y', $model->started_date) : 'Not Set'; ?></p>
                </div>
                <div class="col-md-6">
                    <p><b>Period</b> : <?= isset($model->project_period) ? $model->project_period : 'Not Set'; ?></p>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($modelFiles)): ?>
        <section class="card mb-3">
            <header class="card-header card-header-lg">Project Files</header>
            <div class="card-block">
                <?= GridView::widget([
                    'dataProvider' => new \yii\data\ArrayDataProvider([
                        'allModels' => $modelFiles,
                        'pagination' => ['pageSize' => 10],
                    ]),
                    'columns' => [
                        'id',
                        'amount',
                        [
                            'attribute' => 'file_path',
                            'label' => 'File',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return Html::a('Download', ['projects/download-file', 'id' => $model->id], [
                                    'class' => 'btn btn-sm btn-outline-primary',
                                ]);
                            }
                        ],
                        [
                            'attribute' => 'status',
                            'value' => function ($model) {
                                return $model->status == 1 ? 'Approved' : 'Pending';
                            },
                            'contentOptions' => function ($model) {
                                return ['class' => $model->status == 1 ? 'text-success' : 'text-warning'];
                            }
                        ],
                        'created_by',
                        [
                            'attribute' => 'approved_by',
                            'label' => 'Approved By',
                            'value' => function ($model) {
                                return $model->approved_by ?: 'N/A';
                            }
                        ],
                        [
                            'attribute' => 'approved_at',
                            'label' => 'Approved At',
                            'value' => function ($model) {
                                return $model->approved_at ? date('d M Y H:i', $model->approved_at) : 'N/A';
                            }
                        ],
                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{approve}',
                            'buttons' => [
                                'approve' => function ($url, $model) {
                                    if ($model->status == 1) {
                                        return '<span class="badge badge-success">Approved</span>';
                                    }
                                    return Html::a('<span class="fa fa-check"></span> Approve', ['projects/approve-file', 'id' => $model->id], [
                                        'class' => 'btn btn-sm btn-success',
                                        'data-method' => 'post',
                                        'data-confirm' => 'Are you sure you want to approve this file?',
                                    ]);
                                }
                            ]
                        ],
                    ]
                ]) ?>
            </div>
        </section>
    <?php endif; ?>
</div>
