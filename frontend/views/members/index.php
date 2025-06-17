<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\Members1Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Members';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-user"></span>
            Members List
            <?php if(in_array('frontend_createmembers',$permissions))
            { ?>
                <a href="/members/create" class="btn btn-success pull-right" title="Create Member">Create Member</a>

            <?php }?>
        </h6>

        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions' => $regions,
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
                                <form action="/members/index">
                       
                                            <input type="hidden" name="MembersSearch[full_name]" value="' . $searchModel->full_name . '">
                                            <input type="hidden" name="MembersSearch[parentage]" value="' . $searchModel->parentage . '">
                                            <input type="hidden" name="MembersSearch[cnic]" value="' . $searchModel->cnic . '">
                                            <input type="hidden" name="MembersSearch[cnic_issue_date]" value="' . $searchModel->info->cnic_issue_date . '">
                                            <input type="hidden" name="MembersSearch[cnic_expiry_date]" value="' . $searchModel->info->cnic_expiry_date . '">
                                            <input type="hidden" name="MembersSearch[dob]" value="' . $searchModel->dob . '">
                                            <input type="hidden" name="MembersSearch[education]" value="' . $searchModel->education . '">
                                            <input type="hidden" name="MembersSearch[marital_status]" value="' . $searchModel->marital_status . '">
                                            <input type="hidden" name="MembersSearch[status]" value="' . $searchModel->status . '">
                                            <input type="hidden" name="MembersSearch[region_id]" value="' . $searchModel->region_id . '">
                                            <input type="hidden" name="MembersSearch[area_id]" value="' . $searchModel->area_id . '">
                                            <input type="hidden" name="MembersSearch[branch_id]" value="' . $searchModel->branch_id . '">
                                            <input type="hidden" name="MembersSearch[team_id]" value="' . $searchModel->team_id . '">
                                            <input type="hidden" name="MembersSearch[field_id]" value="' . $searchModel->field_id . '">

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
            <h3>Search Members through above filters!</h3>
        </div>
        <?php } ?>
    </div>
</div>
<?php Modal::begin([
    "id" => "ajaxCrudModal",
    "footer" => "",// always need it for jquery plugin
]) ?>
<?php Modal::end(); ?>
