<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoanTranchesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Loan Tranches';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Loan Tranches</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="loan-tranches-index">
            <?php echo $this->render('_tranche_search_column', [
                'model' => $searchModel,
            ]); ?>
            <div id="ajaxCrudDatatable" style="margin-top: 1em">
                <?php if (!empty($dataProvider)) { ?>
                    <?= GridView::widget([
                        'id' => 'crud-datatable',
                        'dataProvider' => $dataProvider,
                        'pjax' => true,
                        'columns' => require(__DIR__ . '/_tranche_columns.php'),
                        'toolbar' => [
                            ['content' =>
                                Html::a('<i class="glyphicon glyphicon-plus"></i>', ['composite-create-loan-tranche'],
                                    ['role' => 'modal-remote', 'title' => 'Create new Loan Tranches', 'class' => 'btn btn-default']) .
                                Html::a('<i class="glyphicon glyphicon-repeat"></i>', [''],
                                    ['data-pjax' => 1, 'class' => 'btn btn-default', 'title' => 'Reset Grid'])
                            ],
                        ],
                        'striped' => true,
                        'condensed' => true,
                        'responsive' => true,
                        'panel' => [
                            'type' => 'default',
                            'heading' => '<i class="glyphicon glyphicon-list"></i> Loan Tranches',
                            '<div class="clearfix"></div>',
                        ]
                    ]) ?>
                <?php } else { ?>
                    <div class="table-responsive">
                        <hr>
                        <h3>Search Loan Tranches through above filters!</h3>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
