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
                    ['attribute' => 'loan_amount', 'label' => 'Loan Amount','value'=>function($data){return number_format($data->loan_amount);}],
                    ['attribute' => 'date_disbursed', 'label' => 'Disbursement Date','value'=>function($data){return \common\components\Helpers\StringHelper::dateFormatter($data->date_disbursed);}],
                    ['attribute' => 'inst_months', 'label' => 'Installment Months','value'=>function($data){return number_format($data->inst_months);}],
                    ['attribute' => 'inst_amnt', 'label' => 'Installment Amount','value'=>function($data){return number_format($data->inst_amnt);}]
                ]
            ]);
        ?>
    </div>
</div>
