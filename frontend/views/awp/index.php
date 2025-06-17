<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset; 
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\AwpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Annual Work Plan 2022-2023';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="awp-index">
   <!-- <div id="ajaxCrudDatatable">-->
        <!--<h3><?/*= Html::encode($this->title) */?></h3>-->

        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'tableOptions' =>['class' => 'table table-striped table-bordered table-hover'],
            'tableOptions' =>['class' => 'table table-bordered table-hover','style'=>'background-color:White;color:Black;font-size:23px'],
            // 'filterModel' => $filterModel,
            'rowOptions'=>function($model){
                if($model->branch->status==0){
                    return ['style' => 'display:none'];
                }else if (in_array($model->branch->id,\common\components\Helpers\AwpHelper::getClosedOlpBranches())){
                    return ['style' => 'background-color:red'];
                }/*else if($model->branch->type != 'branch'){
                return ['style' => 'background-color:yellow'];
            }*/
            },
            'showFooter' => true,
//            'rowOptions'=>function($model){
//                /*if($model->branch->type != 'branch'){
//                    return ['style' => 'background-color:yellow'];
//                }*/
//                if($model->branch->status==0){
//                    return ['style' => 'display:none'];
//                }
//            },

            'columns' => [
                /*[
                    'class' => 'kartik\grid\CheckboxColumn',
                    'width' => '20px',
                ],
                [
                    'class' => 'kartik\grid\SerialColumn',
                    'width' => '30px',
                ],*/
                // [
                // 'class'=>'\kartik\grid\DataColumn',
                // 'attribute'=>'id',
                // ],
                ['attribute'=>'branch_id',
                    'format' => 'raw',
                    'value' => function ($data) {

                        if (in_array($data->branch->code,\common\components\Helpers\AwpHelper::getAgriBranches())) {
                            return '<i style="color:green;" class="fa fa-leaf"></i>'. ' ' . $data->branch->name;
                            //return '<i style="color:white;" class="fa fa-house"></i>'. ' ' . $data->branch->code;
                        }else if($data->branch->type != 'branch'){
                            return '<i style="color:green;" class="fa fa-home"></i>'. ' ' . $data->branch->name;
                        }else{
                            return $data->branch->name;
                        }
                    },
                    'label'=>'Branch Name',
                    'footer'=>'<b>Total</b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'header'=>'<b style="color: White">Branch Name</b>',
                ],
               /* [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'project_id',
                ],*/
                /*[
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'month',
                ],*/

                 /*[
                 'class'=>'\kartik\grid\DataColumn',
                 'attribute'=>'active_loans',
                     'footer' => \common\components\AwpHelper::getTotal($dataProvider->getModels(), 'active_loans'),
                 ],
                 [
                 'class'=>'\kartik\grid\DataColumn',
                 'attribute'=>'monthly_olp',
                  'footer' => \common\components\AwpHelper::getTotal($dataProvider->getModels(), 'monthly_olp'),
                 ],*/
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'active_loans_last',
                    'value'=>function($data){return number_format($data->active_loans_last);},
                    'label'=>'Active Loans as on 30 June,2022',
                    //'format'=>'decimal',
                    'footer' => '<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'active_loans_last').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'olp_last',
                    'label'=>'OLP as on 30 June,2022',
                    //'format'=>'decimal',
                    'value'=>function($data){return number_format($data->olp_last);},
                    'footer' => '<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'olp_last').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'no_of_loans',
                    'label'=>'No of Loans',
                    'value'=>function($data){return number_format($data->no_of_loans);},
                    'footer' =>\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'no_of_loans'),
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#0f6742;color:White'],
                    'header'=>'<b style="color: White">Expected No of Loans</b>',
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'amount_disbursed',
                    'label'=>'Total Disbursement Amount',
                    'value'=>function($data){return isset($data->disbursement_amount)?number_format($data->disbursement_amount):0;},
                    // 'format'=>'decimal',
                    'footer' => '<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'disbursement_amount').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#0f6742;color:White'],
                    'header'=>'<b style="color: White">Expected Disbursement Amount</b>',
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'monthly_recovery',
                    'label'=>'Total Recovery',
                    //'format'=>'decimal',
                    'value'=>function($data){return isset($data->monthly_recovery)?number_format($data->monthly_recovery):0;},
                    'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'monthly_recovery').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#0f6742;color:White'],
                    'header'=>'<b style="color: White">Expected Recovery</b>',
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'funds_required',
                    'label'=>'Total Funds Require',
                    //'format'=>'decimal',
                    'value'=>function($data){return number_format($data->disbursement_amount-$data->monthly_recovery);},
                    'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotalTwoColumns($dataProvider->getModels(), 'disbursement_amount','monthly_recovery').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#0f6742;color:White'],
                    'header'=>'<b style="color: White">Expected Funds Required</b>',
                ],[
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'active_loans_current',
                    'label'=>'Expected Active Loans as on 30 June,2023',
                    //'format'=>'decimal',
                    'value'=>function($data){
                        return number_format(($data->active_loans_current-$data->monthly_closed_loans_last) +$data->no_of_loans_last);
                    },
                    'footer' => \common\components\Helpers\AwpHelper::getTotalthree($dataProvider->getModels(), 'active_loans_current','monthly_closed_loans_last','no_of_loans_last'),
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'olp_current',
                    'label'=>'Expected OLP as on 30 June,2023',
                    //'format'=>'decimal',
                    'value'=>function($data){
                        return number_format(($data->olp_current-$data->monthly_recovery_last)+$data->amount_disbursed_last);
                    },
                    'footer' => '<b>'.\common\components\Helpers\AwpHelper::getTotalthree($dataProvider->getModels(), 'olp_current','monthly_recovery_last','amount_disbursed_last').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                ],
                 /*[
                 'class'=>'\kartik\grid\DataColumn',
                 'attribute'=>'avg_loan_size',

                 ],*/
                 /*[
                 'class'=>'\kartik\grid\DataColumn',
                 'attribute'=>'monthly_closed_loans',
                 'footer' => \common\components\AwpHelper::getTotal($dataProvider->getModels(), 'monthly_closed_loans'),
                 ],*/

                 /*[
                 'class'=>'\kartik\grid\DataColumn',
                 'attribute'=>'avg_recovery',
                 'footer' =>\common\components\AwpHelper::getTotal($dataProvider->getModels(), 'avg_recovery')
                 ],*/

            ],
        ]); ?>
    <!--</div>
    </div>-->
</div>
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
