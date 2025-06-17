
<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Users';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <div class="box-typical box-typical-padding">
            <h6 class="address-heading"><span class="glyphicon glyphicon-user"></span>
                Users List <a href="/user-management/transfers/list" class="btn btn-success pull-right" title="Transfer List">User Transfer List</a></h6>
            <div class="table-responsive">

        <?=GridView::widget([
            'id'=>'crud-datatable',
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => require(__DIR__ . '/_columns.php'),
            'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                <form action="/users/index">
                                <input type="hidden" name="UsersSearch[username]" value="' . $searchModel->username . '">
                                <input type="hidden" name="UsersSearch[fullname]" value="' . $searchModel->fullname . '">
                                <input type="hidden" name="UsersSearch[father_name]" value="' . $searchModel->father_name . '">
                                <input type="hidden" name="UsersSearch[email]" value="' . $searchModel->email . '">
                                <input type="hidden" name="UsersSearch[emp_code]" value="' . $searchModel->emp_code . '">
                                <input type="hidden" name="UsersSearch[cnic]" value="' . $searchModel->cnic . '">
                                <input type="hidden" name="UsersSearch[city_id]" value="' . $searchModel->city_id . '">
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
        ])?>
    </div>
        </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
