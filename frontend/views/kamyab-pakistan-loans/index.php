<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\VigaLoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'NADRA Verisys Detail Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="row">
    <div class="col-sm-12">
    <div class="col-sm-12">
        <div class="col-sm-12">
            <div class="col-md-12">
                    <?php if (Yii::$app->session->hasFlash('success')): ?>
                        <div class="alert alert-success alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-check"></i>success!</h4>
                                <?= Yii::$app->session->getFlash('success') ?>
                        </div>
                    <?php endif; ?>
                    <?php if (Yii::$app->session->hasFlash('error')): ?>
                        <div class="alert alert-danger alert-dismissable">
                            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                            <h4><i class="icon fa fa-check"></i>error!</h4>
                                <?= Yii::$app->session->getFlash('error') ?>
                        </div>
                    <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">
                <a class="pull-right btn btn-primary" href="/kamyab-pakistan-loans/summary">NADRA Verisys
                    Summary Report</a>
                <h6 class="address-heading"><span class="glyphicon glyphicon-list"></span>
                    NADRA Verisys Detail Report </h6>
                    <?php echo $this->render('_search', [
                            'model' => $searchModel,
                            'regions' => $regions,
                            'projects' => $projects,
                    ]); ?>
                    <?php if (!empty($dataProvider)) { ?>
                    <?= GridView::widget([
                            'id' => 'crud-datatable',
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => require(__DIR__ . '/_columns.php'),
                            'summary' => '
                 Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                 <div class="dropdown pull-right">
                    <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-export"></i>
                    <span class="caret"></span></button>
                    
                    <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                        <li role="presentation" >
                            <form action="/kamyab-pakistan-loans/index">
                            <input type="hidden" name="KamyabPakistanSearch[full_name]" value="' . $searchModel->member->full_name . '">
                            <input type="hidden" name="KamyabPakistanSearch[parentage]" value="' . $searchModel->member->parentage . '">
                            <input type="hidden" name="KamyabPakistanSearch[cnic]" value="' . $searchModel->cnic . '">
                            <input type="hidden" name="KamyabPakistanSearch[cnic_issue_date]" value="' . $searchModel->member->info->cnic_issue_date . '">
                            <input type="hidden" name="KamyabPakistanSearch[cnic_expiry_date]" value="' . $searchModel->member->info->cnic_expiry_date. '">    
                            <input type="hidden" name="KamyabPakistanSearch[area_id]" value="' . $searchModel->area_id . '">
                            <input type="hidden" name="KamyabPakistanSearch[region_id]" value="' . $searchModel->region_id . '">
                            <input type="hidden" name="KamyabPakistanSearch[branch_id]" value="' . $searchModel->branch_id . '">
                            <input type="hidden" name="KamyabPakistanSearch[project_id]" value="' . $searchModel->project_id . '">
                            <input type="hidden" name="KamyabPakistanSearch[application_date]" value="' . $searchModel->application_date . '">
                            <input type="hidden" name="KamyabPakistanSearch[created_at]" value="' . $searchModel->created_at . '">
                            <input type="hidden" name="KamyabPakistanSearch[status]" value="' . $searchModel->status . '">
                            <input type="hidden" name="KamyabPakistanSearch[nadra_verisys_status]" value="' . $searchModel->nadra_verisys_status . '">
                          
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
                <?php } else { ?>
                    <div class="table-responsive">
                        <hr>
                        <h3>Search through above filters! Application Date Required</h3>
                    </div>
                <?php } ?>
        </div>
    </div>
    </div>
</div>
<?php Modal::begin([
        "id" => "ajaxCrudModal",
        "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
