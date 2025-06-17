<?php

use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Referral Report';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);

?>
    <div class="container-fluid">
        <div class="box-typical box-typical-padding">
            <h6 class="address-heading"><span class="fa fa-list"></span> Referral Report</h6>

    <?php  echo $this->render('_search_referral_report', ['model' => $searchModel,'regions' => $regions,'projects' => $projects]); ?>

    <?= GridView::widget([
        'id'=>'crud-datatable',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax'=>true,

        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'member_name',
                'value'=>'application.member.full_name',
                'label'=>'Name',
            ],
            [
                'attribute'=>'member_parentage',
                'value'=>'application.member.parentage',
                'label'=>'Parentage',
            ],
            [
                'attribute'=>'member_cnic',
                'value'=>'application.member.cnic',
                'label'=>'CNIC',
            ],
            [
               // 'attribute'=>'loan_amount',
                'value'=>function($data){return number_format($data->loan_amount);},
                'label'=>'Loan Amount',
            ],
            [
                'attribute'=>'application_no',
                'value'=>'application.application_no'
            ],
            [
                'attribute'=>'region_id',
                'value'=>'region.name',
                'label'=>'Region',
                'filter'=>$regions
            ],
            [
                'attribute'=>'project_id',
                'value'=>'project.name',
                'label'=>'Project',
                'filter'=>$projects
            ],
            [
                'attribute'=>'area_id',
                'value'=>'area.name',
                'label'=>'Area',
                'filter'=>\yii\helpers\ArrayHelper::map(\common\models\Areas::find()->asArray()->all(), 'id', 'name'),
            ],
            [
                'attribute'=>'branch_id',
                'value'=>'branch.name',
                'label'=>'Branch',
                'filter'=>\yii\helpers\ArrayHelper::map(\common\models\Branches::find()->asArray()->all(), 'id', 'name'),
            ],
            [
                'attribute'=>'date_disbursed',
                'value'=>function ($data) {return date('d M y',$data->date_disbursed);},
                'label'=>'Date Disbursed'
            ],
            [
                'attribute'=>'referral_id',
                'value'=>'application.referral.name',
                'label'=>'Referred By',
                'filter'=>\common\components\Helpers\ApplicationHelper::getReferredByList(),
            ],
        ],

        'footerRowOptions' => ['style' => 'font-weight:bold;'],
        'showFooter' => true,
        'toolbar'=> [
                ['content'=>
             Html::a('<i class="glyphicon glyphicon-plus"></i>', ['fileimport'],
                    ['title'=> 'Import File','class'=>'btn btn-default'])
                    ],],
        'summary' => '
         Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
         <div class="dropdown pull-right">
            <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <i class="glyphicon glyphicon-export"></i>
            <span class="caret"></span></button>
                        
             <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation" >
                   <form action="/loans/referral-report">
                     <input type="hidden" name="LoansSearch[member_name]" value="' . $searchModel->member_name . '">
                     <input type="hidden" name="LoansSearch[member_parentage]" value="' . $searchModel->member_parentage . '">
                     <input type="hidden" name="LoansSearch[member_cnic]" value="' . $searchModel->member_cnic . '">
                     <input type="hidden" name="LoansSearch[application_no]" value="' . $searchModel->application_no . '">
                     <input type="hidden" name="LoansSearch[region_id]" value="' . $searchModel->region_id . '">
                     <input type="hidden" name="LoansSearch[area_id]" value="' . $searchModel->area_id . '">
                     <input type="hidden" name="LoansSearch[branch_id]" value="' . $searchModel->branch_id . '">
                     <input type="hidden" name="LoansSearch[project_id]" value="' . $searchModel->project_id . '">
                     <input type="hidden" name="LoansSearch[date_disbursed]" value="' . $searchModel->date_disbursed . '">
                     <input type="hidden" name="LoansSearch[referral_id]" value="' . $searchModel->referral_id . '">
                     
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

        </div></div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
