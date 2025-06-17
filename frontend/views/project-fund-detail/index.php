<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProjectFundDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Batches Detail';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
    <?php echo $this->render('_search', ['model' => $searchModel, 'bank_names' => $bank_names,'projects'=>$projects]);?>

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4><i class="icon fa fa-check"></i>Saved!</h4>
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

<div class="project-fund-detail-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
//            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                <form action="/project-fund-detail/index">
                       
                                            <input type="hidden" name="ProjectFundDetailSearch[batch_no]" value="' . $searchModel->batch_no . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[name]" value="' . $searchModel->name . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[disbursement_source]" value="' . $searchModel->disbursement_source . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[project_id]" value="' . $searchModel->project_id . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[no_of_loans]" value="' . $searchModel->no_of_loans . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[fund_batch_amount]" value="' . $searchModel->fund_batch_amount . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[allocation_date]" value="' . $searchModel->allocation_date . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[txn_mode]" value="' . $searchModel->txn_mode . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[txn_no]" value="' . $searchModel->txn_no . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[received_at]" value="' . $searchModel->received_at . '">
                                            <input type="hidden" name="ProjectFundDetailSearch[status]" value="' . $searchModel->status . '">
                                          
                        <button title="Export to CSV" type="submit" name="export_summary" value="export_summary" class="btn btn-default col-sm-12 btn-sm"
                                role="menuitem" tabindex="-1"><i
                                    class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                        </button>

                    </form>
                            </li>
                        </ul>
                     </div>
                              '

        ])?>
    </div>
</div></div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>


<?php $this->registerJs("
$('#ajaxCrudModal').on('hidden.bs.modal', function () {
location.reload();
})
");
?>
