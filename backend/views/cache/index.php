<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\CitiesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Cache';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container">
    <h2>Cache Clear</h2>
    <table class="table table-bordered">
        <tr>
            <th>Clear DB Cache</th>
            <td><a href="/cache/clear-db">Clear DB Cache</a></td>
        </tr>
        <tr>
            <th>Clear Schema Cache</th>
            <td></td>
        </tr>
    </table>
</div>
