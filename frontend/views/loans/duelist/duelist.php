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

$this->title = 'DueList-Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> DueList Report</h6>
        <?php
        echo $this->render('_search_duelist', [
            'model' => $data['searchModel'],
            'regions' => $data['regions'],
            'branches' => $data['branches'],
            'projects' => $data['projects'],
            'teams' => $data['teams'],
            'provinces' => $data['provinces'],
        ]);

        ?>
        <div class="container">
            <div class="row">
                <div class="col-md-12"></div>
            </div>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Total Loans</th>
                <th>Male Loans</th>
                <th>Female Loans</th>
                <th>Active Loans</th>
                <th>Cum. Disb</th>
                <th>Cum. Due</th>
                <th>Cum. Recv</th>
                <th>Not Yet Due</th>
                <th>OLP</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?= isset($data['progress'][0]['no_of_loans']) ? number_format($data['progress'][0]['no_of_loans']) : '0' ?></td>
                <td><?= isset($data['progress'][0]['family_loans']) ? number_format($data['progress'][0]['family_loans']) : '0' ?></td>
                <td><?= isset($data['progress'][0]['female_loans']) ? number_format($data['progress'][0]['female_loans']) : '0' ?></td>
                <td><?= isset($data['progress'][0]['active_loans']) ? number_format($data['progress'][0]['active_loans']) : '0' ?></td>
                <td><?= isset($data['progress'][0]['cum_disb']) ? number_format($data['progress'][0]['cum_disb']) : '0' ?></td>
                <td><?= isset($data['progress'][0]['cum_due']) ? number_format($data['progress'][0]['cum_due']) : '0' ?></td>
                <td><?= isset($data['progress'][0]['cum_recv']) ? number_format($data['progress'][0]['cum_recv']) : '0' ?></td>
                <td><?= isset($data['progress'][0]['not_yet_due']) ? number_format($data['progress'][0]['not_yet_due']) : '0' ?></td>
                <td><?= isset($data['progress'][0]['olp_amount']) ? number_format($data['progress'][0]['olp_amount']) : '0' ?></td>
            </tr>
            </tbody>
        </table>
        <?php if(!empty($data['dataProvider'])){?>
        <div class="loans-index">
            <div id="ajaxCrudDatatable">
                <?php
                echo \yii\grid\GridView::widget([
                    'columns' => require(__DIR__ . '/_columns_duelist.php'),


                    'dataProvider' => $data['dataProvider'],
                    'filterModel' => $data['searchModel'],
                    'footerRowOptions' => ['style' => 'font-weight:bold;'],
                    'showFooter' => true,
                    'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                <form action="/loans/duelist">
                       <input type="hidden" name="DuelistSearch[sanction_no]" value="' . $data['searchModel']->sanction_no . '">
                                            <input type="hidden" name="DuelistSearch[name]" value="' . $data['searchModel']->name . '">
                                            <input type="hidden" name="DuelistSearch[parentage]" value="' . $data['searchModel']->parentage . '">
                                            <input type="hidden" name="DuelistSearch[team_name]" value="' . $data['searchModel']->team_name . '">
                                            <input type="hidden" name="DuelistSearch[date_disbursed]" value="' . $data['searchModel']->date_disbursed . '">
                                            <input type="hidden" name="DuelistSearch[loan_amount]" value="' . $data['searchModel']->loan_amount . '">
                                            <input type="hidden" name="DuelistSearch[tranch_amount]" value="' . $data['searchModel']->tranch_amount . '">
                                            <input type="hidden" name="DuelistSearch[tranch_no]" value="' . $data['searchModel']->tranch_no . '">
                                            <input type="hidden" name="DuelistSearch[due_amount]" value="' . $data['searchModel']->due_amount . '">
                                            <input type="hidden" name="DuelistSearch[credit]" value="' . $data['searchModel']->credit . '">
                                            <input type="hidden" name="DuelistSearch[outstanding_balance]" value="' . $data['searchModel']->outstanding_balance . '">
                                            <input type="hidden" name="DuelistSearch[grpno]" value="' . $data['searchModel']->grpno . '">
                                            <input type="hidden" name="DuelistSearch[address]" value="' . $data['searchModel']->address . '">
                                            <input type="hidden" name="DuelistSearch[mobile]" value="' . $data['searchModel']->mobile . '">
                                            <input type="hidden" name="DuelistSearch[region_id]" value="' . $data['searchModel']->region_id . '">
                                            <input type="hidden" name="DuelistSearch[area_id]" value="' . $data['searchModel']->area_id . '">
                                            <input type="hidden" name="DuelistSearch[branch_id]" value="' . $data['searchModel']->branch_id . '">
                                            <input type="hidden" name="DuelistSearch[province_id]" value="' . $data['searchModel']->province_id . '">
                                            <input type="hidden" name="DuelistSearch[division_id]" value="' . $data['searchModel']->division_id . '">
                                            <input type="hidden" name="DuelistSearch[district_id]" value="' . $data['searchModel']->district_id . '">
                                            <input type="hidden" name="DuelistSearch[city_id]" value="' . $data['searchModel']->city_id . '">
                                            <input type="hidden" name="DuelistSearch[report_date]" value="' . $data['searchModel']->report_date . '">
                                            <input type="hidden" name="DuelistSearch[project_id]" value="' . $data['searchModel']->project_id . '">
                                            <input type="hidden" name="DuelistSearch[team_id]" value="' . $data['searchModel']->team_id . '">
                        <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                                role="menuitem" tabindex="-1"><i
                                    class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                        </button>
                        <button title="Export to PDF" type="submit" name="export" value="pdf" class="btn btn-default col-sm-12 btn-sm"
                                role="menuitem" tabindex="-1"><i
                                    class="text-primary glyphicon glyphicon-floppy-open"></i> PDF
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

                    ],
                    'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                ])


                ?>
            </div>
        </div>
        <?php } ?>
        <?php Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => '',// always need it for jquery plugin

        ]) ?>
        <?php Modal::end(); ?>
