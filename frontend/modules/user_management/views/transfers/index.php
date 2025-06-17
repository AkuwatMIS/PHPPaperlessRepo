<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserTransfersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Transfers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">

    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-user"></span>
            Transfer List</h6>
        <div class="table-responsive">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'user_id',
                'label'=>'User',
                'value'=>'user.fullname',
            ],
            /*[
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'role',
                'label'=>'Role',
                'value'=>'role.itemName.name',
            ],*/
            'type',
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'region_id',
                'label'=>'Region',
                'value'=>'region.name',
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'area_id',
                'label'=>'Area',
                'value'=>'area.name',
            ],
            [
                //'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'branch_id',
                'label'=>'Branch',
                'value'=>'branch.name',
            ],
            //'area_id',
            //'branch_id',
            //'team_id',
            //'field_id',
            //'status',
            //'remarks:ntext',
            //'assigned_to',
            //'created_by',
            //'updated_by',
            //'created_at',
            //'updated_at',
            //'deleted',

[
            'class' => 'yii\grid\ActionColumn',
            'visibleButtons' => [
                'delete' => function ($model, $key, $index) {
                    return $model->status == 0;
                }
            ],

            'template' => '{view}{delete}',
            'contentOptions' => ['style' => 'width:90px;'],
        ],

    ],
    ]); ?>
        </div>

</div>
</div>
