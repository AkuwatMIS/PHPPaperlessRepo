<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ACAG Loans';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="font-icon glyphicon glyphicon-tag"></span>
            Pending Loans List For Pledge Status
        </h6>
        <?php echo $this->render('_pledge_search', [
            'model' => $data['searchModel'],
            'regions_by_id' => $data['regions_by_id'],
            'projects' => $data['projects'],
        ]); ?>
        <?php
        $dataProvider = $data['dataProvider'];
        $searchModel = $data['searchModel'];
        ?>
        <?php if (!empty($dataProvider)) { ?>
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $data['dataProvider'],
                    'filterModel' => $data['searchModel'],
                    'columns' => require(__DIR__ . '/_pledge_columns.php'),
                    'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     ',
                    'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                ]); ?>

            </div>
        <?php } else { ?>
            <div class="table-responsive">
                <hr>
                <h3>Search Loans through above filters!</h3>
            </div>
        <?php } ?>
    </div>
</div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>