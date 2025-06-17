<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\GroupsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Groups';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="font-icon glyphicon glyphicon-cloud"></span>
            Groups List
            <?php if(in_array('frontend_creategroups',$permissions))
            { ?>
            <a href="/groups/create" class="btn btn-success pull-right" title="Group Formation">Group Formation</a></h6>
        <?php }?>

        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions_by_id' => $regions_by_id,
        ]); ?>
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
                                <form action="/groups/index">
                       
                                            <input type="hidden" name="GroupsSearch[grp_no]" value="' . $searchModel->grp_no . '">
                                            <input type="hidden" name="GroupsSearch[group_name]" value="' . $searchModel->group_name . '">
                                            <input type="hidden" name="GroupsSearch[grp_type]" value="' . $searchModel->grp_type . '">
                                            <input type="hidden" name="GroupsSearch[region_id]" value="' . $searchModel->region_id . '">
                                            <input type="hidden" name="GroupsSearch[area_id]" value="' . $searchModel->area_id . '">
                                            <input type="hidden" name="GroupsSearch[branch_id]" value="' . $searchModel->branch_id . '">
                                            <input type="hidden" name="GroupsSearch[team_id]" value="' . $searchModel->team_id . '">
                                            <input type="hidden" name="GroupsSearch[field_id]" value="' . $searchModel->field_id . '">

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
    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
