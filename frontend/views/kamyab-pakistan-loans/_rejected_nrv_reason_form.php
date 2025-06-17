<?php

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
<div class="container-fluid border-0" id="demo">
    <?php $form = ActiveForm::begin([
        'action' => ['/kamyab-pakistan-loans/reject-submited-nadra-verisys'],
        'method' => 'post'
    ]); ?>
    <div class="row">
        <?= $form->field($model, 'nadra_verisys_rejected_id')->hiddenInput()->label(false); ?>
    </div>
    <div class="row">

        <div class="col-md-12">
            <?php
            echo $form->field($model, 'reject_reason')->dropDownList (['Blur NADRA e-Sahulat Token Attached – MIS'=>'Blur NADRA e-Sahulat Token Attached – MIS',
                'Expire NADRA e-Sahulat Token Attached – MIS'=>'Expire NADRA e-Sahulat Token Attached – MIS',
                'NTL Token not Required NADRA e-Sahulat Token Required – MIS'=>'NTL Token not Required NADRA e-Sahulat Token Required – MIS',
                'Invalid NADRA e-Sahulat Token – MIS'=>'Invalid NADRA e-Sahulat Token – MIS',
                'NADRA e-Sahulat Token Not Verified From NADRA – NADRA'=>'NADRA e-Sahulat Token Not Verified From NADRA – NADRA',
                'Incomplete NADRA e-Sahulat Token Attached – MIS'=>'Incomplete NADRA e-Sahulat Token Attached – MIS',
                'Incorrect CNIC Number on NADRA e-Sahulat Token – MIS'=>'Incorrect CNIC Number on NADRA e-Sahulat Token – MIS',
                'Incorrect CNIC Picture Attached – MIS'=>'Incorrect CNIC Picture Attached – MIS',
                'Incorrect Front Side CNIC Picture Attached – MIS'=>'Incorrect Front Side CNIC Picture Attached – MIS',
                'Incorrect Back Side CNIC Picture Attached – MIS'=>'Incorrect Back Side CNIC Picture Attached – MIS',
                'Incorrect Front and Back CNIC Attached – MIS'=>'Incorrect Front and Back CNIC Attached – MIS',
                'Incorrect CNIC Number – MIS'=>'Incorrect CNIC Number – MIS',
                'Incorrect Profile Picture – MIS'=>'Incorrect Profile Picture – MIS',
                'Profile Picture Not Updated – MIS'=>'Profile Picture Not Updated – MIS',
                'Expired CNIC Attached – MIS'=>'Expired CNIC Attached – MIS',
                'Incorrect name - MIS'=>'Incorrect name - MIS',
                'Blurred CNIC pictures - MIS'=>'Blurred CNIC pictures - MIS',
                'Incorrect CNIC pictures - MIS'=>'Incorrect CNIC pictures - MIS',
                'Profile picture change - MIS'=>'Profile picture change - MIS',
                'Incorrect NADRA e-Sahulat token attached – MIS'=>'Incorrect NADRA e-Sahulat token attached – MIS',
                'Date of Birth Incorrect in MIS'=>'Date of Birth Incorrect in MIS']);
//            echo $form->field($model, 'reject_reason')->dropDownList(['invalid_cnic'=>'Invalid Cnic','incorrect_name'=>'Incorrect Name','cnic_scanning_issue_mis'=>'CNIC Scanning Issue - MIS','profile_picture_issue_mis'=>'Profile Picture Issue - MIS',
//                'Issue Date not Show in MIS'=>'Issue Date not Show in MIS','wrong_gender_mis'=>'Wrong Gender - MIS',
//                'wrong_cnic_number_mis'=>'Wrong CNIC Number - MIS','Applicant/Parentage Name Data Entry Error - MIS'=>'Applicant/Parentage Name Data Entry Error - MIS',
//                'expired_cnic_nadra'=>'Expired CNIC - NADRA','Reprocessing Required in NADRA'=>'Reprocessing Required in NADRA',
//                'Duplicate CNIC - NADRA'=>'Duplicate CNIC - NADRA','Digitally impounded - NADRA'=>'Digitally impounded - NADRA',
//                'Deceased - NADRA'=>'Deceased - NADRA','System Independent - NADRA'=>'System Independent - NADRA',
//                'NADRA has never issued an identity card - NADRA'=>'NADRA has never issued an identity card - NADRA',
//                'Picture Mismatch on CNIC Card in - NADRA'=>'Picture Mismatch on CNIC Card in NADRA'], ['prompt' => 'Select Reason']);
            ?>
        </div>
        <div class="col-md-12">
            <?php
            echo $form->field($model, 'remarks')->textarea(['rows' => '4']);
            ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('save', ['class' => 'btn btn-primary pull-right']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
