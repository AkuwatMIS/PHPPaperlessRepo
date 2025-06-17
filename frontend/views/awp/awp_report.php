<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use kartik\daterange\DateRangePicker;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\depdrop\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use common\models\Regions;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProgressReports */
/* @var $dataProvider yii\data\ActiveDataProvider */
/*print_r($progress_report);
die();*/
$this->title = 'Detailed AWP Report';
$this->params['breadcrumbs'][] = $this->title;
$export_path = Yii::$app->basePath.'\web\PHPExport\dataexport.php';
/*echo $export_path;
//echo dirname(__DIR__);
die('we die here');*/
CrudAsset::register($this);
$this->registerJs(
    "$(document).ready(function () {

        var data = $awp_report
        // prepare the data
        var source =  
            {
                dataType: \"json\",
                dataFields: [
                    { name: \"name\", type: \"string\" },
                    { name: \"branch_code\", type: \"string\" },
                    { name: \"district\", type: \"string\" },
                    { name: \"no_of_loans\", type: \"number\" },
                    { name: \"amount_disbursed\", type: \"number\" },
                    { name: \"monthly_recovery\", type: \"number\" },
                    { name: \"funds_required\", type: \"number\" },
                    { name: \"actual_recovery\", type: \"number\" },
                    { name: \"actual_disbursement\", type: \"number\" },
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
                    { text: \"No of Loans\", cellsAlign: \"center\", align: \"center\", cellsFormat: 'n', dataField: \"no_of_loans\" , width: 150 },
                    { text: \"Total Disbursement Amount(PKR)\", dataField: \"amount_disbursed\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\",width: 190 },
                    { text: \"Total Recovery(PKR)\", dataField: \"monthly_recovery\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\" ,width: 190 },
                    { text: \"Total Funds Require(PKR)\", dataField: \"funds_required\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\" ,width: 190 },
                    { text: \"Actual Recovery(PKR)\", dataField: \"actual_recovery\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\" ,width: 170 },
                    { text: \"Actual Disbursement(PKR)\", dataField: \"actual_disbursement\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\" ,width: 190 },
               ],
               
                exportSettings : {columnsHeader: true, hiddenColumns: true, serverURL: '/PHPExport/dataexport.php', characterSet: null, collapsedRecords: true, recordsInView: true,
                    fileName: \"Awp Report \"}
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
            $(\"#treeGrid\").jqxTreeGrid('exportData', 'xls', 'Awp-Report');
        });
        $(\"#htmlExport\").click(function () {
            $(\"#treeGrid\").jqxTreeGrid('exportData', 'html', 'Awp-Report');
        });

    });"
);
$model = $searchModel;
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">


        <?php $form = ActiveForm::begin([
            'action' => ['awp-report'],
            'method' => 'post',
        ]); ?>
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4><i class="fa fa-map"></i><b style="font-size:20px"> AWP</b> Region Wise Report</h2></i></h4>
                    </div>
                </div>
            </div>
        </header>
        <div class="row">

            <div class="col-sm-3">
                <?php
                //$regions = ArrayHelper::map(Regions::find()->asArray()->all(), 'id', 'name');
                echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region');
                ?>
            </div>
            <div class="col-sm-3">
                <?php
                $value = !empty($model->area_id)?$model->area->name:null;
                echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                    'pluginOptions'=>[
                        'depends'=>['awpsearch-region_id'],
                        'initialize' => true,
                        'initDepends'=>['awpsearch-region_id'],
                        'placeholder'=>'Select Area',
                        'url'=>Url::to(['/structure/fetch-area-by-region'])
                    ],
                    'data' => $value?[$model->area_id => $value]:[]
                ] )->label('Area');
                ?>
            </div>
            <div class="col-sm-3">
                <?php
                $value = !empty($model->branch_id)?$model->branch->name:null;
                echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                    'pluginOptions'=>[
                        'depends'=>['awpsearch-area_id'],
                        //'initialize' => true,
                        //'initDepends'=>['progressreportdetailssearch-area_id'],
                        'placeholder'=>'Select Branch',
                        'url'=>Url::to(['/structure/fetch-branch-by-area'])
                    ],
                    'data' => $value?[$model->branch_id => $value]:[]
                ] )->label('Branch');
                ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'project_id')->dropDownList($projects, ['class' => 'form-control','prompt' => 'All Projects'])->label('Projects');?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'month_from')->textInput(['placeholder'=>'Select Month', 'class'=>'form-control'])->dropDownList(common\components\Helpers\AwpHelper::getAwpMonthUpdated()) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'month')->textInput(['placeholder'=>'Select Month', 'class'=>'form-control'])->dropDownList(common\components\Helpers\AwpHelper::getAwpMonthUpdated())->label('Month To') ?>
            </div>
            <div class="col-sm-1">
                <div style="height: 23px;"></div>
                <div class="form-group">
                    <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        <div class="row">

        </div>



        <?php ActiveForm::end(); ?>


        <div style='margin-top: 20px;'>
            <div style='float: right;'>
                <input type="button" id="expandall" value="Expand All" />
                <input type="button" id="collapseall" value="Collapse All" />
                <input type="button" value="Export to Excel" id='excelExport' />
                <input type="button" value="Export to HTML" id='htmlExport' />
                <br />
                <br />
            </div>
        </div>
        <div id="treeGrid">
        </div>
        <div style='margin-top: 20px;'>
        </div>
        <br><br><br><br>


    </div>
</div>