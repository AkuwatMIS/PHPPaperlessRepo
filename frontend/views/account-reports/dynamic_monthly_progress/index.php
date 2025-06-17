<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DynamicReportsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Dynamic Reports';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-list"></span>
            Dynamic Reports
            <?php if(in_array('frontend_createdynamicreports',$permissions))
            { ?>
                <a href="/account-reports/monthly-progress-dynamic" class="btn btn-success pull-right" title="Create Reports" role="modal-remote">Create Reports</a>

            <?php }?>
        </h6>
        <div class="table-responsive">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['monthly-progress-dynamic'],
                        ['role'=>'modal-remote','title'=> 'Create new Dynamic Reports','class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                        ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                    '{toggleData}'.
                    '{export}'
                ],
            ],

            'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
        ]);?>
        </div>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
