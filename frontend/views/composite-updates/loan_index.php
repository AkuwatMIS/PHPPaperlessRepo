<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Loans';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="font-icon glyphicon glyphicon-tag"></span>
            Loans List
            <?php echo $this->render('_loan_search', [
                'model' => $data['searchModel'],
            ]); ?>
            <?php if (!empty($data['dataProvider'])) { ?>
                <div class="table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $data['dataProvider'],
                        'filterModel' => $data['searchModel'],
                        'columns' => require(__DIR__ . '/_loan_columns.php'),
                        'summary' => '',
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
