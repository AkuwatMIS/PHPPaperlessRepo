<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\SocialAppraisalSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Social Appraisals';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="font-icon font-icon-build"></span>
            Social Appraisal List <a href="/social-appraisal/create" class="btn btn-success pull-right" title="Create Social Appraisal">Create Social Appraisal</a></h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions'=>$regions
        ]); ?>
        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,

            'columns' => require(__DIR__.'/_columns.php'),

        ])?>
    </div>
</div>
    </div></div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
