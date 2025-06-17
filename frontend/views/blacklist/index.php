<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BlacklistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Blacklists';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-ban-circle"></span>
            Blacklist
                <a href="/blacklist/create" class="btn btn-success pull-right" title="Create Application">Add to Blacklist
                    </a>
        </h6>
        <div class="table-responsive">
            <div class="dropdown" style="width: 10%">
                <button title="Export to CSV" class="btn btn-default dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                    <i class="glyphicon glyphicon-export"></i>
                    <span class="caret"></span></button>

                <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                    <li role="presentation" >
                        <form action="/blacklist/index">
                            <input type="hidden" name="BlacklistSearch[name]" value="">
                            <input type="hidden" name="BlacklistSearch[parentage]" value="">
                            <input type="hidden" name="BlacklistSearch[cnic]" value="">
                            <input type="hidden" name="BlacklistSearch[cnic_invalid]" value="">
                            <input type="hidden" name="BlacklistSearch[type]" value="">
                            <input type="hidden" name="BlacklistSearch[province]" value="">
                            <input type="hidden" name="BlacklistSearch[reason]" value="">
                            <input type="hidden" name="BlacklistSearch[reject_reason]" value="">
                            <input type="hidden" name="BlacklistSearch[created_at]" value="">
                            <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm" role="menuitem" tabindex="-1"><i class="text-primary glyphicon glyphicon-floppy-open"></i> CSV</button>
                        </form>
                    </li>
                </ul>
            </div>

            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => require(__DIR__ . '/_columns.php'),
                'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                              ',
                'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
            ]); ?>
    </div>
</div>

<?php
Modal::begin([
    'header'=>'<h4></h4>',
    'id'=>'update-modal',
    'size'=>'modal-lg'
]);
echo "<div id='updateModalContent'>
<h4>Are you sure you want to remove this cnic from blacklist?</h4>
<br>
<form id='delete-form'  method='post'>
    <div class='row'>
         <div class=\"col-sm-6\">
            <label>Enter Reason</label>
            <input type='textbox' required name='reason' value='' class ='form-control form-control-sm'>
         </div>
         <div class=\"col-sm-6\">
             <div class='form-group'>
                 <button style='margin-top: 18px' type='submit' class='btn btn-success'>Delete</button>
             </div>
         </div>
    </div>
</form>
</div>";

Modal::end();
?>