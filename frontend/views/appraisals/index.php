<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AppraisalsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Appraisals';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
            Appraisals List
            <a href="/appraisals/create" class="btn btn-success pull-right" title="Create Application">Create Appraisals</a>
        </h6>
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => require(__DIR__ . '/_columns.php'),
                'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
            ]); ?>

        </div>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>


