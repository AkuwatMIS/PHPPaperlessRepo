<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoanWriteOffSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Loan Write off';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');

CrudAsset::register($this);

?>
<div class="container-fluid">
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4><i class="icon fa fa-check"></i>Saved!</h4>
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('error')): ?>
        <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
            <h4><i class="icon fa fa-check"></i>Saved!</h4>
            <?= Yii::$app->session->getFlash('error') ?>
        </div>
    <?php endif; ?>
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-user"></span>
            loan write off List
        </h6>
        <?php  echo $this->render('_search_write_off', ['model' => $searchModel, 'projects' =>$projects,'regions' => $regions]); ?>
        <?php if(in_array('frontend_createloanwriteoff',$permissions))
        { ?>
            <a href="/loan-write-off/create" class="btn btn-success pull-right" title="Add Takaful">Add write off</a>

        <?php }?>


        <?= GridView::widget([
            'id'=>'crud-datatable',
            'pjax'=>true,
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'kartik\grid\SerialColumn'],
                [
                    'class' => 'kartik\grid\CheckboxColumn',
                    'width' => '20px',
                ],
                [
                    'class'=>'kartik\grid\DataColumn',
                    'attribute'=>'sanction_no',
                    'value'=>'loan.sanction_no'
                ],
                'amount',
                'cheque_no',
                'voucher_no',
                [
                    'class'=>'kartik\grid\DataColumn',
                    'attribute'=>'bank_name',
                    'value'=>function($data){
                        if($data->type == 0){
                            return $data->bank_name;
                        }else{
                            return 'NA';
                        }

                    }
                ],
                [
                    'class'=>'kartik\grid\DataColumn',
                    'attribute'=>'bank_account_no',
                    'value'=>function($data){
                        if($data->type == 0){
                            return $data->bank_account_no;
                        }else{
                            return 'NA';
                        }

                    }
                ],

                [
                        'class'=>'kartik\grid\DataColumn',
                        'attribute'=>'reason',
                        'filter' => \common\components\Helpers\ListHelper::getLoanWriteOffReason(),
                        'value'=>function($data){
                       if($data->type == 0){
                           if($data->reason=='disable'){
                               return'Permanently Disable';
                           }else{
                               return 'Death';
                           }
                       }else{
                           return 'NA';
                       }

                    }
                ],
                [
                    'class'=>'kartik\grid\DataColumn',
                    'attribute'=>'status',
                    'filter' => \common\components\Helpers\ListHelper::getLoanWriteOffStatus(),
                    'value'=>function($data){
                        if($data->status==0){
                            return'Pending';
                        }elseif($data->status==1){
                            return 'Approved';
                        }else{
                            return 'Rejected';
                        }

                    }
                ],

                [
                    'class' => 'kartik\grid\ActionColumn',
                    'dropdown' => false,
                    'vAlign'=>'middle',
                    'urlCreator' => function($action, $model, $key, $index) {
                        return Url::to([$action,'id'=>$key]);
                    },
                    'viewOptions'=>['role'=>'modal-remote','title'=>'View','data-toggle'=>'tooltip'],
                    'updateOptions'=>['role'=>'modal-remote','title'=>'Update', 'data-toggle'=>'tooltip'],
                    'template' => '{view} {update}',
                    'visibleButtons' => [
                        'view' => function ($model) {
                            return true;
                        },
                        'update' => function ($model) {
                            if ($model->status == 1 || $model->status == 2) {
                                return false;
                            }else{
                                return true;
                            }
                        },
                    ],
                ],
            ],
            'panel' => [
            'after'=>BulkButtonWidget::widget([
                    'buttons'=>Html::a('<i class="glyphicon glyphicon-pencil"></i>&nbsp; Update All',
                        ["bulk-update"] ,
                        [
                            "class"=>"btn btn-info btn-xs",
                            'role'=>'modal-remote-bulk',
                            'data-confirm'=>false, 'data-method'=>false,// for overide yii data api
                            'data-request-method'=>'post',
                            'data-confirm-title'=>'Are you sure?',
                            'data-confirm-message'=>'Are you sure want to update these items'
                        ]),
                ]).
                 '<div class="clearfix"></div>',
                ]

        ]); ?>
    </div>
</div>

<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
