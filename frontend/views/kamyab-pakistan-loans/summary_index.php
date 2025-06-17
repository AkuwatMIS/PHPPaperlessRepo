<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\VigaLoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'NADRA Verisys Summary Report';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <a class="pull-right btn btn-primary" href="/kamyab-pakistan-loans/index">NADRA Verisys Detailed Report</a>
        <h6 class="address-heading"><span class="glyphicon glyphicon-list"></span>
            NADRA Verisys Summary Report</h6>
        <?php  echo $this->render('summary_search', [
            'model' => $searchModel,
            'regions' => $regions,
            'projects' => $projects,
            ]); ?>
        <br><br>
        <?php if(!empty($result)) {?>
        <div class="row">
            <div class="col-sm-4">
                <article class="statistic-box green">
                    <div>
                        <div class="number"><?= $result['verified']  ?></div>
                        <?php
                        ?>
                        <div class="caption"><div>Verified Cases</div></div>

                    </div>
                </article>
            </div>
            <div class="col-sm-4">
                <article class="statistic-box red">
                    <div>
                        <div class="number"><?= $result['unverified']  ?></div>
                        <?php
                        ?>
                        <div class="caption"><div>Unverified Cases</div></div>

                    </div>
                </article>
            </div>
            <div class="col-sm-4">
                <article class="statistic-box purple">
                    <div>
                        <div class="number"><?= $result['verified']+$result['unverified']  ?></div>
                        <div class="caption"><div>Total Cases</div></div>
                        <div class="percent">
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </div>
    <?php } else{ ?>
        <div class="table-responsive">
            <hr>
            <h3>Search through above filters! Application Date Required</h3>
        </div>
    <?php } ?>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
