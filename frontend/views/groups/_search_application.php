<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $model common\models\Groups */
/* @var $form yii\widgets\ActiveForm */
$js = '
$(document).ready(function(){
 function clearInput(element){
element.value="";
}
//        $(\'#applications-type input\').on(\'change\', function() {
//        
//            type = $(\'input[name="Applications[type]"]:checked\', \'#applications-type\').val();
//            
//            if(type == \'application_no\'){
//                $("label[for=\'applications-application_no\']").text("Application No");
//                //$("#applications-application_no").attr("name", "GlobalsSearch[sanction_no]");
//                $("#applications-application_no").attr("placeholder", "Search by Application No");
//            }else if(type == \'cnic\'){
                $("label[for=\'applications-application_no\']").text("CNIC");
                //$("#globalssearch-sanction_no").attr("name", "GlobalsSearch[borrower_cnic]");
                $("#applications-application_no").attr("placeholder", "Search by CNIC");
//            }
//           else{
//           }
//        });
       
        });
';
$this->registerJs($js);
//$types = array('application_no'=>'Application No','cnic'=>' CNIC',);
$types = array('cnic'=>' CNIC',);

?>
<style>
    .btn-md {
        font-size: 15px;
        width: 160px;
        background-color: #5A738E;
    }

    .reset {
        font-size: 15px;
        width: 160px;
    }
    .radio input {
        position: relative;
        visibility: visible;
    }
    label {
        display: inline;
    }
</style>
<?php $form = ActiveForm::begin([
    'action' => ['create'],
    'method' => 'post',
]); ?>
<div class="col-md-4">
<!--    $application->type='application_no'-->
    <?php $application->type='cnic'?>
    <?= $form->field($application, 'type')->radioList($types, ['class' => 'radio', 'required' => 'required','style' => 'font-size:20px;display:block'])->label(false); ?>

    <?= $form->field($application, 'application_no')->textInput(['maxlength' => true,'placeholder'=>'Search by Application No']) ?>
</div>

<div class="col-md-4">
    <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>

</div>

<?php ActiveForm::end(); ?>


