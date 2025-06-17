<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\UserHierarchyChangeRequest */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Hierarchy Change Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Transfer Details</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                //'id',
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
                    'label'=>'Field',
                    'value' => function ($model) {
                        if ($model->field_id != 0) {
                            return isset($model->field->name) ? $model->field->name : '--';
                        } else {
                            return 'N/A';
                        }
                    },
                ],
                'status',
                [
                    'attribute' => 'created_by',
                    'label'=>'Created By',
                    'value' => function ($model) {
                        if ($model->created_by != 0) {
                            return \common\components\Helpers\UsersHelper::getUserName($model->created_by)->username;
                        } else {
                            return 'N/A';
                        }
                    },
                ],
                [
                    'attribute' => 'recommended_by',
                    'label'=>'Recommended By',
                    'value' => function ($model) {
                        if ($model->recommended_by != 0) {
                            return \common\components\Helpers\UsersHelper::getUserName($model->recommended_by)->username;
                        } else {
                            return 'N/A';
                        }
                    },
                ],
            ],
        ]) ?>
    </div>
</div>

