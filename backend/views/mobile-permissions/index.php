<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\MobilePermissionsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Mobile Permissions';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="mobile-permissions-index">
    <div id="ajaxCrudDatatable">
        <h1><?= Html::encode($this->title) ?></h1>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p>
            <?= Html::a('Create Roles', ['create-role'], ['class' => 'btn btn-success']) ?>
        </p>

        <?= GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'name',
                'description:ntext',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '{Permissions}',
                    'buttons' => [
                        'Permissions'=> function ($url, $model, $key) {
                            return Html::a('<span class="glyphicon glyphicon-send"></span>', ['create', 'name'=>$model->name],['role'=>'modal-remote','data-toggle'=>'tooltip','title'=>"Set Permissions"]);
                        },
                    ],
                ],
            ],
        ]); ?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>