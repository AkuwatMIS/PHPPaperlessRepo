<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\export\ExportMenu;
use yii2tech\csvgrid\CsvGrid;
/* @var $this yii\web\View */
/* @var $searchModel common\models\RecoveriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Disbursement Summary';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$dataProvider->pagination->pageSize = 50;
ini_set('memory_limit','2G');
ini_set('max_execution_time', 300);

?>
<?php echo $this->render('_search_project_wise', [
    'model' => $searchModel,
    'months'=>$months
]); ?>
<div class="recoveries-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'pjax'=>true,
            'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container']],
            'columns' => require(__DIR__.'/_columns_awp_project_wise.php'),
            'toolbar'=> [
                //$fullExportMenu,
                '{export}',
                ['content'=>
                    /*Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                        ['role'=>'modal-remote','title'=> 'Create new Activities','class'=>'btn btn-default']).*/
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                        ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                    '{toggleData}'
                ],
            ],

            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'success',
                'heading' => '<i class="glyphicon glyphicon-list"></i> Awp Project Wise Report-('.$date.')',
            ],
            'pager' => [
                'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
                'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
                'nextPageLabel' => 'Next',   // Set the label for the "next" page button
                'firstPageLabel'=>'First',   // Set the label for the "first" page button
                'lastPageLabel'=>'Last',    // Set the label for the "last" page button
                'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
                'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
                'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
                'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
                'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
            ],
            'export'=>[
                'target'=>GridView::TARGET_BLANK
            ],
            'showFooter' => true,
        ])?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
