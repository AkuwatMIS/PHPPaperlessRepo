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
            $branches = ArrayHelper::map(\common\models\Branches::find()->asArray()->all(), 'id', 'name');
          echo $form->field($model, 'branch')->dropDownList($branches, ['prompt' => 'Select Branch'])->label('Branch');

            ?>
        </div>
        <div class="col-sm-3">
            <?php

            echo $form->field($model, 'cnic')->widget(\yii\widgets\MaskedInput::className(), [
                'mask' => '99999-9999999-9',
            ])->label("Member CNIC")->textInput(['placeholder'=>'Member CNIC', 'class'=>'form-control form-control-sm'])           ?>
        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'poverty_score')->label("Poverty Score")->dropDownList( array(1=>'1',2=>'2',3=>3,4=>4,5=>5),['prompt' => 'Select Poverty Score'])?>


        </div>
        <div class="col-sm-3">
            <?php
            echo $form->field($model, 'status')->label("Status")->dropDownList( ['0' => 'Pending', '1' => 'Processed'],['prompt' => 'Select Status'])

        // echo $form->field($model, 'status')->label(" Status")->dropDownList( ['prompt' => 'Select Status','value[0]'=>'Pending','value[1]'=>'Processed'])?>
        </div>
        <div class="col-sm-3">
            <?php
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
