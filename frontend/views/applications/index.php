<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApplicationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Applications';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
            Applications List
            <?php if(in_array('frontend_createapplications',$permissions))
            { ?>
                <a href="/applications/create" class="btn btn-success pull-right" title="Create Application">Create Application</a>

            <?php }?>
        </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions_by_id' => $regions_by_id,
            'projects' => $projects,
        ]); ?>
        <?php if(!empty($dataProvider)) {?>
        <div class="table-responsive">
            <?= GridView::widget([
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
                                <form action="/applications/index">
                       
                                            <input type="hidden" name="ApplicationsSearch[full_name]" value="' . $searchModel->full_name . '">
                                            <input type="hidden" name="ApplicationsSearch[cnic]" value="' . $searchModel->cnic . '">
                                            <input type="hidden" name="ApplicationsSearch[application_no]" value="' . $searchModel->application_no . '">
                                            <input type="hidden" name="ApplicationsSearch[region_id]" value="' . $searchModel->region_id . '">
                                            <input type="hidden" name="ApplicationsSearch[area_id]" value="' . $searchModel->area_id . '">
                                            <input type="hidden" name="ApplicationsSearch[branch_id]" value="' . $searchModel->branch_id . '">
                                            <input type="hidden" name="ApplicationsSearch[team_id]" value="' . $searchModel->team_id . '">                                          
                                            <input type="hidden" name="ApplicationsSearch[field_id]" value="' . $searchModel->field_id . '">
                                            <input type="hidden" name="ApplicationsSearch[req_amount]" value="' . $searchModel->req_amount . '">
                                            <input type="hidden" name="ApplicationsSearch[status]" value="' . $searchModel->status . '">
                                            <input type="hidden" name="ApplicationsSearch[application_date]" value="' . $searchModel->application_date . '">
                                            <input type="hidden" name="ApplicationsSearch[created_at]" value="' . $searchModel->created_at . '">
                                            <input type="hidden" name="ApplicationsSearch[project_id]" value="' . $searchModel->project_id . '">
                                            <input type="hidden" name="ApplicationsSearch[nadra_verisys]" value="' . $searchModel->nadra_verisys . '">
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
            ]); ?>

        </div>
        <?php } else{ ?>
            <div class="table-responsive">
                <hr>
                <h3>Search Applications through above filters!</h3>
            </div>
        <?php } ?>
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
