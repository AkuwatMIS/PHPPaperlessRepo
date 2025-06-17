<?php

use yii\helpers\Html;
use yii\grid\GridView;
use \yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\TemporaryDisbursementRejectedSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $memberModel common\models\Search\BorrowersSearch */
/* @var $types */
$this->title = 'Temporary Reject Loan';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);

if(Yii::$app->session->hasFlash('success')){
    $js = "swal({
		title: \"Success\",
		text: \"" .Yii::$app->session->getFlash('success') ."\",
        type: \"success\",
        confirmButtonClass: \"btn-success\"
	 });";
	 $this->registerJs($js);
}elseif(Yii::$app->session->hasFlash('danger')){
    $js = "swal({
		title: \"Failed\",
		text: \"" .Yii::$app->session->getFlash('danger') ."\",
        type: \"error\",
        confirmButtonClass: \"btn-danger\"
	 });";
    $this->registerJs($js);
}elseif(Yii::$app->session->hasFlash('pending')){
    $js = "swal({
		title: \"Warning\",
		text: \"" .Yii::$app->session->getFlash('pending') ."\",
        type: \"warning\",
        confirmButtonClass: \"btn-warning\"
	 });";
    $this->registerJs($js);
}
		
?>
<link rel="stylesheet" href="/css/sweetalert.css">
<link rel="stylesheet" href="/css/sweet-alert-animations.min.css">
<style>
    .modal-header {
        justify-content: left !important;
    }

    .modal-header .close {
        display: none;
    }
</style>
<?php
if (isset($designation) && in_array($designation->code,['RA','ADMIN']) && !in_array(Yii::$app->user->id,[2011])) {
    ?>
    <div class="col-md-12">
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">
                <div class="disbursement-rejected-index">
                    <h3 class="address-heading"><span class="fa fa-list"></span>
                        <?= Html::encode($this->title) ?></h3>
                    <?php echo $this->render('_search', ['searchModel' => $memberModel, 'types' => $types]); ?>
                </div>
            </div>
        </div>
    </div>
<?php }
?>
<?php
if (isset($memberProvider)) {
    if ($memberProvider->getTotalCount() > 0) {
        ?>
        <div class="col-md-12">
            <div class="container-fluid">
                <div class="box-typical box-typical-padding">
                    <div class="table-responsive">
                        <?= \kartik\grid\GridView::widget([
                            'id' => 'crud-datatable',
                            'dataProvider' => $memberProvider,
                            'pjax' => true,
                            'columns' => require(__DIR__ . '/_member_columns.php'),
                            'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                        ]); ?>
                        <?php if (isset($modelLoan)) { ?>
                            <?= Html::a('Reject', ['reject-disbursed-loan', 'id' => $modelLoan['id']],
                                ['role' => 'modal-remote', 'title' => 'Reject Disbursement', 'data-toggle' => 'modal', 'data-target' => '#ajaxCrudModal',
                                    'data-pjax' => '0', 'class' => 'btn btn-danger text-center', 'style' => 'float: right;width: 150px;margin-top:10px;height: 35px;']) ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } else {
        ?>
        <div class="col-md-12">
            <div class="container-fluid">
                <div class="box-typical box-typical-padding">
                    <div class="table-responsive">
                        <h3>No record found</h3>
                    </div>
                </div>
            </div>
        </div>
    <?php }
}
?>

<div class="col-md-12">
    <div class="container-fluid">
        <div class="box-typical box-typical-padding">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
//                    'filterModel' => $searchModel,
//                'pjax' => true,
                'columns' => require(__DIR__ . '/reject_columns.php'),
                'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
            ]); ?>
        </div>
    </div>
</div>


<?php
Modal::begin([
    "id" => "ajaxCrudModal",
    'size' => 'modal-lg',
    "header" => "<b>Temporary Reject Disbursement<b/>",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>

<script src="/js/sweetalert.min.js"></script>