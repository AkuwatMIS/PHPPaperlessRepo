<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProgressReports */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fund Request Summary';
$this->params['breadcrumbs'][] = $this->title;
$export_path = Yii::$app->basePath . '\web\PHPExport\dataexport.php';

CrudAsset::register($this);

$this->registerJs(
    "$(document).ready(function () {
        
        var data = $fundRequest_report
        // prepare the data
        var source =  
            {
                dataType: \"json\",
                dataFields: [
                    { name: \"name\", type: \"string\" },
                    { name: \"disbursement_amount\", type: \"number\" },
                    { name: \"fund_requests_amount_processed\", type: \"number\" },
                    { name: \"account_report_amount_disbursed\", type: \"number\" },
                  
                    { name: \"id\", type: \"number\" },
                    { name: \"children\", type: \"array\" }
                ],
                hierarchy:
                    {
                        root: \"children\"
                    },
                localData: data,
                id: \"id\",
                addRow: function (rowID, rowData, position, parentID, commit) {
                    commit(true);
                    newRowID = rowID;
                },
                updateRow: function (rowID, rowData, commit) {
                    commit(true);
                },
                deleteRow: function (rowID, commit) {
                    commit(true);
                }
            };
        var dataAdapter = new $.jqx.dataAdapter(source, {
            loadComplete: function () {
            }
        });

        // create Tree Grid
        $(\"#treeGrid\").jqxTreeGrid(
            {
                source: dataAdapter,
                altRows: true,
                width: '100%',
                autoRowHeight: true,
                columnsResize: true,
                filterable: true,
                filterMode: 'simple',
                columns: [
                    { text: \"Regions\",pinned: true, align: \"center\", dataField: \"name\", width: 245 },
                    { text: \"Target Amount\", dataField: \"disbursement_amount\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\",width: 270 },
                    { text: \"Processed Amount\", dataField: \"fund_requests_amount_processed\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\",width: 270 },
                    { text: \"Disbursed Amount\", dataField: \"account_report_amount_disbursed\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\",width: 275 }
                    
                ],
                exportSettings : {columnsHeader: true, hiddenColumns: true, serverURL: '/PHPExport/dataexport.php', characterSet: null, collapsedRecords: true, recordsInView: true,
                    fileName: \"Fund Request Report \"}
            });
        $('#jqxbutton').click(function () {
            var rows = $(\"#treeGrid\").jqxTreeGrid('getRows');
            var rowsData = \"\";
            var traverseTree = function(rows)
            {
                for(var i = 0; i < rows.length; i++)
                {
                    rowsData = $(\"#treeGrid\").jqxTreeGrid('getKey', rows[i]);
                    var myArray = new Array(9,6);
                    if(arrValues.contains(rowsData)){
                        alert(rowsData);
                    }
                    if (rows[i].records)
                    {
                        traverseTree(rows[i].records);
                    }
                }
            };
            traverseTree(rows);
            //var key = $(\"#treeGrid\").jqxTreeGrid('getKey', rows[0]);

        });
        $(\"#treeGrid\").jqxTreeGrid('collapseAll');
        $(\"#excelExport\").jqxButton();
        $(\"#htmlExport\").jqxButton();
        $(\"#collapseall\").jqxButton();
        $('#collapseall').click(function () {
            $(\"#treeGrid\").jqxTreeGrid('collapseAll');
        });
        $(\"#expandall\").jqxButton();
        $('#expandall').click(function () {
            $(\"#treeGrid\").jqxTreeGrid('expandAll');
        });
        $(\"#excelExport\").click(function () {
            $(\"#treeGrid\").jqxTreeGrid('exportData', 'xls', 'Fund-Request-Report');
        });
        $(\"#htmlExport\").click(function () {
            $(\"#treeGrid\").jqxTreeGrid('exportData', 'html', 'Fund-Request-Report');
        });

    });"
);
$model = $searchModel;
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <?php $form = ActiveForm::begin([
            'action' => ['fund-request-report'],
            'method' => 'post',
        ]); ?>
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4><i class="fa fa-balance-scale"><?php echo '&nbsp;&nbsp' . $heading ?></i></h4>
                    </div>
                </div>
            </div>
        </header>
        <div class="row">

            <div class="col-sm-2">
                <?php
                //$regions = ArrayHelper::map(Regions::find()->asArray()->all(), 'id', 'name');
                echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region');
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                $value = !empty($model->area_id) ? $model->area_id : null;
                echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['arcaccountreportdetailssearch-region_id'],
                        'initialize' => true,
                        'initDepends' => ['arcaccountreportdetailssearch-region_id'],
                        'placeholder' => 'Select Area',
                        'url' => Url::to(['/structure/fetch-area-by-region'])
                    ],
                    'data' => $value ? [$model->area_id => $value] : []
                ])->label('Area');
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                $value = !empty($model->branch_id) ? $model->branch_id : null;
                echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['arcaccountreportdetailssearch-area_id'],
                        //'initialize' => true,
                        //'initDepends'=>['progressreportdetailssearch-area_id'],
                        'placeholder' => 'Select Branch',
                        'url' => Url::to(['/structure/fetch-branch-by-area'])
                    ],
                    'data' => $value ? [$model->branch_id => $value] : []
                ])->label('Branch');
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                $projects['0'] = 'All';
                ksort($projects);
                echo $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project');
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                echo $form->field($model, 'from_date')->dropDownList(common\components\Helpers\AccountsReportHelper::getMonths(),['prompt'=>''])->label("Month From");
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                echo $form->field($model, 'to_date')->dropDownList(common\components\Helpers\AccountsReportHelper::getMonths(),['prompt'=>''])->label("Month To");
                ?>
            </div>
            <div class="col-sm-1">
                <div style="height: 23px;"></div>
                <div class="form-group">
                    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>

        </div>
        <?php ActiveForm::end(); ?>
        <div style='margin-top: 20px;'>
            <div style='float: right;'>
                <input type="button" id="expandall" value="Expand All"/>
                <input type="button" id="collapseall" value="Collapse All"/>
                <input type="button" value="Export to Excel" id='excelExport'/>
                <input type="button" value="Export to HTML" id='htmlExport'/>
                <br/>
                <br/>
            </div>
        </div>
        <div id="treeGrid">
        </div>
        <div style='margin-top: 20px;'>
        </div>
        <br><br><br><br>
    </div>
</div>

