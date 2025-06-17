<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MembersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Members';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="members-index">
    <?php echo $this->render('_search', [
        'model' => $searchModel,
        'regions' => $regions,
    ]); ?>
    <div id="ajaxCrudDatatable">
        <?php if(!empty($dataProvider)){?>
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'rowOptions' => function ($model, $index, $widget, $grid){
                if($model->deleted=1){
                    return ['class' => GridView::TYPE_DANGER];
                }
            },
           'pjax'=>true,
            'columns' => require(__DIR__.'/_columns.php'),
            'toolbar'=> [
                ['content'=>
                    Html::a('<i class="glyphicon glyphicon-plus"></i>', ['create'],
                    ['role'=>'modal-remote','title'=> 'Create new Members','class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-eye-open"></i>', ['members-logs'],
                        ['role'=>'modal-remote','title'=> 'Members Logs','class'=>'btn btn-default']).
                    Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                    ['data-pjax'=>1, 'class'=>'btn btn-default', 'title'=>'Reset Grid']).
                    '{toggleData}'.
                    '{export}'
                ],
            ],
            'striped' => true,
            'condensed' => true,
            'responsive' => true,
            'panel' => [
                'type' => 'primary',
                'heading' => '<i class="glyphicon glyphicon-user"></i> Members listing',
            ]
        ])?>
        <?php  }?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
