<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use kartik\daterange\DateRangePicker;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\search\AreasSearch */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="container-fluid collapse border-0" id="demo">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>
    <div class="row">
        <div class="col-sm-3">
            <?php
            //$regions = ArrayHelper::map(Regions::find()->asArray()->all(), 'id', 'name');
            //  $regions = ArrayHelper::map(\common\models\Regions::find()->asArray()->all(), 'id', 'name');
            echo $form->field($model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region');

            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->area_id) ? $model->area_id : null;
            echo $form->field($model, 'area_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['takafulpaidsearch-region_id'],
                    'initialize' => true,
                    'initDepends' => ['takafulpaidsearch-region_id'],
                    'placeholder' => 'Select Area',
                    'url' => Url::to(['/structure/fetch-area-by-region'])
                ],
                'data' => $value ? [$model->area_id => $value] : []
            ])->label('Area');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            $value = !empty($model->branch_id) ? $model->branch_id : null;
            echo $form->field($model, 'branch_id')->widget(DepDrop::classname(), [
                'pluginOptions' => [
                    'depends' => ['takafulpaidsearch-area_id'],
                    'initialize' => true,
                    'initDepends' => ['takafulpaidsearch-area_id'],
                    'placeholder' => 'Select Branch',
                    'url' => Url::to(['/structure/fetch-branch-by-area'])
                ],
                'data' => $value ? [$model->branch_id => $value] : []
            ])->label('Branch');
            ?>

        </div>
        <div class="col-sm-3">
            <?php
            //  $projects = ArrayHelper::map(\common\models\Projects::find()->asArray()->all(), 'id', 'name');

            echo  $form->field($model, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label("Project")
            //        echo $form->field($model, 'status')->label("Status")->dropDownList( ['0' => 'Pending', '1' => 'Processed'],['prompt' => 'Select Status'])

            // echo $form->field($model, 'status')->label(" Status")->dropDownList( ['prompt' => 'Select Status','value[0]'=>'Pending','value[1]'=>'Processed'])?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'receive_date')->widget(DateRangePicker::classname(), [
                'convertFormat' => true,
                'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Takaful Receive Date'],
                'pluginOptions' => [
                    'locale' => [
                        'format' => 'Y-m-d',
                    ]
                ]
            ])->label(" Takaful Receive Date");

            //  echo  $form->field($model, 'status')->dropDownList(['pending' => 'Pending', 'approved' => 'Approved'], ['prompt' => 'Select Status'])
            // echo $form->field($model, 'action_date')->widget(DateRangePicker::classname(), [
            //   'convertFormat'=>true,
            //  'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'PMT Lock  Date'],
            //  'pluginOptions'=>[
            //      'locale'=>[
            //        'format'=>'Y-m-d',
            //     ]
            // ]
            //])->label("PMT Lock Date");
            ?>


        </div>
        <div class="col-sm-3">


        </div>

        <div class="col-sm-3">

        </div>

        <!--<div class="col-sm-3">
            <?/*= $form->field($model, 'loan_amount')->label("Loan Amount")->textInput(['placeholder'=>'Loan Amount', 'class'=>'form-control form-control-sm']) */?>
        </div>-->
        <div class="col-sm-3">
            <?php
            /* echo   \kartik\field\FieldRange::widget([
                 'name1'=>'LoansSearch[loan_amnt_frm]',
                 'name2'=>'LoansSearch[loan_amnt_to]',
                 'value1'=>isset($model->loan_amnt_frm)?$model->loan_amnt_frm:'',
                 'value2'=>isset($model->loan_amnt_to)?$model->loan_amnt_to:'',
                 'label'=>'Loan Amount',
                 // 'form' => $form,
                 // 'model' => $model,
                 //  'label' => 'Enter amount range',
                 // 'attribute1' => 1,
                 //'attribute2' => 1000,
                 'type' => \kartik\field\FieldRange::INPUT_TEXT,
             ]);*/

            ?>*
        </div>
        <div class="col-sm-3">
            <?php
            // echo $form->field($model, 'inst_type')->dropDownList(\common\components\Helpers\LoanHelper::getInstallmentTypes(), ['prompt' => 'Select Inst. Type'])->label('Inst. Type');
            ?>
        </div>
        <div class="col-sm-3">
            <?php
            //echo $form->field($model, 'status')->dropDownList(\common\components\Helpers\LoanHelper::getDisbursementStatus(), ['prompt' => 'Select Disb. Type'])->label('Disb. Type');
            ?>
        </div>
        <div class="col-sm-3">

        </div>
        <div class="col-sm-3">

        </div>
        <div class="col-sm-3">
            <?php
            /*  echo $form->field($model, 'date_disbursed')->widget(DateRangePicker::classname(), [
                  'convertFormat'=>true,
                  'options' => ['class' => 'form-control form-control-sm', 'placeholder' => 'Disbursement Date'],
                  'pluginOptions'=>[
                      'locale'=>[
                          'format'=>'Y-m-d',
                      ]
                  ]
              ])->label("Disbursement Date");*/
            ?>
        </div>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<a href="#demo" class="btn btn-primary" data-toggle="collapse" ><span
        class="glyphicon glyphicon-search"></span> Advanced Search</a>

