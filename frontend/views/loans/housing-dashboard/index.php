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

$this->title = 'Housing Dashboard';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Housing Dashboard</h6>
        <?php
        echo $this->render('_search', [
            'model' => $data['searchModel'],
            'regions' => $data['regions'],
            'branches' => $data['branches'],
            'projects' => $data['projects'],
            'provinces' => $data['provinces'],

        ]);

        ?>
        <?php if (!empty($data['dataProvider'])) { ?>
            <div class="loans-index">
                <div id="ajaxCrudDatatable">
                </div>
            </div>
        <?php } ?>
    </div>
</div>