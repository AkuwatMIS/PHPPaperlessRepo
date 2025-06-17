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

$this->title = 'Donation Report Commulative';
$this->params['breadcrumbs'][] = $this->title;
$export_path = Yii::$app->basePath . '\web\PHPExport\dataexport.php';

CrudAsset::register($this);

$this->registerJs(
    "$(document).ready(function () {
        
        var data = $account_report
        // prepare the data
        var source =  
            {
                dataType: \"json\",
                dataFields: [
                    { name: \"name\", type: \"string\" },
                    { name: \"branch_code\", type: \"string\" },
                    { name: \"district\", type: \"string\" },
                    //{ name: \"objects_count\", type: \"number\" },
                    { name: \"amount\", type: \"number\" },
                    //{ name: \"average\", type: \"number\" },
                   
                  
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
                    //{ text: \"No of Loans\", cellsAlign: \"center\", cellsFormat: 'n', dataField: \"objects_count\" , align: \"center\", width: 200 },
                    { text: \"MDP\", dataField: \"amount\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\",width: 800 },
                   // { text: \"MDP/Borrower\", dataField: \"average\", cellsAlign: \"center\", cellsFormat: 'n', align: \"center\",width: 300 }
                    
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
            'action' => ['donationreportoverallcommulative'],
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
                $value = !empty($model->branch_id) ? $model->branch->name : null;
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
                $value = !empty($model->team_id) ? $model->team->name : null;
                echo $form->field($model, 'team_id')->widget(DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['arcaccountreportdetailssearch-branch_id'],
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
                        'depends' => ['arcaccountreportdetailssearch-team_id'],
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
                echo $form->field($model, 'from_date')->dropDownList(common\components\Helpers\AccountsReportHelper::getMonths(),['prompt'=>''])->label("Month From");
                ?>
            </div>
            <div class="col-sm-2">
                <?php
                echo $form->field($model, 'to_date')->dropDownList(common\components\Helpers\AccountsReportHelper::getMonths(),['prompt'=>''])->label("Month");
                ?>
            </div>

            <?php

            /*
                        $value = !empty($model->project_ids) ? $model->project_ids : null;
                        echo $form->field($model, "project_ids")->widget(\kartik\depdrop\DepDrop::classname(), [
                            'type'=>\kartik\depdrop\DepDrop::TYPE_SELECT2,
                            'options' => [
                                'class' => 'form-control update-trigger field-selection',
                                'data-field_index' => '1',
                                'multiple' => true,
                                'theme' => Select2::THEME_BOOTSTRAP,
                            ],
                            'pluginOptions' => [
                                //'multiple' => true,
                                //'allowClear' => true,
                                //'initialize' => true,
                                'depends' => ['loanssearch-branch_id'],
                                'initDepends' => ['loanssearch-branch_id'],
                                'placeholder' => 'Select Projects',
                                'url' => \yii\helpers\Url::to(['/structure/branchprojectsbyid'])
                            ],
                            'data' => $value
                        ])->label('Projects');
                        */?>
            <?php
            /*echo $form->field($model, 'project_ids')->widget(Select2::classname(), [
                'data' => $projects,
                'options' => ['placeholder' => 'Select Project'],
                'pluginOptions' => [
                    'multiple' => true,
                    'allowClear' => true
                ],
            ])->label("Project");
            */?>
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

