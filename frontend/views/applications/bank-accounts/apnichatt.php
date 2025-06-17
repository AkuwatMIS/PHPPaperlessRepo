<?php

use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Apni Chatt Report';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Apni Chatt Report</h6>

        <?php echo $this->render('_searchapnichatt', ['model' => $searchModel, 'projects_name' => $projects_name, 'regions' => $regions]); ?>

        <?php if (!empty($dataProvider)) { ?>
            <?= GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax' => true,

                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'cnic',
                        'value' => 'member.cnic',
                        'label' => 'CNIC',
                    ],
                    [
                        'attribute' => 'parentage',
                        'value' => 'member.parentage',
                        'label' => 'Parentage',
                    ],
                    //'member.cnic',
                    [
                        'attribute' => 'mother_name',
                        'value' => 'member.memberInfo.mother_name',
                        'label' => 'mother_name',
                    ],
            
                    [
                        'attribute' => 'title',
                        'value' => 'member.memberAccount.title',
                        'label' => 'title',

                    ],
                    [
                        'attribute' => 'project_id',
                        'value' => 'project.name',
                        'label' => 'Project',
                        'filter' => $projects_name
                    ],
                    [
                        'attribute' => 'branch_id',
                        'value' => 'branch.name',
                        'label' => 'Branch',
                        'filter' => \yii\helpers\ArrayHelper::map(\common\models\Branches::find()->asArray()->all(), 'id', 'name'),
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                        'visibleButtons' => [
                            'update' => function ($data) use ($permissions) {
                                if ((in_array($data->member->memberAccount->status,[0,2])) && in_array('frontend_verify-accountapplications', $permissions)) {
                                    return true;
                                }
                            },
                        ],
                        'buttons' => [
                            'update' => function ($url, $model, $key) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['verify-account', 'id' => $model->member->memberAccount->id], [
                                    'role' => 'modal-remote', 'title' => 'update',
                                ]);
                            },
                        ]
                    ],
                ],
                'toolbar' => [
                    ['content' =>
                        Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                            ['role' => 'modal-remote', 'title' => 'Create new Verified Accounts', 'class' => 'btn btn-default']) .
                        Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                            ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid']) .
                        '{toggleData}' .
                        '{export}'
                    ],
                ],
                'footerRowOptions' => ['style' => 'font-weight:bold;'],
                'showFooter' => true,
                /*'toolbar'=> [
                        ['content'=>
                     Html::a('<i class="glyphicon glyphicon-plus"></i>', ['fileimport'],
                            ['title'=> 'Import File','class'=>'btn btn-default'])
                            ],],*/
//                    Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                'summary' => '
         <div class="dropdown pull-right">
            <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <i class="glyphicon glyphicon-export"></i>
            <span class="caret"></span></button>
                        
             <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation" >
                   <form action="/applications/own-housing-report">
                     <input type="hidden" name="ApplicationsSearch[project_id]" value="' . $searchModel->project_id . '">
                     <input type="hidden" name="ApplicationsSearch[branch_id]" value="' . $searchModel->branch_id . '">
                     <input type="hidden" name="ApplicationsSearch[region_id]" value="' . $searchModel->region_id . '">
                     <input type="hidden" name="ApplicationsSearch[area_id]" value="' . $searchModel->area_id . '">
                     
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
        <?php }
        ?>


    </div>
</div>

<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
