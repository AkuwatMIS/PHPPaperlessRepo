<?php

use yii\bootstrap\Modal;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApplicationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Publish Loan';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
$js='
$(document).ready(function(){
  $(\'#BtnModalId\').click(function(e){    

    e.preventDefault();

    $(\'#your-modal\').modal(\'show\')

        .find(\'#modalContent\')

        .load($(this).attr(\'value\'));

   return false;
  });
  $("#applications-status").on(\'change\', function() {
    if(this.value=="rejected"){
      $("#applications-reject_reason").show();
    }
    else{
      $("#applications-reject_reason").hide();
    }
  });
  window.setTimeout(function() {
    $(".alert").fadeTo(3000, 0).slideUp(1000, function(){
        $(this).remove(); 
    });
}, 500);
if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
}
})';
$this->registerJs($js);

?>
<div class="container-fluid">
    <h6 class="address-heading"><!--<span class="glyphicon glyphicon-edit"></span>-->
        Publish Loan
    </h6>
    <div class="box-typical box-typical-padding">

        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions_by_id' => $regions_by_id,
        ]); ?>

        <?php if (Yii::$app->session->hasFlash('success')) { ?>
            <div style="margin-top:5%" class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                <h4><i class="icon fa fa-check"></i> Published!</h4>
                <?= Yii::$app->session->getFlash('success')[0] ?>
            </div>
        <?php }
        if (Yii::$app->session->hasFlash('error')) { ?>
            <div class="alert alert-danger alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                <h4><i class="icon fa fa-remove"></i> Error!</h4>
                <?= Yii::$app->session->getFlash('error')[0] ?>
            </div>
        <?php } ?>
        <div>
            <?= \yii\helpers\Html::button('Publish', [
                'class' =>'btn btn-success pull-right',
                'id' => 'BtnModalId',
                //'style'=>'margin-top: 30px',
                'data-toggle'=> 'modal',
                'data-target'=> '#your-modal',

            ]) ?>
        </div>
        <br>
        <br>

        <?=\yii\helpers\Html::beginForm(['disbursements/publish-loan'],'post');?>
        <?php if(!empty($dataProvider)){ ?>
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => require(__DIR__ . '/_columns.php'),
                'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items.
                     <div class="dropdown pull-right">
                        <button title="Export to CSV" class="btn btn-primary dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">
                        <i class="glyphicon glyphicon-export"></i>
                        <span class="caret"></span></button>
                        
                        <ul class="dropdown-menu pull-right" role="menu" style="height: 30px;" aria-labelledby="menu1">
                            <li role="presentation" >
                                            <input type="hidden" name="LoansSearch[project_id]" value="' . $data['searchModel']->project_id . '">
                                            <input type="hidden" name="LoansSearch[region_id]" value="' . $data['searchModel']->region_id . '">
                                            <input type="hidden" name="LoansSearch[area_id]" value="' . $data['searchModel']->area_id . '">
                                            <input type="hidden" name="LoansSearch[branch_id]" value="' . $data['searchModel']->branch_id . '">
                                            <input type="hidden" name="LoansSearch[bank]" value="' . $data['searchModel']->bank . '">
                        <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-default col-sm-12 btn-sm"
                                role="menuitem" tabindex="-1"><i
                                    class="text-primary glyphicon glyphicon-floppy-open"></i> CSV
                        </button>

                   
                            </li>
                        </ul>
                     </div>
                              ',
                'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
            ]); ?>
<?php } ?>
        </div>

        <?php

        Modal::begin([

            'header' => '',

            'id' => 'your-modal',

            'size' => 'modal-md',

        ]);

        echo "<div id='modalContent'>
        <div class='form-group'>
        <label class='control-label'>Are You Sure to publish selected loans</label>
        <!--<select required id='applications-status' class='form-control'  name='status'>
        <option value=''>Select Status</option>
        <option value='4'>Add To Fund Request</option>
        </select>-->
        
        </div>
        <div id='applications-reject_reason' style='display: none' class='form-group'>
        <label class='control-label'>Reject Reason</label>
        <input class='form-control' type='text' name='reject_reason'>
        </div>
        <button style=\"margin-top: 30px\" class=\"btn btn-success pull-right \">Yes</button>
        </div>";

        Modal::end();

        ?>
        <?= \yii\helpers\Html::endForm();?>
    </div>
</div>



