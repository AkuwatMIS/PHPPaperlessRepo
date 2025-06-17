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

$this->title = 'Annual work plan';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>

<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
            <?= Html::encode($this->title) ?>
        </h6>
        <?php
        echo $this->render('_search', [
            'model' => $searchModel,
            'regions'=>$regions,
            'areas'=>$areas,
            'branches'=>$branches,
            'projects'=>$projects

        ]);

        ?>
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'tableOptions' =>['class' => 'table table-striped table-bordered table-hover'],
            'tableOptions' =>['class' => 'table table-bordered table-hover','style'=>'background-color:White;color:Black'/*;font-size:28px'*/],
            // 'filterModel' => $filterModel,
            'showFooter' => true,
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
                [   'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'branch_id',
                    'value'=>'branch.name',
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
                    'label'=>'Active Loans as on 30 June,2018',
                    //'format'=>'decimal',
                    'footer' => '<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'active_loans_last').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'olp_last',
                    'label'=>'Olp as on 30 June,2018',
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
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'header'=>'<b style="color: White">No of Loans</b>',
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'amount_disbursed',
                    'label'=>'Total Disbursement Amount',
                    'value'=>function($data){return isset($data->amount_disbursed)?number_format($data->amount_disbursed):0;},
                    // 'format'=>'decimal',
                    'footer' => '<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'disbursement_amount').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'header'=>'<b style="color: White">Total Disbursement Amount</b>',
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'monthly_recovery',
                    'label'=>'Total Recovery',
                    //'format'=>'decimal',
                    'value'=>function($data){return isset($data->monthly_recovery)?number_format($data->monthly_recovery):0;},
                    'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'monthly_recovery').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'header'=>'<b style="color: White">Total Recovery</b>',
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'funds_required',
                    'label'=>'Total Funds Require',
                    //'format'=>'decimal',
                    'value'=>function($data){return isset($data->funds_required)?number_format($data->funds_required):0;},
                    'footer' =>'<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'funds_required').'<b>',
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'header'=>'<b style="color: White">Total Funds Require</b>',
                ],[
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'active_loans_current',
                    'label'=>'Active Loans as on 30 June,2019',
                    //'format'=>'decimal',
                    'value'=>function($data){
                        return number_format(($data->active_loans_current-$data->monthly_closed_loans_last) +$data->no_of_loans_last);
                    },
                    'footer' => \common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'active_loans_current'),
                    'footerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                    'headerOptions'=>['style'=>'background-color:#5A738E;color:White'],
                ],
                [
                    'class'=>'\kartik\grid\DataColumn',
                    'attribute'=>'olp_current',
                    'label'=>'Active Loans as on 30 June,2019',
                    //'format'=>'decimal',
                    'value'=>function($data){
                        return number_format(($data->olp_current-$data->monthly_recovery_last)+$data->amount_disbursed_last);
                    },
                    'footer' => '<b>'.\common\components\Helpers\AwpHelper::getTotal($dataProvider->getModels(), 'olp_current').'<b>',
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
<?php Modal::begin([
    "id"=>"ajaxCrudModal",
    "footer"=>"",// always need it for jquery plugin
])?>
<?php Modal::end(); ?>
    </div></div>