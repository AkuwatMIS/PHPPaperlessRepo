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

$this->title = 'Application Steps';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
            Verification CheckList </h6>
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
                                <form action="/steps/index">
                                    <input type="hidden" name="StepsSearch[full_name]" value="' . $searchModel->full_name . '">
                                    <input type="hidden" name="StepsSearch[cnic]" value="' . $searchModel->cnic . '">
                                    <input type="hidden" name="StepsSearch[region_id]" value="' . $searchModel->region_id . '">
                                    <input type="hidden" name="StepsSearch[area_id]" value="' . $searchModel->area_id . '">
                                    <input type="hidden" name="StepsSearch[branch_id]" value="' . $searchModel->branch_id . '">
                                    <input type="hidden" name="StepsSearch[application_date]" value="' . $searchModel->application_date . '">
                                    <input type="hidden" name="StepsSearch[created_at]" value="' . $searchModel->created_at . '">                                             <input type="hidden" name="StepsSearch[project_id]" value="' . $searchModel->project_id . '">
                                    <input type="hidden" name="StepsSearch[cibstatus]" value="' . $searchModel->cibstatus . '">
                                    <input type="hidden" name="StepsSearch[Nadra]" value="' . $searchModel->Nadra . '">
                                    <input type="hidden" name="StepsSearch[PMT]" value="' . $searchModel->PMT . '">
                                    <input type="hidden" name="StepsSearch[status]" value="' . $searchModel->status . '">
                                    <input type="hidden" name="StepsSearch[Account_Verification]" value="' . $searchModel->Account_Verification . '">
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
