<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\RolesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Roles';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="roles-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Roles', ['create-role'], ['class' => 'btn btn-success']) ?>

        <?= Html::a('File', ['file','type' => 'a','file_name' => '5b17c72059c94.png'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            /*'id',
            'date_entered',
            'date_modified',
            'modified_user_id',*/
            //'created_by',
            'name',
            'description:ntext',
            //'deleted',

            [
                    'class' => 'yii\grid\ActionColumn',
                'template' => '{Permissions}{Api_permissions}',
                'buttons' => [
                    'Permissions'=> function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-send"></span>', ['permission', 'name'=>$model->name],['title'=>"Set Permissions"]);
                    },
                    'Api_permissions'=> function ($url, $model, $key) {
                        return Html::a('<span class="glyphicon glyphicon-phone"></span>', ['permission-api', 'name'=>$model->name],['title'=>"Set Api Permissions"]);
                    },
                    ],
            ],
        ],
    ]); ?>
</div>
