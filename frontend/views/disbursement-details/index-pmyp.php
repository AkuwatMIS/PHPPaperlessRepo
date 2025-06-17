<?php

use common\models\search\DisbursementDetailsSearch;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DisbursementDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Publised Loans Status';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Published Loans Status</h6>
<?php echo $this->render('_searchDisbursementDetailPmyp', ['model' => $searchModel, 'bank_names' => $bank_names,'branches_names' => $branches_names,            'regions' => $regions
]);?>
<?php if(!empty($dataProvider)){ ?>
<div class="disbursement-details-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns_pmyp.php'),
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
                   <form action="/disbursement-details/index-pmyp">
                     <input type="hidden" name="DisbursementDetailsSearch[sanction_no]" value="' . $searchModel->sanction_no . '">
                     <input type="hidden" name="DisbursementDetailsSearch[cnic]" value="' . $searchModel->cnic . '">
                     <input type="hidden" name="DisbursementDetailsSearch[payment_method_id]" value="' . $searchModel->payment_method_id . '">
                     <input type="hidden" name="DisbursementDetailsSearch[project_id]" value="' . $searchModel->project_id . '">
                     <input type="hidden" name="DisbursementDetailsSearch[branch_id]" value="' . $searchModel->branch_id . '">
                     <input type="hidden" name="DisbursementDetailsSearch[area_id]" value="' . $searchModel->area_id . '">
                     <input type="hidden" name="DisbursementDetailsSearch[region_id]" value="' . $searchModel->region_id . '">
                     <input type="hidden" name="DisbursementDetailsSearch[tranche_id]" value="' . $searchModel->tranche_id . '">
                     <input type="hidden" name="DisbursementDetailsSearch[bank_name]" value="' . $searchModel->bank_name . '">
                     <input type="hidden" name="DisbursementDetailsSearch[title]" value="' . $searchModel->title . '">
                     <input type="hidden" name="DisbursementDetailsSearch[account_no]" value="' . $searchModel->account_no . '">
                     <input type="hidden" name="DisbursementDetailsSearch[transferred_amount]" value="' . $searchModel->transferred_amount . '">
                     <input type="hidden" name="DisbursementDetailsSearch[date_disbursed]" value="' . $searchModel->date_disbursed . '">
                     <input type="hidden" name="DisbursementDetailsSearch[status]" value="' . $searchModel->status . '">
                      <input type="hidden" name="DisbursementDetailsSearch[response_description]" value="' . $searchModel->response_description . '">
                      <input type="hidden" name="DisbursementDetailsSearch[created_at]" value="' . $searchModel->created_at . '">
                      <input type="hidden" name="DisbursementDetailsSearch[activity_id]" value="' . $searchModel->activity_id . '">
                      <input type="hidden" name="DisbursementDetailsSearch[province_id]" value="' . $searchModel->province_id . '">
                      <input type="hidden" name="DisbursementDetailsSearch[district_id]" value="' . $searchModel->district_id . '">
                      <input type="hidden" name="DisbursementDetailsSearch[age]" value="' . $searchModel->age . '">
                      <input type="hidden" name="DisbursementDetailsSearch[gender]" value="' . $searchModel->gender . '">
                    
                     <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                        role="menuitem" tabindex="-1"><i
                            class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                    </button>
                </form>
                </li>
            </ul>
         </div>
               ',
        ])?>
    </div>
</div>
<?php }else{ ?>
    <hr>
    <h3>Select Region/Project and status First!</h3>
<?php } ?>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
