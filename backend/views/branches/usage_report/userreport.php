<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usage-Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h3><span class="fa fa-list"></span> Usage Report</h3>
        <?php
        echo $this->render('_search_userreport', [
            'model' => $searchModel,
            'regions' => $regions,
            'areas'=>$areas,
            'branches'=>$branches,
        ]);

        ?>
<br>
        <br>

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
                    'columns' => require(__DIR__ . '/_columns_userreport.php'),
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
                             <form action="/branches/usage-report">
                       
                        <input type="hidden" name="BranchesSearch[region_id]" value="'.$searchModel->region_id.'">
                        <input type="hidden" name="BranchesSearch[area_id]" value="'. $searchModel->area_id.'">
                        <input type="hidden" name="BranchesSearch[id]" value="'.$searchModel->id.'">
                        <input type="hidden" name="BranchesSearch[no_of_members]" value="'.$searchModel->no_of_members .'">
                        <input type="hidden" name="BranchesSearch[no_of_applications]" value="'.$searchModel->no_of_applications.'">
                        <input type="hidden" name="BranchesSearch[no_of_social_appraisals]" value="'.$searchModel->no_of_social_appraisals.'">
                        <input type="hidden" name="BranchesSearch[no_of_business_appraisals]" value="'.$searchModel->no_of_business_appraisals.'">
                        <input type="hidden" name="BranchesSearch[no_of_verifications]" value="'.$searchModel->no_of_verifications.'">
                        <input type="hidden" name="BranchesSearch[no_of_groups]" value="'.$searchModel->no_of_groups.'">
                        <input type="hidden" name="BranchesSearch[no_of_loans]" value="'. $searchModel->no_of_loans.'">
                        <input type="hidden" name="BranchesSearch[no_of_fund_requests]" value="'.$searchModel->no_of_fund_requests.'">
                        <input type="hidden" name="BranchesSearch[no_of_disbursements]" value="'.$searchModel->no_of_disbursements.'">
                        <input type="hidden" name="BranchesSearch[no_of_recoveries]" value="'.$searchModel->no_of_recoveries.'">
                        <input type="hidden" name="BranchesSearch[report_date]" value="'.$searchModel->report_date.'">
                        <input type="hidden" name="BranchesSearch[platform]" value="'.$searchModel->platform.'">
                        <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                                role="menuitem" tabindex="-1"><i
                                    class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                        </button>
                    </form>
                            </li>
                        </ul>
                     </div>
                                          <br><br>

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
      <!--  </div>-->

        <?php Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => '',// always need it for jquery plugin

        ]) ?>
        <?php Modal::end(); ?>
