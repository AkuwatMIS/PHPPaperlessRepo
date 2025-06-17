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

$this->title = 'OverDueList-Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span>Charges Overdue Report</h6>
        <?php
        echo $this->render('_search_charges_overduelist', [
            'model' => $data['searchModel'],
            'regions' => $data['regions'],
            'branches' => $data['branches'],
            'projects' => $data['projects'],
            'provinces' => $data['provinces'],

        ]);

        ?>
        <?php if (!empty($data['dataProvider'])) { ?>
            <div class="loans-index">
                <div id="ajaxCrudDatatable">
                    <?php
                    echo \yii\grid\GridView::widget([
                        'columns' => require(__DIR__ . '/_columns_charges_overduelist.php'),
                        'dataProvider' => $data['dataProvider'],
                        'filterModel' => $data['searchModel'],
                        'footerRowOptions' => ['style' => 'font-weight:bold;'],
                        'showFooter' => true,


                        'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong> {totalCount} </strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                <form action="/loans/overdue-charges-list">
                       <input type="hidden" name="DuelistSearch[sanction_no]" value="' . $data['searchModel']->sanction_no . '">
                                             <input type="hidden" name="OverduelistSearch[sanction_no]" value="' . $data['searchModel']->sanction_no . '">
                                            <input type="hidden" name="OverduelistSearch[name]" value="' . $data['searchModel']->name . '">
                                            <input type="hidden" name="OverduelistSearch[parentage]" value="' . $data['searchModel']->parentage . '">
                                            <input type="hidden" name="OverduelistSearch[date_disbursed]" value="' . $data['searchModel']->date_disbursed . '">
                                            <input type="hidden" name="OverduelistSearch[loan_amount]" value="' . $data['searchModel']->loan_amount . '">
                                            <input type="hidden" name="OverduelistSearch[overdue_amount]" value="' . $data['searchModel']->overdue_amount . '">
                                            <input type="hidden" name="OverduelistSearch[outstanding_balance]" value="' . $data['searchModel']->outstanding_balance . '">
                                            <input type="hidden" name="OverduelistSearch[region_id]" value="' . $data['searchModel']->region_id . '">
                                            <input type="hidden" name="OverduelistSearch[area_id]" value="' . $data['searchModel']->area_id . '">
                                            <input type="hidden" name="OverduelistSearch[branch_id]" value="' . $data['searchModel']->branch_id . '">
                                            <input type="hidden" name="OverduelistSearch[province_id]" value="' . $data['searchModel']->province_id . '">
                                            <input type="hidden" name="OverduelistSearch[division_id]" value="' . $data['searchModel']->division_id . '">
                                            <input type="hidden" name="OverduelistSearch[district_id]" value="' . $data['searchModel']->district_id . '">
                                            <input type="hidden" name="OverduelistSearch[city_id]" value="' . $data['searchModel']->city_id . '">
                                            <input type="hidden" name="OverduelistSearch[report_date]" value="' . $data['searchModel']->report_date . '">
                                            <input type="hidden" name="OverduelistSearch[project_id]" value="' . $data['searchModel']->project_id . '">
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
                        ]])
                    ?>
                </div>
            </div>
        <?php } ?>
        <?php Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => '',// always need it for jquery plugin

        ]) ?>
        <?php Modal::end(); ?>
