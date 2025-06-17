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

$this->title = 'User-Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h3><span class="fa fa-list"></span> User Report</h3>
        <?php
        echo $this->render('_search_userreport', [
            'model' => $searchModel,
            'regions' => $regions,
            'roles'=>$roles,
            'areas'=>$areas,
            'branches'=>$branches,

        ]);

        ?>
<br>
        <br>

        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Region</th>
                <th>Area</th>
                <th>Branch</th>
                <th>Team</th>
                <th>Field</th>
                <th>Role</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><?= !empty($searchModel->region_id) ? \common\models\Regions::find()->where(['id'=>$searchModel->region_id])->one()->name : 'All' ?></td>
                <td><?= !empty($searchModel->area_id) ? \common\models\Areas::find()->where(['id'=>$searchModel->area_id])->one()->name: 'All' ?></td>
                <td><?= !empty($searchModel->branch_id) ? \common\models\Branches::find()->where(['id'=>$searchModel->branch_id])->one()->name : 'All' ?></td>
                <td><?= !empty($searchModel->team_id) ? \common\models\Teams::find()->where(['id'=>$searchModel->team_id])->one()->name : 'All' ?></td>
                <td><?= !empty($searchModel->field_id) ? \common\models\Fields::find()->where(['id'=>$searchModel->field_id])->one()->name : 'All' ?></td>
                <td><?= !empty($searchModel->role) ? $searchModel->role: 'All' ?></td>

            </tr>
            </tbody>
        </table>


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
                             <form action="/users/user-report">
                        <input type="hidden" name="UsersSearch[username]" value="'.$searchModel->username.'">
                        <input type="hidden" name="UsersSearch[cnic]" value="'.$searchModel->cnic.'">
                        <input type="hidden" name="UsersSearch[emp_code]" value="'.$searchModel->emp_code.'">
                        <input type="hidden" name="UsersSearch[role]" value="'.$searchModel->role.'">
                        <input type="hidden" name="UsersSearch[region_id]" value="'.$searchModel->region_id.'">
                        <input type="hidden" name="UsersSearch[area_id]" value="'. $searchModel->area_id.'">
                        <input type="hidden" name="UsersSearch[branch_id]" value="'.$searchModel->branch_id.'">
                        <input type="hidden" name="UsersSearch[team_id]" value="'.$searchModel->team_id.'">
                        <input type="hidden" name="UsersSearch[field_id]" value="'.$searchModel->field_id.'">
                        <input type="hidden" name="UsersSearch[no_of_members]" value="'.$searchModel->no_of_members .'">
                        <input type="hidden" name="UsersSearch[no_of_applications]" value="'.$searchModel->no_of_applications.'">
                        <input type="hidden" name="UsersSearch[no_of_social_appraisals]" value="'.$searchModel->no_of_social_appraisals.'">
                        <input type="hidden" name="UsersSearch[no_of_business_appraisals]" value="'.$searchModel->no_of_business_appraisals.'">
                        <input type="hidden" name="UsersSearch[no_of_verifications]" value="'.$searchModel->no_of_verifications.'">
                        <input type="hidden" name="UsersSearch[no_of_groups]" value="'.$searchModel->no_of_groups.'">
                        <input type="hidden" name="UsersSearch[no_of_loans]" value="'. $searchModel->no_of_loans.'">
                        <input type="hidden" name="UsersSearch[no_of_fund_requests]" value="'.$searchModel->no_of_fund_requests.'">
                        <input type="hidden" name="UsersSearch[no_of_disbursements]" value="'.$searchModel->no_of_disbursements.'">
                        <input type="hidden" name="UsersSearch[no_of_recoveries]" value="'.$searchModel->no_of_recoveries.'">
                        <input type="hidden" name="UsersSearch[report_date]" value="'.$searchModel->report_date.'">
                        <input type="hidden" name="UsersSearch[platform]" value="'.$searchModel->platform.'">
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
    </div>
</div>
        <?php Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => '',// always need it for jquery plugin

        ]) ?>
        <?php Modal::end(); ?>
