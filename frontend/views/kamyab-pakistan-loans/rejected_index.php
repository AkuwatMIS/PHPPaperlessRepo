<?php

use common\models\search\nadra\RejectedNadraVerisysSearch;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\VigaLoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'NADRA Verisys Rejected List';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);

?>
<div class="col-sm-12">
    <div class="col-sm-12">
        <div class="col-md-2"></div>
        <div class="col-md-8">
            <?php if (Yii::$app->session->hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <h4><i class="icon fa fa-check"></i>Remarks</h4>
                    <?= Yii::$app->session->getFlash('success') ?>
                </div>
            <?php endif; ?>
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                    <h4><i class="icon fa fa-check"></i>Saved!</h4>
                    <?= Yii::$app->session->getFlash('error') ?>
                </div>
            <?php endif; ?>
        </div>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-list"></span>
            NADRA Verisys Rejected List </h6>
        <?php  echo $this->render('_rejected_search', [
            'model' => $searchModel,
            'regions' => $regions,
            'projects' => $projects
            ]); ?>
        <?php if(!empty($dataProvider)) {?>
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
//            'filterModel' => $searchModel,
            'columns' => require(__DIR__ . '/_rejected_columns.php'),
            'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                <form action="/kamyab-pakistan-loans/rejected-nic-list">
                               <input type="hidden" name="RejectedNadraVerisysSearch[region_id]" value="' . $searchModel->region_id . '">
                                <input type="hidden" name="RejectedNadraVerisysSearch[area_id]" value="' . $searchModel->area_id . '">
                                <input type="hidden" name="RejectedNadraVerisysSearch[branch_id]" value="' . $searchModel->branch_id . '">
                                <input type="hidden" name="RejectedNadraVerisysSearch[fullname]" value="' . $searchModel->fullname . '">
                                  <input type="hidden" name="RejectedNadraVerisysSearch[parentage]" value="' . $searchModel->parentage . '">
                                   <input type="hidden" name="RejectedNadraVerisysSearch[cnic]" value="' . $searchModel->cnic . '">
                                    <input type="hidden" name="RejectedNadraVerisysSearch[cnic_issue_date]" value="' . $searchModel->cnic_issue_date . '">
                                     <input type="hidden" name="RejectedNadraVerisysSearch[cnic_expire_date]" value="' . $searchModel->cnic_expiry_date . '">
                                <input type="hidden" name="RejectedNadraVerisysSearch[reject_reason]" value="' . $searchModel->reject_reason . '">
                                <input type="hidden" name="RejectedNadraVerisysSearch[remarks]" value="' . $searchModel->remarks . '">
                                <input type="hidden" name="RejectedNadraVerisysSearch[rejected_date]" value="' . $searchModel->rejected_date . '">
                                <input type="hidden" name="RejectedNadraVerisysSearch[status]" value="' . $searchModel->status . '">
                                
                              
                        <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                                role="menuitem" tabindex="-1"><i
                                    class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                        </button>

                    </form>
                            </li>
                        </ul>
                     </div>
                              ',
            'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
        ]) ?>
    </div>
    <?php } else{ ?>
        <div class="table-responsive">
            <hr>
            <h3>Search through above filters! Application Date Required</h3>
        </div>
    <?php } ?>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
