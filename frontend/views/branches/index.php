<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BranchesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Branches';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
            Branches List </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions' => $array['regions'],
        ]); ?>
        <div class="table-responsive">
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
                                <form action="/branches/index">
                                <input type="hidden" name="BranchesSearch[name]" value="' . $searchModel->name . '">
                                <input type="hidden" name="BranchesSearch[type]" value="' . $searchModel->type . '">
                                <input type="hidden" name="BranchesSearch[short_name]" value="' . $searchModel->short_name . '">
                                <input type="hidden" name="BranchesSearch[region_id]" value="' . $searchModel->region_id . '">
                                <input type="hidden" name="BranchesSearch[area_id]" value="' . $searchModel->area_id . '">        
                                <input type="hidden" name="BranchesSearch[city_id]" value="' . $searchModel->city_id . '">
                                <input type="hidden" name="BranchesSearch[district_id]" value="' . $searchModel->district_id . '">
                                <input type="hidden" name="BranchesSearch[division_id]" value="' . $searchModel->division_id . '">
                                <input type="hidden" name="BranchesSearch[province_id]" value="' . $searchModel->province_id . '">
                                <input type="hidden" name="BranchesSearch[country_id]" value="' . $searchModel->country_id . '">
                                <input type="hidden" name="BranchesSearch[status]" value="' . $searchModel->status . '">
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
    </div>
</div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
