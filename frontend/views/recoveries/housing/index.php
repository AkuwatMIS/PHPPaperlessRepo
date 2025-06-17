<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\Search\RecoveriesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Recoveries';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="fa fa-bank"></span>
            Housing Recoveries List
           <!-- <?php /*if (in_array('frontend_add-bulkrecoveries', $permissions))
            { */?>
            <a href="/recoveries/add-bulk" class="btn btn-success pull-right" title="Post Recoveries">Post
                Recoveries</a></h6>
           --><?php /*} */?>
        </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'projects'=>$projects,
            'regions'=>$regions,
        ]); ?>
        <div class="table-responsive">
            <div class="dropdown" style="width: 10%">
                <button title="Export to CSV" class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-export"></i>
                    <span class="caret"></span></button>

                <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                    <li role="presentation" >
                        <form action="/recoveries/housing">
                            <input type="hidden" name="RecoveriesSearch[region_id]" value="<?php echo $searchModel->region_id ?>">
                            <input type="hidden" name="RecoveriesSearch[area_id]" value="<?php echo  $searchModel->area_id ?>">
                            <input type="hidden" name="RecoveriesSearch[branch_id]" value="<?php echo  $searchModel->branch_id ?>">
                            <input type="hidden" name="RecoveriesSearch[sanction_no]" value="<?php echo  $searchModel->sanction_no ?>">
                            <input type="hidden" name="RecoveriesSearch[member_name]" value="<?php echo  $searchModel->member_name ?>">
                            <input type="hidden" name="RecoveriesSearch[member_cnic]" value="<?php echo  $searchModel->member_cnic ?>">
                            <input type="hidden" name="RecoveriesSearch[receive_date]" value="<?php echo $searchModel->receive_date ?>">
                            <input type="hidden" name="RecoveriesSearch[amount]" value="<?php echo $searchModel->amount ?>">
                            <input type="hidden" name="RecoveriesSearch[charges_amount]" value="<?php echo $searchModel->charges_amount ?>">
                            <input type="hidden" name="RecoveriesSearch[credit_tax]" value="<?php echo $searchModel->credit_tax ?>">
                            <input type="hidden" name="RecoveriesSearch[receipt_no]" value="<?php echo  $searchModel->receipt_no ?>">
                            <input type="hidden" name="RecoveriesSearch[project_id]" value="<?php echo $searchModel->project_id ?>">
                            <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm" role="menuitem" tabindex="-1"><i class="text-primary glyphicon glyphicon-floppy-open"></i> CSV</button>

                        </form>
                    </li>
                </ul>
            </div>
                <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => require(__DIR__ . '/_columns.php'),
                    'pager' => [
                        'options'=>['class'=>'pagination'],   // set clas name used in ui list of pagination
                        'prevPageLabel' => 'Previous',   // Set the label for the "previous" page button
                        'nextPageLabel' => 'Next',   // Set the label for the "next" page button
                        'firstPageLabel'=>'First',   // Set the label for the "first" page button
                        'lastPageLabel'=>'Last',    // Set the label for the "last" page button
                        'nextPageCssClass'=>'next',    // Set CSS class for the "next" page button
                        'prevPageCssClass'=>'prev',    // Set CSS class for the "previous" page button
                        'firstPageCssClass'=>'first',    // Set CSS class for the "first" page button
                        'lastPageCssClass'=>'last',    // Set CSS class for the "last" page button
                        'maxButtonCount'=>10,    // Set maximum number of page buttons that can be displayed
                    ],
            ]); ?>

        </div>
    </div>
</div>
