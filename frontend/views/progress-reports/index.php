<?php

use yii\helpers\Html;
use kartik\select2\Select2;
use yii\widgets\ActiveForm;
use johnitvn\ajaxcrud\CrudAsset;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProgressReports */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Progress Reports';
$this->params['breadcrumbs'][] = $this->title;
$export_path = Yii::$app->basePath . '\web\PHPExport\dataexport.php';

CrudAsset::register($this);

$this->registerJs(
    "$(document).ready(function () {

        var data = $progress_report
        // prepare the data
        var source =  
            {
                dataType: \"json\",
                dataFields: [
                    { name: \"name\", type: \"string\" },
                    { name: \"branch_code\", type: \"string\" },
                    { name: \"district\", type: \"string\" },
                    { name: \"members_count\", type: \"number\" },
                    { name: \"no_of_loans\", type: \"number\" },
                    { name: \"family_loans\", type: \"number\" },
                    { name: \"female_loans\", type: \"number\" },
                    { name: \"active_loans\", type: \"number\" },
                    { name: \"cum_disb\", type: \"number\" },
                    { name: \"cum_due\", type: \"number\" },
                    { name: \"cum_recv\", type: \"number\" },
                    { name: \"overdue_borrowers\", type: \"number\" },
                    { name: \"overdue_amount\", type: \"number\" },
                    { name: \"overdue_percentage\", type: \"number\" },
                    { name: \"par_amount\", type: \"number\" },
                    { name: \"par_percentage\", type: \"number\" },
                    { name: \"not_yet_due\", type: \"number\" },
                    { name: \"olp_amount\", type: \"number\" },
                    { name: \"recovery_percentage\", type: \"number\" },
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
                    { text: \"Regions\",pinned: true, align: \"center\", dataField: \"name\", width: 220 },
                    { text: \"Branch Code\", cellsAlign: \"center\", cellsFormat: 'n', dataField: \"branch_code\" ,hidden: true, width: 70 },
                    { text: \"District\", cellsAlign: \"center\", cellsFormat: 'n', dataField: \"district\" , hidden: true, width: 70 },
                    { text: \"Beneficiaries\", cellsAlign: \"center\", cellsFormat: 'n', dataField: \"members_count\" , width: 70 },
                    { text: \"Loans\", cellsAlign: \"center\", cellsFormat: 'n', dataField: \"no_of_loans\" , width: 70 },
                    { text: \"Male Loans\", dataField: \"family_loans\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\",width: 70 },
                    { text: \"Female Loans\", dataField: \"female_loans\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\" ,width: 70 },
                    { text: \"Active Loans\", dataField: \"active_loans\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\" ,width: 70 },
                    { text: \"Cum Disb\", dataField: \"cum_disb\", cellsAlign: \"center\", align: \"center\", cellsFormat: 'n',width: 100 },
                    { text: \"Cum Due\", dataField: \"cum_due\", cellsAlign: \"center\", align: \"center\" , cellsFormat: 'n',width: 100 },
                    { text: \"Cum Recov\", dataField: \"cum_recv\", cellsAlign: \"center\", align: \"center\", cellsFormat: 'n',width: 100 },
                    { text: \"OD Borrowers\", dataField: \"overdue_borrowers\", cellsAlign: \"center\", align: \"center\" , cellsFormat: 'n', width: 100 },
                    { text: \"Overdue\", dataField: \"overdue_amount\", cellsAlign: \"center\", align: \"center\" , cellsFormat: 'n', width: 100 },
                    { text: \"% Overdue\", dataField: \"overdue_percentage\", cellsAlign: \"center\", align: \"center\" , cellsFormat: 'p',width: 55 },
                    { text: \"PAR\", dataField: \"par_amount\", cellsAlign: \"center\", align: \"center\" , cellsFormat: 'n',width: 100 },
                    { text: \"% PAR\", dataField: \"par_percentage\", cellsAlign: \"center\", align: \"center\" , cellsFormat: 'p',width: 55 },
                    { text: \"Not Yet Due\", dataField: \"not_yet_due\", cellsAlign: \"center\", align: \"center\" , cellsFormat: 'n',width: 100 },
                    { text: \"OLP\", dataField: \"olp_amount\", cellsAlign: \"center\", align: \"center\" , cellsFormat: 'n',width: 100 },
                    {text: \"% Recovery\", cellsAlign: \"center\", align: \"center\", dataField: \"recovery_percentage\", cellsFormat: 'p',width: 58}
                ],
                exportSettings : {columnsHeader: true, hiddenColumns: true, serverURL: '/PHPExport/dataexport.php', characterSet: null, collapsedRecords: true, recordsInView: true,
                    fileName: \"Progress Report \"}
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
            $(\"#treeGrid\").jqxTreeGrid('exportData', 'xls', 'Progress-Report');
        });
        $(\"#htmlExport\").click(function () {
            $(\"#treeGrid\").jqxTreeGrid('exportData', 'html', 'Progress-Report');
        });

    });"
);
$model = $searchModel;
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <?php $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'post',
        ]); ?>
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4><i class="fa fa-line-chart"><?php echo '&nbsp;&nbsp' . $heading ?></i></h4>
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
                $value = !empty($model->area_id) ? $model->area->name : null;
                echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['progressreportdetailssearch-region_id'],
                        'initialize' => true,
                        'initDepends' => ['progressreportdetailssearch-region_id'],
                        'placeholder' => 'Select Area',
                        'url' => Url::to(['/structure/fetch-area-by-region'])
                    ],
                    'data' => $value ? [$model->area_id => $value] : []
                ])->label('Area');
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                $value = !empty($model->branch_id) ? $model->branch->name : null;
                echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['progressreportdetailssearch-area_id'],
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
                $value = !empty($model->team_id) ? $model->team->name : null;
                echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['progressreportdetailssearch-branch_id'],
                        //'initialize' => true,
                        //'initDepends'=>['progressreportdetailssearch-area_id'],
                        'placeholder' => 'Select Branch',
                        'url' => Url::to(['/structure/fetch-team-by-branch'])
                    ],
                    'data' => $value ? [$model->team_id => $value] : []
                ])->label('Team');
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                $value = !empty($model->filed_id) ? $model->field->name : null;
                echo $form->field($model, 'field_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['progressreportdetailssearch-team_id'],
                        //'initialize' => true,
                        //'initDepends'=>['progressreportdetailssearch-area_id'],
                        'placeholder' => 'Select Field',
                        'url' => Url::to(['/structure/fetch-field-by-team'])
                    ],
                    'data' => $value ? [$model->team_id => $value] : []
                ])->label('Field');
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                $projects['0'] = 'All';
                ksort($projects);
                echo $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project');
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-2">
                <?php
                //print_r($model);
                //die();
                $value = !empty($model->progress_report_id) ? date('M j, Y',$model->progress->report_date) : null;
                //print_r($value);
                //die();
                echo $form->field($model, 'progress_report_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['progressreportdetailssearch-project_id'],
                        //'initialize' => true,
                        //'initDepends'=>['progressreportdetailssearch-area_id'],
                        'placeholder' => 'Select Report Date',
                        'required' => 'required',
                        'url' => Url::to(['/structure/fetch-date-by-project'])
                    ],
                    'data' => $value?[$model->progress_report_id => $value]:[]
                ])->label('Report Date');
                ?>
            </div>

            <!--<div class="col-sm-2">
                <?php
/*                echo $form->field($model, 'gender')->widget(Select2::classname(), [
                    'data' => array_merge(["0" => "All"], array('m' => 'Male', 'f' => 'Female')),
                    'options' => ['placeholder' => 'Select Gender'],
                    'hideSearch' => true,
                    //'size' => Select2::MEDIUM,
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ])->label("Gender");
                */?>
            </div>-->
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

