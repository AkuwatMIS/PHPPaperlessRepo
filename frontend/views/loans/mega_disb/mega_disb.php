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

$this->title = 'Mega Disbursement-Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Mega Disbursement Report</h6>
        <?php
        echo $this->render('_search_mega_disb', [
            'model' => $searchModel,
            'regions' => $regions,
            'branches' => $branches,
            'projects' => $projects,
        ]);

        ?>
        <?php if(!empty($dataProvider)){?>
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
                    'columns' => require(__DIR__ . '/_columns_mega_disb.php'),
                    'dataProvider' => $dataProvider,
                    //'filterModel' => $searchModel,
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
                                <form action="/loans/mega-disbursement-report">
                        <input type="hidden" name="LoansSearch[region_id]" value="'.$searchModel->region_id.'">
                        <input type="hidden" name="LoansSearch[area_id]" value="'. $searchModel->area_id.'">
                        <input type="hidden" name="LoansSearch[branch_id]" value="'.$searchModel->branch_id.'">
                        <input type="hidden" name="LoansSearch[date_disbursed]" value="'.$searchModel->date_disbursed.'">
                        <input type="hidden" name="LoansSearch[cheque_no]" value="'.$searchModel->cheque_no .'">
                        <input type="hidden" name="LoansSearch[sanction_no]" value="'.$searchModel->sanction_no.'">
                        <input type="hidden" name="LoansSearch[loan_amount]" value="'.$searchModel->loan_amount.'">
                        <input type="hidden" name="LoansSearch[group_no]" value="'.$searchModel->group_no.'">
                        <input type="hidden" name="LoansSearch[member_name]" value="'.$searchModel->member_name.'">
                        <input type="hidden" name="LoansSearch[member_cnic]" value="'.$searchModel->member_cnic.'">
                        <input type="hidden" name="LoansSearch[member_parentage]" value="'. $searchModel->member_parentage.'">
                        <input type="hidden" name="LoansSearch[project_id]" value="'.$searchModel->project_id.'">
                        <input type="hidden" name="LoansSearch[inst_type]" value="'.$searchModel->inst_type.'">
                        <input type="hidden" name="LoansSearch[inst_months]" value="'.$searchModel->inst_months.'">
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
