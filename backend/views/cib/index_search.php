<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApplicationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Applications Cib';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="applications-index">
    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'regions' => $regions,
        'projects' => $projects,
    ]); ?>
    <div id="ajaxCrudDatatable">
        <?php if (!empty($dataProvider)) { ?>
            <?= GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProvider,
//                'filterModel' => $searchModel,
                'pjax' => true,
                'columns' => require(__DIR__ . '/_columns.php'),
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
                               <form action="/cib/index-search">
                                 <input type="hidden" name="ApplicationsCibSearch[id]" value="' . $searchModel->id . '">
                                 <input type="hidden" name="ApplicationsCibSearch[application_id]" value="' . $searchModel->application_id . '">
                                 <input type="hidden" name="ApplicationsCibSearch[cnic]" value="' . $searchModel->member_cnic . '">
                                 <input type="hidden" name="ApplicationsCibSearch[region]" value="' . $searchModel->region_id . '">
                                 <input type="hidden" name="ApplicationsCibSearch[area]" value="' . $searchModel->area_id . '">
                                 <input type="hidden" name="ApplicationsCibSearch[branch]" value="' . $searchModel->branch_id . '">
                                 <input type="hidden" name="ApplicationsCibSearch[receipt_no]" value="' . $searchModel->receipt_no . '">
                                 <input type="hidden" name="ApplicationsCibSearch[application]" value="' . $searchModel->project_id . '">
                                 <input type="hidden" name="ApplicationsCibSearch[status]" value="' . $searchModel->status . '">
                                 <input type="hidden" name="ApplicationsCibSearch[application]" value="' . $searchModel->req_amount . '">
                                 <input type="hidden" name="ApplicationsCibSearch[city_id]" value="' . $searchModel->city_id . '">
                                 <input type="hidden" name="ApplicationsCibSearch[member]" value="' . $searchModel->gender . '">
                                 <input type="hidden" name="ApplicationsCibSearch[application]" value="' . $searchModel->app_date . '">
                                 <input type="hidden" name="ApplicationsCibSearch[cib_date]" value="' . $searchModel->cib_date . '">
                                 <input type="hidden" name="ApplicationsCibSearch[application]" value="' . $searchModel->app_status . '">
                                 <input type="hidden" name="ApplicationsCibSearch[member]" value="' . $searchModel->member_name . '">
                                 <input type="hidden" name="ApplicationsCibSearch[member]" value="' . $searchModel->dob . '">
                                 <input type="hidden" name="ApplicationsCibSearch[parentage]" value="' . $searchModel->parentage . '">
                                 <input type="hidden" name="ApplicationsCibSearch[application]" value="' . $searchModel->app_no . '">
                                 <input type="hidden" name="ApplicationsCibSearch[address]" value="' . $searchModel->address . '">
                                 <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                                    role="menuitem" tabindex="-1"><i
                                        class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                                </button>
                            </form>
                            </li>
                        </ul>
                     </div>
               ',
                'toolbar' => [
                    ['content' =>
                        Html::a('<i class="glyphicon glyphicon-plus"></i>', [''],
                            ['role' => 'modal-remote', 'title' => 'Create new Applications', 'class' => 'btn btn-default']) .
                        Html::a('<i class="glyphicon glyphicon-eye-open"></i>', [''],
                            ['role' => 'modal-remote', 'title' => 'Applications Logs', 'class' => 'btn btn-default']) .
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                            ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid']) .
                        '{toggleData}' .
                        '{export}'
                    ],
                ],
                'striped' => true,
                'condensed' => true,
                'responsive' => true,
                'panel' => [
                    'type' => 'primary',
                    'heading' => '<i class="glyphicon glyphicon-envelope"></i> Cib listing',

                ]
            ]) ?>
        <?php } ?>
    </div>
</div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
