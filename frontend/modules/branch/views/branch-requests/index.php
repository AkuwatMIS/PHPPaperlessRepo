<?php

use yii\helpers\Html;
use yii\grid\GridView;
use common\models\BranchRequests;
use common\models\AuthItem;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BranchRequestsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Branch Requests';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-send"></span>
            Branch Requests
            <?php if(in_array('createapplications',$permissions))
            { ?>
                <a href="/branch/branch-requests/create" class="btn btn-success pull-right" title="Create Branch Request">Create Branch Request</a>

            <?php }?>
        </h6>
        <?php /*echo $this->render('_search', [
            'model' => $searchModel,
            'regions' => $array['regions'],
        ]); */?>
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function ($model) {
                    if ($model->status == 'approved') {
                        return ['class' => 'success'];
                    }
                },
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

//             'id',
                    [
                        'attribute' => 'name',
                        'format' => 'raw',
                        'value' => function ($data) {
                            return Html::a($data->name, ['view', 'id' => $data->id]);
                        },
                    ],
                    [
                        'attribute' => 'area_name',
                        'value' => 'area.name',
                        'label' => 'Area'
                    ],
                    [
                        'attribute' => 'region_name',
                        'value' => 'region.name',
                        'label' => 'Region'
                    ],
                    [
                        'attribute' => 'city_name',
                        'value' => 'city.name',
                        'label' => 'City'
                    ],
                    [
                        'attribute' => 'status',
                        'label' => 'Status',
                        'value' => function ($data) {
                            return $data->status;
                        },
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'header' => 'Action',
                        'contentOptions' => ['style' => 'width:70px'],
                        'template' => '{view} {update} {delete}',
                        'buttons' => [
                            /*'recommend' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-thumbs-up"></span>', $url, ['title' => 'Recommend','data-toggle' => 'tooltip']);
                            },
                            'approve' => function ($url, $model, $key) {
                                    return Html::a('<span class="glyphicon glyphicon-check"></span>', $url, ['title' => 'Approve','data-toggle' => 'tooltip']);
                            },*/
                            'delete' => function ($url, $model, $key) {
                                if(!$model->status == 'approved'){
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, ['title' => 'Delete','data-toggle' => 'tooltip']);
                                }
                            },
                        ],
                    ],
                ],
                'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                              ',
                'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
            ]); ?>
        </div>
    </div>
</div>
