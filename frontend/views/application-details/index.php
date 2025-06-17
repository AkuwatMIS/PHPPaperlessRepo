<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BranchesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'PMT';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
            PMT LIST </h6>

        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'dataProvider' => $dataProvider,
          //  'regions' => $regions,

        ]); ?>
    </div>
    <?php if(!empty($dataProvider)) {?>
    <div class="table-responsive">
        <div class="table-responsive">
            <?= GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'rowOptions' => function ($model) {
                    $now = time();
                    if (!empty($model->action_date) && $model->action_date != null) {
                        $differnce = $now - $model->action_date;
                        $days = Round($differnce / (60 * 60 * 24));
                        if ($days >= 13) {
                            return ['class' => 'danger'];
                        }
                    }

                },

                'columns' => require(__DIR__ . '/_columns.php'),
                'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right" style="display: none;">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret">"/application-details/export-pmt-index"</span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >      
                            <form action="/application_details/index">
                                <input type="hidden" name="ApplicationDetailsSearch[poverty_score]" value="' . $searchModel->poverty_score . '">
                                <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                                        role="menuitem" tabindex="-1"><i
                                            class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                                </button>
                            </form>
                            </li>
                        </ul>
                     </div>
                              ',
                'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
            ]) ?>
        </div>
        <?php }else{ ?>
            <div class="table-responsive">
                <hr>
                <h3>Search PMT through above filters!</h3>
            </div>
        <?php } ?>
    </div>
</div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
