<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Recoveries */
$this->title = $model->receipt_no;
$this->params['breadcrumbs'][] = ['label' => 'Recoveries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
    <div class="container-fluid">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h3>View Recovery</h3>
                    </div>
                </div>
            </div>
        </header>

        <div class="box-typical box-typical-padding">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    [
                        'attribute' => 'due_date',
                        'label' => 'Due Date',
                        'value'=>function($model){
                            return date('d M Y', $model->due_date);
                        }
                    ],
                    [
                        'attribute' => 'receive_date',
                        'label' => 'Receive Date',
                        'value'=>function($model){
                            return date('d M Y', $model->receive_date);
                        }
                    ],
                    [
                        'attribute' => 'amount',
                        'label' => 'Receive Amount',
                        'value'=>function($model){
                            return number_format($model->amount);
                        }
                    ],
                    'receipt_no',
                ],
            ]) ?>

        </div>
    </div>
<?php

\yii\bootstrap\Modal::begin([
    'header' => '<h4 class="modal-title">Logs</h4>',
    'headerOptions' => ['style' => ['display' => 'block']],
    'id' => 'model',
    'size' => 'model-lg',
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