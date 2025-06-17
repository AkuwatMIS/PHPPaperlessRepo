<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\DisbursementDetailsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Published Manual';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>

<div class="container-fluid">



    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Published Manual </h6>
        <?php echo $this->render('_searchDisbursementDetail', [
            'model' => $searchModel,
            'bank_names' => $bank_names,
            'branches_names' => $branches_names,
            'regions' => $regions,
            'projects' => $projects
        ]); ?>
        <?php if (!empty($dataProvider)) { ?>
            <?= Html::beginForm(['disbursement-details/published'], 'post'); ?>
            <div class="row">
                <div class="col-md-12">
                    <?=Html::submitButton('InProcess', ['class' => 'btn btn-info','style'=>'float:right']);?>
                </div>
            </div>

            <div class="disbursement-details-index">
                <div id="ajaxCrudDatatable">
                    <?= GridView::widget([
                        'id' => 'crud-datatable',
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'pjax' => true,
                        'columns' => require(__DIR__ . '/_columns.php'),
                        'summary' => '
         Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
               ',
                    ]) ?>
                </div>
            </div>
            <?= Html::endForm(); ?>
        <?php } else { ?>
            <hr>
            <h3>Select Region/Project First!</h3>
        <?php } ?>
        <?php Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => "",// always need it for jquery plugin
        ]) ?>
        <?php Modal::end(); ?>
    </div>
</div>
