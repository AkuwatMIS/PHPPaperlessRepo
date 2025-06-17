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

$this->title = 'Visit Images';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);

?>
<style>
    img {
        padding: 2%;
    }
    .user-card-row {
        width: 40%;
        font-size: .9999rem;
        margin-left: 20px;
        margin-top: 3px;
        margin-bottom: 2px;
.card-typical .card-typical-section{
padding-left: 10px;
padding-right: 10px;
}
</style>

<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-list"></span>Flood Visit Images </h6>

        <?php  echo $this->render('_search_flood',
            [
                'model' => $searchModel,
                'dataProvider' => $dataProvider,
                'regions' => $regions,
                'visitCount' => $visitCount,
                'disb_status' =>  $disb_status,
                'images_status'=> $images_status,
                'referrals'=> $referrals

            ]); ?>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'layout' => '{summary}<div class="container-fluid"><br>{items}</div> <div>{pager}</div>',
        'itemView' => '_data',
    ]);
    ?>
</div>

