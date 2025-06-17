<?php
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="borrowers-index">
    <div id="ajaxCrudDatatable">
        <?php
            echo DetailView::widget([
                'model'=>$model,
                'attributes'=>[
                    ['attribute' => 'application_no', 'label' => 'Application No'],
                    ['attribute' => 'req_amount', 'label' => 'Requested Amount','value'=>function($data){return number_format($data->req_amount);}]
                ]
            ]);
        ?>
    </div>
</div>
