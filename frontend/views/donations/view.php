<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Donations */
?>
<div class="donations-view">
    <div class="donations-logs">
        <?php if(isset($model->donationsLogs) && !empty($model->donationsLogs)){
            echo  Html::button('Logs', ['id' => 'modelButton', 'value' => \yii\helpers\Url::to(['donations/logs','id' => $model->id ]), 'class' => 'btn btn-success pull-righ']) ; } ?>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'application_id',
            'loan_id',
            'recovery_id',
            'branch_id',
            'project_id',
            'credit',
            'debit',
            'recv_date',
            'receipt_no',
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