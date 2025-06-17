<?php

use yii\bootstrap\Modal;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ApplicationsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Remove from Fund Request';
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
  if ( window.history.replaceState ) {
  window.history.replaceState( null, null, window.location.href );
  }
})';
$this->registerJs($js);

?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
            Remove from Fund Request List
        </h6>
        <?php echo $this->render('_search', [
            'model' => $searchModel,
            'regions_by_id' => $regions_by_id,
        ]); ?>

        <div>
            <?= \yii\helpers\Html::button('Remove From Fund Request', [
                'class' =>'btn btn-success',
                'id' => 'BtnModalId',
                'style'=>'margin-top: 30px',
                'data-toggle'=> 'modal',
                'data-target'=> '#your-modal',

            ]) ?>
        </div>


        <?=\yii\helpers\Html::beginForm(['loans/remove-fund-request'],'post');?>
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                //'filterModel' => $searchModel,
                'columns' => require(__DIR__ . '/_columns.php'),
                'summary' => '
                     Showing <strong>{begin}</strong>-<strong>{end}</strong> of <strong>{totalCount}</strong> items. ',
                'tableOptions' => ['class' => 'table table-bordered table-hover table-xs'],
            ]); ?>

        </div>

        <?php

        Modal::begin([

            'header' => '',

            'id' => 'your-modal',

            'size' => 'modal-md',

        ]);

        echo "<div id='modalContent'>
        <div class='form-group'>
        <label class='control-label'>Are You Sure to remove these loans from fund request</label>
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



