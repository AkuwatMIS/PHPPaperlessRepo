<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApplicationsCibSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Applications Cibs';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="applications-cib-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Applications Cib', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'application_id',
            'cib_type_id',
            'fee',
            'receipt_no',
            //'status',
            //'type',
            //'file_path',
            //'response:ntext',
            //'transfered',
            //'created_by',
            //'created_at',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
