<?php

use dimmitri\grid\ExpandRowColumn;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\EmergencyLoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'CIB Report';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
           Application CIB Listing
        </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions' =>$regions,
        ]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'member_cnic',
                'value'=>'application.member.cnic',
                'label'=>'CNIC',
            ],
            [
                'attribute'=>'region_id',
                'value'=>'application.region.name',
                'label'=>'Region',
                'filter' => $regions,
            ],
            [
                'attribute'=>'area_id',
                'value'=>'application.area.name',
                'label'=>'Area',
            ],
            [
                'attribute'=>'branch_id',
                'value'=>'application.branch.name',
                'label'=>'Branch',
            ],
            [
                'attribute'=>'cib_type_id',
                'value'=> function ($data, $key, $index) {
                        return \common\components\Helpers\StructureHelper::getCIBType($data->cib_type_id);
                },
                'label'=>'CIB Type',
                'filter' => [1 => 'Tasdeek',2=>'Data Check']
            ],
            /*[
                'class'=>'\kartik\grid\DataColumn',
                'attribute'=>'fee',
            ],*/
            [
                'attribute'=>'receipt_no',
            ],

           // ['class' => 'yii\grid\ActionColumn'],
        ],
        'footerRowOptions' => ['style' => 'font-weight:bold;'],
        'showFooter' => true,
        'summary' => '
         Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
         <div class="dropdown pull-right">
            <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <i class="glyphicon glyphicon-export"></i>
            <span class="caret"></span></button>
                        
             <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation" >
                   <form action="/applications-cib/index">
                   
                     <input type="hidden" name="ApplicationsCibSearch[cnic]" value="' . $searchModel->member_cnic . '">
                     <input type="hidden" name="ApplicationsCibSearch[region]" value="' . $searchModel->region_id. '">
                     <input type="hidden" name="ApplicationsCibSearch[area]" value="' . $searchModel->area_id. '">
                     <input type="hidden" name="ApplicationsCibSearch[branch]" value="' . $searchModel->branch_id. '">
                     <input type="hidden" name="ApplicationsCibSearch[cib_type_it]" value="' . $searchModel->cib_type_id. '">
                     <input type="hidden" name="ApplicationsCibSearch[receipt_no]" value="' . $searchModel->receipt_no. '">
                    <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                        role="menuitem" tabindex="-1"><i
                            class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                    </button>
                </form>
                </li>
            </ul>
         </div>
               ',
    ]); ?>
    </div>
</div>
