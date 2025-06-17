<?php

use dimmitri\grid\ExpandRowColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmergencyLoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Emergency Loans';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
           Emergency Loans
        </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'projects_name' =>$projects_name,
            'regions' =>$regions,
        ]); ?>

        <div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute' => 'region_id',
                'value'=>'loan.region.name',
                'label' => 'Region',
                'filter' => $regions,
            ],
            [
                'attribute' => 'area_id',
                'value'=>'loan.area.name',
                'label' => 'Area'
            ],
            [
                'attribute' => 'branch_id',
                'value'=>'loan.branch.name',
                'label' => 'Branch'
            ],
            [
                'attribute' => 'member_name',
                'value'=>'loan.application.member.full_name',
                'label' => 'Name'
            ],
            [
                'attribute'=>'member_parentage',
                'value'=>'loan.application.member.parentage',
                'label' => 'Parentage'
            ],
            [
                'attribute'=>'member_cnic',
                'value'=>'loan.application.member.cnic',
                'label' => 'CNIC'
            ],

            [
                'attribute'=>'sanction_no',
                'value'=> 'loan.sanction_no',
                'label' => 'Sanction No'
            ],
            [
                'attribute'=>'date_disbursed',
                //'value'=> 'loan.date_disbursed',
                'value'=>function ($model, $key, $index) {
                    if($model->loan->date_disbursed != 0) {
                        return \common\components\Helpers\StringHelper::dateFormatter($model->loan->date_disbursed);
                    }
                },
                'label' => 'Date Disbursed'
            ],
            [
                'attribute'=>'project_id',
                'value'=> 'loan.project.name',
                'label' => 'Project',
                'filter'=> $projects_name
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'template'=>'{delete}',
            ],
        ],
        'footerRowOptions' => ['style' => 'font-weight:bold;'],
        'showFooter' => true,
    ]); ?>
</div>
    </div></div>
