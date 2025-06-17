<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BusinessAppraisalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Business Appraisals';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="font-icon font-icon-speed"></span>
            Business Appraisal List <a href="/business-appraisal/create" class="btn btn-success pull-right" title="Create Business Appraisal">Create Business Appraisal</a></h6>

        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions'=>$regions
        ]); ?>
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
