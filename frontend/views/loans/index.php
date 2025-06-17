<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Loans';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="font-icon glyphicon glyphicon-tag"></span>
            Loans List
            <?php if(in_array('frontend_lacloans',$permissions))
            { ?>
            <a href="/loans/lac" class="btn btn-success pull-right" title="Create Member">LAC</a></h6>

            <?php }?>

        <?php echo $this->render('_search', [
            'model' => $data['searchModel'],
            'regions_by_id' => $data['regions_by_id'],
            'projects'=>$data['projects'],
        ]); ?>
        <?php
        $dataProvider=$data['dataProvider'];
        $searchModel=$data['searchModel'];
        ?>
        <?php if(!empty($dataProvider)) {?>
            <div class="table-responsive">
                <?= GridView::widget([
                    'dataProvider' => $data['dataProvider'],
                    'filterModel' => $data['searchModel'],
                    'columns' => require(__DIR__ . '/_columns.php'),
                    'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                <form action="/loans/index">
                       
                                            <input type="hidden" name="LoansSearch[member_name]" value="' . $data['searchModel']->member_name . '">
                                            <input type="hidden" name="LoansSearch[member_cnic]" value="' . $data['searchModel']->member_cnic . '">
                                            <input type="hidden" name="LoansSearch[application_no]" value="' . $data['searchModel']->application_no . '">
                                            <input type="hidden" name="LoansSearch[sanction_no]" value="' . $data['searchModel']->sanction_no . '">
                                            <input type="hidden" name="LoansSearch[loan_amount]" value="' . $data['searchModel']->loan_amount . '">
                                            <input type="hidden" name="LoansSearch[inst_amnt]" value="' . $data['searchModel']->inst_amnt . '">
                                            <input type="hidden" name="LoansSearch[inst_months]" value="' . $data['searchModel']->inst_months . '">                                          
                                            <input type="hidden" name="LoansSearch[date_disbursed]" value="' . $data['searchModel']->date_disbursed . '">
                                            <input type="hidden" name="LoansSearch[group_no]" value="' . $data['searchModel']->group_no . '">
                                            <input type="hidden" name="LoansSearch[status]" value="' . $data['searchModel']->status . '">
                                            <input type="hidden" name="LoansSearch[project_id]" value="' . $data['searchModel']->project_id . '">
                                            <input type="hidden" name="LoansSearch[region_id]" value="' . $data['searchModel']->region_id . '">
                                            <input type="hidden" name="LoansSearch[area_id]" value="' . $data['searchModel']->area_id . '">
                                            <input type="hidden" name="LoansSearch[branch_id]" value="' . $data['searchModel']->branch_id . '">
                                            <input type="hidden" name="LoansSearch[team_id]" value="' . $data['searchModel']->team_id . '">                                            
                                            <input type="hidden" name="LoansSearch[project_id]" value="' . $searchModel->project_id . '">
                                            <input type="hidden" name="LoansSearch[field_id]" value="' . $data['searchModel']->field_id . '">

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
                <h3>Search Loans through above filters!</h3>
            </div>
        <?php } ?>

    </div>
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
