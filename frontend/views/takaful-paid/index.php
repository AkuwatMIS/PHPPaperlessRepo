<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\StepsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Takaful Paid';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
    <div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
            Takaful Paid </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions'=>$regions,
            'projects'=>$projects,
        ]); ?>
        <div>
            <div id="ajaxCrudDatatable">
                <?php if(!empty($dataProvider)){?>
                    <?=GridView::widget([
                        'id'=>'crud-datatable',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax'=>true,
                        'columns' => require(__DIR__.'/_columns.php'),
                        'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                <form action="/takaful-paid/index">
                                    <input type="hidden" name="TakafulPaidSearch[full_name]" value="' . $searchModel->full_name . '">
                                    <input type="hidden" name="TakafulPaidSearch[cnic]" value="' . $searchModel->cnic . '">
                                    <input type="hidden" name="TakafulPaidSearch[region_id]" value="' . $searchModel->region_id . '">
                                    <input type="hidden" name="TakafulPaidSearch[area_id]" value="' . $searchModel->area_id . '">
                                    <input type="hidden" name="TakafulPaidSearch[branch_id]" value="' . $searchModel->branch_id . '">
                                      <input type="hidden" name="TakafulPaidSearch[project_id]" value="' . $searchModel->project_id . '">
                                      <input type="hidden" name="TakafulPaidSearch[sanction_no]" value="' . $searchModel->sanction_no . '">
                                      <input type="hidden" name="TakafulPaidSearch[receipt_no]" value="' . $searchModel->receipt_no . '">
                                      <input type="hidden" name="TakafulPaidSearch[receive_date]" value="' . $searchModel->receive_date . '">
                                       <input type="hidden" name="TakafulPaidSearch[loan_amount]" value="' . $searchModel->receive_date . '">
                                      <input type="hidden" name="TakafulPaidSearch[credit]" value="' . $searchModel->credit . '">
                                   
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
                    ]); ?>


                <?php  }?>
            </div>
        </div>
    </div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>