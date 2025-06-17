<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Operations */
?>
<div class="operations-view">
    <div class="operations-logs">
        <?php if(isset($model->operationsLogs) && !empty($model->operationsLogs)){
            echo  Html::button('Logs', ['id' => 'modelButton', 'value' => \yii\helpers\Url::to(['operations/logs','id' => $model->id ]), 'class' => 'btn btn-success pull-righ']) ; } ?>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'loan_id',
            'operation_type_id',
            'credit',
            'application_no',
            'receipt_no',
            'receive_date',
            'branch_id',
            'team_id',
            'field_id',
            'transaction_id',
            'project_id',
            'region_id',
            'area_id',
            'deleted',
            'assigned_to',
            'created_by',
            'updated_by',
            'created_at',
            'updated_at',
        ],
    ]) ?>

</div>
<?php

\yii\bootstrap\Modal::begin([
    'header' => '<h4 class="modal-title">Logs</h4>',
    'headerOptions' => ['style' => ['display' => 'block']],
    'id'     => 'model',
    'size'   => 'model-lg',
    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]),
]);

echo "<div id='modelContent'></div>";

\yii\bootstrap\Modal::end();

?>
<?php
$script = "$(function(){
    $('#modelButton').click(function(){
        $('.modal').modal('show')
            .find('#modelContent')
            .load($(this).attr('value'));
    });
});";
$this->registerJs($script);
?>