<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UserHierarchyChangeRequestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'User Transfers';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4><?= Html::encode($this->title) ?></h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-rocket"></span>
            Transfer Request </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions' => $array['regions'],
        ]); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'rowOptions'=>function($model){
            if($model->status == 'approved'){
                return ['class' => 'success'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'user_id',
                'label'=>'Username',
                'value'=>function ($model) {
                    return isset($model->user->username)?$model->user->username:'--';
                },
            ],
            [
                'attribute'=>'region_id',
                'label'=>'Region',
                'value'=>function ($model) {
                    if($model->region_id!=0) {
                        return isset($model->region->name) ? $model->region->name : '--';
                    }
                    else{
                        return'N/A';
                    }
                },
            ],
            [
                'attribute'=>'area_id',
                'label'=>'Area',
                'value' => function ($model) {
                    if ($model->area_id != 0) {
                        return isset($model->area->name) ? $model->area->name : '--';
                    } else {
                        return 'N/A';
                    }
                },

            ],
            [
                'attribute' => 'branch_id',
                'label'=>'Branch',
                'value' => function ($model) {
                    if ($model->branch_id != 0) {
                        return isset($model->branch->name) ? $model->branch->name : '--';
                    } else {
                        return 'N/A';
                    }
                },
            ],
            [
                'attribute' => 'team_id',
                'label'=>'Team',
                'value' => function ($model) {
                    if ($model->team_id != 0) {
                        return isset($model->team->name) ? $model->team->name : '--';
                    } else {
                        return 'N/A';
                    }
                },
            ],
            [
                'attribute' => 'field_id',
                'label'=>'Filed',
                'value' => function ($model) {
                    if ($model->field_id != 0) {
                        return isset($model->field->name) ? $model->field->name : '--';
                    } else {
                        return 'N/A';
                    }
                },
            ],
            'status',
            /*[
                'attribute'=>'created_by',
                'value'=>function ($model) {
                    return isset($model->user->username)?$model->user->username:'--';
                },
            ],
            [
                'attribute' => 'recommended_by',
                'value' => function ($model) {
                    if ($model->recommended_by != 0) {
                        return isset($model->user->username) ? $model->user->username : '--';
                    } else {
                        return 'N/A';
                    }
                },
            ],*/

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:70px;'],
                'urlCreator' => function($action, $model, $key, $index) {
                    return \yii\helpers\Url::to([$action,'id'=>$key]);
                },
                'buttons' => [

                    'recommend' => function ($url, $model, $key) {
                        if ($model->status == 'pending') {
                            return \yii\helpers\Html::a('<span class="glyphicon glyphicon-send"></span>', ['recommend-by-rm', 'id' => $model->id], ['title' => 'Recommend by RA']);
                        }
                    },
                    'approve' => function ($url, $model, $key) {
                        if ($model->status == 'recommended') {
                            return \yii\helpers\Html::a('<span class="glyphicon glyphicon-ok"></span>', ['approve-by-da', 'id' => $model->id], ['title' => 'Approve by DA']);
                        }
                    },
                ],

                'template' => '{recommend} {approve} {view}',
            ],
        ],
        'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],

        ]); ?>
</div>
</div>
