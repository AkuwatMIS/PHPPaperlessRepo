<?php

use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'S.E Visits';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span>S.E Visits</h6>
        <?php echo $this->render('_searchvisitsreport', ['model' => $searchModel, 'regions' => $regions]); ?>
        <?= GridView::widget([
            'id' => 'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax' => true,

            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                [
                    'attribute' => 'full_name',
                    'value' => 'member.full_name',
                    'label' => 'Name',
                ],
                [
                    'attribute' => 'parentage',
                    'value' => 'member.parentage',
                    'label' => 'Parentage',
                ],
                [
                    'attribute' => 'cnic',
                    'value' => 'member.cnic',
                    'label' => 'CNIC',
                ],
                [
                    'attribute' => 'region_id',
                    'value' => 'region.name',
                    'label' => 'Region',
                    'filter' => $regions,
                ],
                [
                    'attribute' => 'area_id',
                    'value' => 'area.name',
                    'label' => 'Area',
                    'filter' => \yii\helpers\ArrayHelper::map(\common\models\Areas::find()->asArray()->all(), 'id', 'name'),
                ],
                [
                    'attribute' => 'branch_id',
                    'value' => 'branch.name',
                    'label' => 'Branch',
                    'filter' => \yii\helpers\ArrayHelper::map(\common\models\Branches::find()->asArray()->all(), 'id', 'name'),

                ],
                [
                    'attribute' => 'created_by',
                    'value' => 'lastVisit.user.username',
                    'label' => 'Site Engineer',
                ],
                [
                    'attribute' => 'percent',
                    'value' => 'lastVisit.percent',
                    'label' => 'Percent',
                ],
                [
                    'attribute' => 'created_at',
                    'value' => function ($data) {
                        return date('d M y', $data->lastVisit->created_at);
                    },
                    'label' => 'Visited at',
                ],
                [
                    'attribute' => 'visit_count',
                    'value' => function ($data) {
                        return ($data->visitsCount);
                    },
                    'label' => 'No. of Visits',
                ],
                [
                    'attribute' => 'application_no',
                    'value' => 'application_no',
                    'label' => 'Application No',
                ],
                [
                    'attribute' => 'application_date',
                    'value' => function ($data) {
                        return date('d M y', $data->application_date);
                    },
                    'label' => 'Application Date',
                ],
                [
                    'attribute' => 'recommended_amount',
                    'value' => function ($data) {
                        return number_format($data->recommended_amount);
                    },
                    'label' => 'Recommended Amount',
                ],
                [
                    'attribute' => 'is_shifted',
                    'value' => function ($data) {
                        if (isset($data->applicationDetails->is_shifted) == null) {
                            return 0;
                        }

                        if ($data->applicationDetails->is_shifted == 0) {
                            $is_shifted = 'Not Shifted';
                        } else {
                            $is_shifted = 'Shifted';
                        }

                        return $is_shifted;
                    },
                    'label' => 'Shifted Status',
                ],
                [
                    'class' => 'yii\grid\ActionColumn',
                    'buttons' => [
                        'visit_history' => function ($url, $model, $key) {
                            if (in_array($model->project_id, \common\components\Helpers\StructureHelper::trancheProjects())) {
                                return \yii\helpers\Html::a('<span class="glyphicon glyphicon-picture"></span>', ['visit-details', 'id' => $model->id], ['target' => '_blank'], ['title' => 'Visit History']);
                            }
                        },
                    ],
                    'template' => '{visit_history}',
                    'contentOptions' => ['style' => 'width:70px;'],
                ],
            ],

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
                   <form action="/applications/visits-reports">
                     <input type="hidden" name="ApplicationsSearch[name]" value="' . $searchModel->full_name . '">
                     <input type="hidden" name="ApplicationsSearch[parentage]" value="' . $searchModel->parentage . '">
                     <input type="hidden" name="ApplicationsSearch[cnic]" value="' . $searchModel->cnic . '">
                     <input type="hidden" name="ApplicationsSearch[region_id]" value="' . $searchModel->region_id . '">
                     <input type="hidden" name="ApplicationsSearch[area_id]" value="' . $searchModel->area_id . '">
                     <input type="hidden" name="ApplicationsSearch[branch_id]" value="' . $searchModel->branch_id . '">
                     <input type="hidden" name="ApplicationsSearch[site_engineer_name]" value="' . $searchModel->lastVisit->created_by . '">
                     <input type="hidden" name="ApplicationsSearch[percent]" value="' . $searchModel->lastVisit->percent . '">
                      <input type="hidden" name="ApplicationsSearch[visited_at]" value="' . $searchModel->lastVisit->created_at . '">
                     <input type="hidden" name="ApplicationsSearch[visit_count]" value="' . $searchModel->visit_count . '">
                     <input type="hidden" name="ApplicationsSearch[application_no]" value="' . $searchModel->application_no . '">
                     <input type="hidden" name="ApplicationsSearch[application_date]" value="' . $searchModel->application_date . '">
                     <input type="hidden" name="ApplicationsSearch[recommended_amount]" value="' . $searchModel->recommended_amount . '">
                     <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                        role="menuitem" tabindex="-1"><i
                            class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                    </button>
                </form>
                </li>
            </ul>
         </div>
               ',

        ]); ?>

    </div>
</div>
