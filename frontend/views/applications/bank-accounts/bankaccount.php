<?php

use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Bank Report';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Bank Account Report</h6>

        <?php echo $this->render('_searchbankaccount', ['model' => $searchModel, /*'bank_names' => $bank_names,*/ 'projects_name' => $projects_name, 'regions' => $regions]); ?>

        <?php if (!empty($dataProvider)) { ?>
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
                    //'member.cnic',
                    [
                        'attribute' => 'cnic',
                        'value' => 'member.cnic',
                        'label' => 'CNIC',
                    ],
                    [
                        'attribute' => 'application_no'
                    ],
                    [
                        'attribute' => 'bank_name',
                        'value' => 'member.memberAccount.bank_name',
                        'label' => 'Bank',
                        'filter' => $bank_names

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
                        'attribute' => 'title',
                        'value' => 'member.memberAccount.title',
                        'label' => 'Title',

                    ],
                    [
                        'attribute' => 'account_no',
                        'value' => 'member.memberAccount.account_no',
                        'label' => 'Account No',

                    ],
                    [
                        'attribute' => 'account_file_id',
                        'value' => 'member.memberAccount.acc_file_id',
                        'label' => 'Account File ID',

                    ],
                    [
                        'attribute' => 'account_file_very_at',
                        'value' => function($data) {
                            return date('d M Y',$data->member->memberAccount->verified_at);
                        },
                        'label' => 'Account verified At',

                    ],
                    [
                        'attribute' => 'created_at',
                        'value' => function($data) {
                            return date('d M Y',$data->member->memberAccount->created_at);
                        },
                        'label' => 'Account Created At',

                    ],
                    [
                        'attribute' => 'last_action_at',
                        'value' => function($data) {
                            return date('d M Y',$data->loan->created_at);
                        },
                        'label' => 'Last Action Taken Date',

                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($data, $key, $index) {
                            return \common\components\Helpers\StructureHelper::getMemberaccountstatus($data->member->memberAccount->status);
                        },
                        'filter' => \common\components\Helpers\ListHelper::getLists('verification'),
                        'label' => 'Status',

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
                'summary' => '
         Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
         <div class="dropdown pull-right">
            <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
            <i class="glyphicon glyphicon-export"></i>
            <span class="caret"></span></button>
                        
             <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                <li role="presentation" >
                   <form action="/applications/bankaccount">
                     <input type="hidden" name="ApplicationsSearch[name]" value="' . $searchModel->full_name . '">
                     <input type="hidden" name="ApplicationsSearch[parentage]" value="' . $searchModel->parentage . '">
                     <input type="hidden" name="ApplicationsSearch[cnic]" value="' . $searchModel->cnic . '">
                     <input type="hidden" name="ApplicationsSearch[application_no]" value="' . $searchModel->application_no . '">
                     <input type="hidden" name="ApplicationsSearch[bank_name]" value="' . $searchModel->bank_name . '">
                     <input type="hidden" name="ApplicationsSearch[project_id]" value="' . $searchModel->project_id . '">
                     <input type="hidden" name="ApplicationsSearch[branch_id]" value="' . $searchModel->branch_id . '">
                     <input type="hidden" name="ApplicationsSearch[region_id]" value="' . $searchModel->region_id . '">
                     <input type="hidden" name="ApplicationsSearch[area_id]" value="' . $searchModel->area_id . '">
                     <input type="hidden" name="ApplicationsSearch[title]" value="' . $searchModel->title . '">
                     <input type="hidden" name="ApplicationsSearch[account_file_id]" value="' . $searchModel->account_file_id . '">
                     <input type="hidden" name="ApplicationsSearch[status]" value="' . $searchModel->status . '">
                     
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
