<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;
use kartik\export\ExportMenu;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Portfolio-Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span>Kpp Portfolio Report</h6>
        <?php
        echo $this->render('_search_portfolio', [
            'model' => $searchModel,
            'regions' => $regions,
            'branches' => $branches,
            'projects' => $projects,
            'provinces' => $provinces,

        ]);

        ?>
    </div>
</div>
        <?php Modal::begin([
            "id" => "ajaxCrudModal",
            "footer" => '',

        ]) ?>
        <?php Modal::end(); ?>
