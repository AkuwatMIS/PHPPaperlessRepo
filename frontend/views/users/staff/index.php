<?php

use common\components\Helpers\ImageHelper;use johnitvn\ajaxcrud\CrudAsset;
use \fruppel\googlecharts\GoogleCharts;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\widgets\ListView;
//use yii\grid\GridView;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Staff';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);

?>
<style>
.card-typical:hover {
    box-shadow: 0 0 11px rgba(33,33,33,.4);
}
.card-typical .card-typical-section{
height:140px;
padding-left: 10px;
padding-right: 10px;
}
</style>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span> Staff Report</h6>

        <?php  echo $this->render('_search',
            [
                'model' => $searchModel,
                'regions' =>$regions,
                'designations' => $designations
            ]);
        ?>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'layout' => '<div class="container-fluid">{summary}<br>{items}</div> <div>{pager}</div>',
        'itemView' => '_data',
    ]);
    ?>
    <br>


