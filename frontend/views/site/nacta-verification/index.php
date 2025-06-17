<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
//$limit=($dataProvider->count);

/*$this->title = 'Home';*/
$this->params['breadcrumbs'][] = $this->title;

?>

<?php

echo $this->render('_search', [
    'searchModel' => $searchModel,
    'types' => $types
]);
if (isset($dataProvider)) {
    if ($dataProvider->getTotalCount() > 0) {
        ?>
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">

                <div class="table-responsive">
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                       // 'filterModel' => $searchModel,
                        'columns' => require(__DIR__ . '/_columns.php'),
                        'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
                    ]); ?>

                </div>
            </div>
        </div>
    <?php } else {
        ?>
        <div class="container-fluid">
            <div class="box-typical box-typical-padding">

                <div class="table-responsive">
                    <h3>No record found</h3>
                </div>
            </div>
        </div>

    <?php }
}
Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end();
?>


