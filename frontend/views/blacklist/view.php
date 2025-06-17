<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Blacklist */
$this->title = $model->cnic;
$this->params['breadcrumbs'][] = ['label' => 'Blacklist', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Blacklist Detail</h4>
                </div>
            </div>
        </div>
    </header>
            <section class="box-typical">
                <div class="blacklist-view">

                    <?= DetailView::widget([
                        'model' => $model,
                        'attributes' => [
                            //'id',
                            //'member_id',
                            'name',
                            'cnic',
                            'reason:ntext',
                            'description:ntext',
                            'location',
                            'type',
                            [
                                'attribute' => 'created_by',
                                'value' => function($data) {
                                    return $data->user->fullname;
                                }
                            ],
                            [
                                'attribute' => 'created_at',
                                'value' => function($data) {
                                    return date('d M Y',$data->created_at);
                                }
                            ],
                            [
                                'attribute' => 'Updated_at',
                                'value' => function($data) {
                                    return date('d M Y',$data->updated_at);
                                }
                            ]
                            //'created_by',
                           // 'created_at',
                           // 'updated_at',
                            //'deleted',
                        ],
                    ]) ?>

                </div>
            </section>
</div>

