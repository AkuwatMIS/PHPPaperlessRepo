<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ProjectChargesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Projects';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
CrudAsset::register($this);
?>
    <div class="container-fluid">
        <div class="box-typical box-typical-padding">
            <?php if (Yii::$app->session->hasFlash('error')): ?>
                <div id="flash-error" class="alert alert-danger" role="alert">
                    <?= Yii::$app->session->getFlash('error') ?>
                </div>

                <?php
                $this->registerJs("
                    setTimeout(function() {
                        $('#flash-error').fadeOut('slow');
                    }, 3000); // Hide after 3 seconds
                ");
                ?>
            <?php endif; ?>

            <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
                Projects</h6>

            <div class="table-responsive">
                <?= GridView::widget([
                    'id' => 'crud-datatable',
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'pjax' => true,
                    'columns' => require(__DIR__ . '/_columns.php'),
                    'toolbar' => [
                        ['content' =>

                            Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                                ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid']) .
                            '{toggleData}' .
                            '{export}'
                        ],
                    ],

                    'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                ]); ?>
            </div>
        </div>
    </div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>