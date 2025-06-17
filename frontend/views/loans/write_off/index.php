<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Write-Off-Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Write-Off Report</h6>
        <?php  echo $this->render('_search_write_off', ['model' => $searchModel, 'projects' =>$projects,'regions' => $regions, 'activities' => $activities]); ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,

            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute'=>'member_name',
                    'value'=>'member_name',
                    'label'=>'Name',
                ],
                [
                    'attribute'=>'member_parentage',
                    'value'=>'member_parentage',
                    'label'=>'Parentage',
                ],
                [
                    'attribute'=>'member_cnic',
                    'value'=>'member_cnic',
                    'label'=>'CNIC',
                ],
                [
                    'attribute'=>'sanction_no',
                    'value'=>'sanction_no',
                    'label'=>'Sanction No',
                ],
                [
                    'attribute'=>'region_id',
                    'value'=>'region_name',
                    'label'=>'Region',
                    'filter'=> $regions

                ],
                [
                    'attribute'=>'area_id',
                    'value'=>'area_name',
                    'label'=>'Area',
                    'filter' =>  \yii\helpers\ArrayHelper::map(\common\models\Areas::find()->asArray()->all(), 'id', 'name'),

                ],
                [
                    'attribute'=>'branch_id',
                    'value'=>'branch_name',
                    'label'=>'Branch',
                    'filter'=> \yii\helpers\ArrayHelper::map(\common\models\Branches::find()->asArray()->all(), 'id', 'name'),

                ],
                [
                    'attribute'=>'project_id',
                    'value'=>'project_name',
                    'label'=>'Project',
                    'filter'=> $projects
                ],
                [
                    'attribute'=>'activity_id',
                    'value'=>'activity_name',
                    'label'=>'Purpose',
                    'filter'=> $activities

                ],
                [
                    'attribute'=>'date_disbursed',
                    'value' => function ($data, $key, $index) {
                        return date("d-M-y", $data->date_disbursed);
                    },
                ],
                [
                    'value'=>function($data){return number_format($data->loan_amount);},
                    'label'=>'Loan Amount',
                ],
                [
                    'attribute'=>'mobile',
                    'value'=> 'mobile',
                    'label'=>'Mobile',

                ],
                [
                    'attribute'=>'write_off_amount',
                    'value'=>'write_off_amount',
                    'label'=>'Write Off Amount',
                ],
                [
                    'attribute'=>'write_off_date',
                    'value'=>function ($data, $key, $index) {
                    return date("d-M-y", $data->write_off_date);
                    },
                    'label'=>'Write Off Date',
                ],
                [
                    'attribute'=>'write_off_by',
                    'value' => 'write_off_by',
                    'label'=>'Write Off By',
                ],
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
                   <form action="/loans/writeoff">
                     <input type="hidden" name="LoansSearch[member_name]" value="' . $searchModel->member_name . '">
                     <input type="hidden" name="LoansSearch[member_parentage]" value="' . $searchModel->member_parentage . '">
                     <input type="hidden" name="LoansSearch[member_cnic]" value="' . $searchModel->member_cnic . '">
                     <input type="hidden" name="LoansSearch[sanction_no]" value="' . $searchModel->sanction_no . '">
                     <input type="hidden" name="LoansSearch[region_id]" value="' . $searchModel->region_id . '">
                     <input type="hidden" name="LoansSearch[area_id]" value="' . $searchModel->area_id . '">
                     <input type="hidden" name="LoansSearch[branch_id]" value="' . $searchModel->branch_id . '">
                     <input type="hidden" name="LoansSearch[project_id]" value="' . $searchModel->project_id . '">                   
                     <input type="hidden" name="LoansSearch[activity_id]" value="' . $searchModel->activity_id . '">
                     <input type="hidden" name="LoansSearch[date_disbursed]" value="' . $searchModel->date_disbursed . '">
                     <input type="hidden" name="LoansSearch[loan_amount]" value="' . $searchModel->loan_amount . '">
                     <input type="hidden" name="LoansSearch[mobile]" value="' . $searchModel->mobile . '">
                     <input type="hidden" name="LoansSearch[write_off_amount]" value="' . $searchModel->write_off_amount . '">
                     <input type="hidden" name="LoansSearch[write_off_by]" value="' . $searchModel->write_off_by . '">
                     <input type="hidden" name="LoansSearch[write_off_date]" value="' . $searchModel->write_off_date . '">                     
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

