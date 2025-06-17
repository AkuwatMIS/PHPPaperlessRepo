<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AwpOverdueSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>




<div class="awp-overdue">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'layout' => '{items}',
        'tableOptions' => ['class' => 'table table-hover table-condensed'],
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn',],

//        ['attribute'=>'branch_id',
//            'value'=>function($data){return date('F,Y',$data->branch->opening_date);},
//            'headerOptions' => ['style' => 'width:20%'],
//            'label' => false,
//        ],
//        ['attribute'=>'active_loans',
//            'value'=>function($data){return number_format($data->active_loans);},
//            'headerOptions' => ['style' => 'width:10%'],
//            'label' => false,
//        ],
//        ['attribute'=>'overdue_numbers',
//            'value'=>function($data){return number_format($data->overdue_numbers);},
//            'headerOptions' => ['style' => 'width:10%'],
//            'label' => false,
//            'label' => false,
//        ],
//        ['attribute'=>'overdue_amount',
//            'value'=>function($data){return number_format($data->overdue_amount);},
//            'headerOptions' => ['style' => 'width:10%'],
//            'label' => false,
//        ],
            ['attribute'=>'branch_id',
                //'format'=>'decimal',
                'value'=>function($data){return $data->branch_id.' 2020';},
                'header'=>'<b style="color: White">branch</b>',
            ],
            ['attribute'=>'month',
                //'format'=>'decimal',
                'value'=>function($data){return 'June 2020';},
//                'value'=>function($data){return date("F", strtotime($data->month));},
                'header'=>'<b style="color: White">Month</b>',
            ],
            ['attribute'=>'diff_olp',
                'value'=>function($data){return number_format($data->diff_olp);},
                'headerOptions' => ['style' => ''],
                'label' => false,
            ],
            ['attribute'=>'def_recovered',
                'value'=>function($data){return number_format($data->def_recovered);},
                'headerOptions' => ['style' => ''],
                'label' => false,
            ],
            ['attribute'=>'awp_olp',
                'value'=>function($data){return number_format($data->awp_olp);},
                'headerOptions' => ['style' => ''],
                'label' => false,
            ],
            ['attribute'=>'write_off_amount',
                'value'=>function($data){return number_format($data->write_off_amount);},
                'headerOptions' => ['style' => ''],
                'label' => false,
            ],
            ['attribute'=>'write_off_recovered',
                'value'=>function($data){return number_format($data->write_off_recovered);},
                'headerOptions' => ['style' => ''],
                'label' => false,
            ],
            ['attribute'=>'awp_active_loans',
                'value'=>function($data){return number_format($data->awp_active_loans);},
                'headerOptions' => ['style' => ''],
                'label' => false,
            ],
            /*['class' => 'yii\grid\ActionColumn'],*/
        ],
    ]); ?>
</div>