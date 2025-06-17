<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProgressReportsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Account Reports';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="progress-reports-index">
    <div id="ajaxCrudDatatable">
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'rowOptions' => function ($model) {
                if ($model->is_verified == 1) {
                    return ['class' => 'success'];
                }
                if ($model->status == 0) {
                    return ['class' => 'danger'];
                }
                if ($model->do_update == 1) {
                    return ['class' => 'info'];
                }
            },
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update-month-reports'],
                        ['role'=>'modal-remote','title'=> 'Create new Account Reports','class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                        ['role'=>'modal-remote','title'=> 'Create new Account Reports','class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-edit"></i>', ['update-reports'],
                        ['target'=>'blank','title'=> 'Update Account Reports','class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                    ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid'])

                ],
            ],          
            'striped' => true,
            'condensed' => true,
            'responsive' => true,          
            'panel' => [
                'type' => 'primary', 
                'heading' => '<i class="glyphicon glyphicon-list"></i> Progress Reports listing',
            ]
        ])?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
