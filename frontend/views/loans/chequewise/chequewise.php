<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Chequewise-Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Chequewise Report</h6>
        <?php
        echo $this->render('_search_chequewise', [
            'model' => $data['searchModel'],
            'regions' => $data['regions'],
            'branches' => $data['branches'],
            'projects' => $data['projects'],
            'inst_types' => $data['inst_types'],
        ]);

        ?>
        <?php if(!empty($data['dataProvider'])){?>
        <div class="loans-index">
            <div id="ajaxCrudDatatable">
                <?php
                /*echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => require(__DIR__.'/_columns_chequewise.php'),
                   // ExportMenu::stripHtml=>true,
                    'fontAwesome' => true,
                    'showColumnSelector'=>true,
                    'exportConfig' => [
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_PDF => false,
                        ExportMenu::FORMAT_EXCEL => false,
                        ExportMenu::FORMAT_HTML => false,
                        ExportMenu::FORMAT_EXCEL_X=> false,


                    ],
                    'filename'=>'Chequewise Report',

                    'stream' => false, // this will automatically save file to a folder on web server
                ]);*/
                //  echo      \kartik\dynagrid\DynaGrid::widget([
                echo GridView::widget([
                    //'id'=>'crud-datatable',
                    'columns' => require(__DIR__ . '/_columns_chequewise.php'),
                    'dataProvider' => $data['dataProvider'],
                    'filterModel' => $data['searchModel'],
                    'footerRowOptions'=>['style'=>'font-weight:bold;'],
                    'showFooter' => true,
                    'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                <form action="/loans/chequewisereport">
                        <input type="hidden" name="LoansSearch[region_id]" value="'.$data['searchModel']->region_id.'">
                        <input type="hidden" name="LoansSearch[area_id]" value="'. $data['searchModel']->area_id.'">
                        <input type="hidden" name="LoansSearch[branch_id]" value="'.$data['searchModel']->branch_id.'">
                        <input type="hidden" name="LoansSearch[date_disbursed]" value="'.$data['searchModel']->date_disbursed.'">
                        <input type="hidden" name="LoansSearch[cheque_no]" value="'.$data['searchModel']->cheque_no .'">
                        <input type="hidden" name="LoansSearch[sanction_no]" value="'.$data['searchModel']->sanction_no.'">
                        <input type="hidden" name="LoansSearch[loan_amount]" value="'.$data['searchModel']->loan_amount.'">
                        <input type="hidden" name="LoansSearch[tranch_amount]" value="'.$data['searchModel']->tranch_amount.'">
                        <input type="hidden" name="LoansSearch[tranch_no]" value="'.$data['searchModel']->tranch_no.'">
                        <input type="hidden" name="LoansSearch[group_no]" value="'.$data['searchModel']->group_no.'">
                        <input type="hidden" name="LoansSearch[member_name]" value="'.$data['searchModel']->member_name.'">
                        <input type="hidden" name="LoansSearch[member_cnic]" value="'.$data['searchModel']->member_cnic.'">
                        <input type="hidden" name="LoansSearch[member_parentage]" value="'. $data['searchModel']->member_parentage.'">
                        <input type="hidden" name="LoansSearch[project_id]" value="'.$data['searchModel']->project_id.'">
                        <input type="hidden" name="LoansSearch[inst_type]" value="'.$data['searchModel']->inst_type.'">
                        <input type="hidden" name="LoansSearch[inst_months]" value="'.$data['searchModel']->inst_months.'">
                        <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                                role="menuitem" tabindex="-1"><i
                                    class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                        </button>

                    </form>
                            </li>
                        </ul>
                     </div>
                              ',
                    'pager' => [
                        'options' => ['class' => 'pagination'],   // set clas name used in ui list of pagination
                        'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
                        'nextPageLabel' => 'Next',   // Set the label for the "next" page button
                        'firstPageLabel' => 'First',   // Set the label for the "first" page button
                        'lastPageLabel' => 'Last',    // Set the label for the "last" page button
                        'nextPageCssClass' => 'next',    // Set CSS class for the "next" page button
                        'prevPageCssClass' => 'prev',    // Set CSS class for the "previous" page button
                        'firstPageCssClass' => 'first',    // Set CSS class for the "first" page button
                        'lastPageCssClass' => 'last',    // Set CSS class for the "last" page button
                        'maxButtonCount' => 10,    // Set maximum number of page buttons that can be displayed


                        //  'options'=>['id'=>'dynagrid-1'] ,
                    ],
                    'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                    ])
                ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>
        <?php Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => '',// always need it for jquery plugin

        ]) ?>
        <?php Modal::end(); ?>
