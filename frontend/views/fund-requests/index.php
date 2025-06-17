<?php

use johnitvn\ajaxcrud\CrudAsset;
use yii\bootstrap\Modal;
//use yii\grid\GridView;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\FundRequestsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Fund Requests';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="font-icon glyphicon glyphicon-book"></span>
            Fund Request List
            <?php if(in_array('frontend_createfundrequests',$permissions))
            { ?>
            <a href="/fund-requests/create" class="btn btn-success pull-right" title="Create Fund Request">Create Fund Request</a></h6>
        <?php }?> <br>

       <?php echo $this->render('_fundrequestcount',['count_as_status' => $count_as_status])?>

        <?php echo $this->render('_search_overall', [
            'model' => $searchModel,
            'regions' => $regions
        ]); ?>

        <div class="table-responsive">
            <?= GridView::widget([
                'id'=>'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'pjax'=>true,
                'columns' => require(__DIR__ . '/_columns.php'),
                'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                'rowOptions'=>function($model){
                    if($model->status == 'approved'){
                        return ['class' => 'success'];
                    }
                },
            ]); ?>

        </div>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
