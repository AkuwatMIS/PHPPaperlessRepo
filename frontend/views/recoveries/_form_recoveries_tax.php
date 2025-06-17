<?php
use yii\helpers\Html;
use yii\helpers\BaseHtml;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Recoveries */
/* @var $form yii\widgets\ActiveForm */
$js = '
jQuery(".dynamicform_wrapper").on("afterInsert", function(e, item) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Address: " + (index + 1))
    });
});

jQuery(".dynamicform_wrapper").on("afterDelete", function(e) {
    jQuery(".dynamicform_wrapper .panel-title-address").each(function(index) {
        jQuery(this).html("Address: " + (index + 1))
    });
});
';
$js .= "
$(document).ready(function(){
    $('.sanction_no').blur(function(e) {
        var id = this.id.split('-');
        var sr = id[1];
        //sanction_no = this.value;
        if(this.value){
            sanction_no = this.value;
        }
        
        $.ajax({
           //alert(this.value);
           url: '/recoveries/get-member-info',
           type: 'POST',
           dataType: 'JSON',
           data: {sanc_no: sanction_no},
           success: function(data, status){
                if(status == 'success'){
                    if(data.error != null){
                        $('#recoveries-'+sr+'-error').text(data.error);
                        $('#recoveries-'+sr+'-error').show();
                        $('#recoveries-'+sr+'-error').delay(4000).fadeOut(3000);
                    }else{
                        $('#recoveries-'+sr+'-name').val(data.name+' ('+data.cnic+')');
                    }
                }else{
                }
           }
        });
    });
});
";
$js .= "
    if($.cookie('branch')){
        $('#branch').val($.cookie('branch'));
    }
    $('#branch').change(function(){
        $.cookie('branch', $('#branch').val(), { expires: 7 });
    });
    if($.cookie('project')){
        $('#project').val($.cookie('project'));
    }
    $('#project').change(function(){
        $.cookie('project', $('#project').val(), { expires: 7 });
    });
    if($.cookie('w0')){
        $('#w0').val($.cookie('w0'));
    }
    $('#w0').change(function(){
        $.cookie('w0', $('#w0').val(), { expires: 7 });
    });
    if($.cookie('n')){
        $('#n').val($.cookie('n'));
    }
    $('#n').change(function(){
        $.cookie('n', $('#n').val(), { expires: 7 });
    });
$(document).ready(function(){
   
    $(\".button-submit\").on('click', function (ev) {
        
        var receive_date = $(\"input[name='receive_date']\").val();
        var today = new Date();
        today.setDate(0);
        today = today.toISOString().split('T')[0];
        receive_date = new Date(receive_date).toISOString().split('T')[0];
        //alert(today);
        //alert(receive_date);
        if(today >= receive_date){
            //alert('123');
            ev.preventDefault();
            $('#myModal').modal({'show': true});
            
        }
        
    });
    
});

$(document).ready(function(){
   
    $(\".modal-submit\").on('click', function (ev) {
        document.getElementById('form').submit();
    });
    
});
$(\"#recoveries-0-receipt_no\").on(\"keypress\", function(e) {
    var k = event ? event.which : window.event.keyCode;
    if (k == 32) return false;
});
";

$this->registerJs($js);

$branch_code = isset($_GET['code']) ? ($_GET['code']) : '';
$funding_line = '';
$project = '';
if (isset($_GET['project'])) {
    $project = $_GET['project'];
    $p = explode('_', $_GET['project']);
    $funding_line = $p[0];
}
$receive_date = isset($_GET['receive_date']) ? ($_GET['receive_date']) : '';
$n = isset($_GET['n']) ? ($_GET['n']) : '';
//print_r($_GET);
?>

<style>
    .modal-body {
        background-color: red;
        color: white;
        font-size: 20px;
    }
</style>


<div style="border:1px solid #d6e9c6;padding:10px;">
    <?= Html::beginForm([''], 'get', ['enctype' => 'multipart/form-data', 'id' => 'form']); ?>
    <div class="row">
        <div class="col-sm-3">
            <?= Html::dropDownList('code', $branch_code, $branches, array('class' => 'form-control', 'id' => 'branch', 'prompt' => 'SELECT BRANCH')) ?>
            <div class="help-block"></div>
        </div>
        <div class="col-sm-3">
            <?php echo DepDrop::widget([
                'name' => 'project',
                'pluginOptions' => ['name' => 'project', 'id' => 'project',
                    'depends' => ['branch'],
                    'placeholder' => 'Select Project',
                    'url' => Url::to(['/structure/branchprojects'])
                ]]); ?>
            <div class="help-block"></div>
        </div>
        <div class="col-sm-2">
            <?php echo DatePicker::widget([
                'name' => 'receive_date',
                'value' => $receive_date,
                'options' => [
                    'class' => 'form-control',
                    'placeholder' => 'Recv Date',
                    'readonly' => 'readonly',
                    'beforeShowDay' => 'js:editDays',

                ],
                'language' => 'en',
                'dateFormat' => 'yyyy-MM-dd',

            ]); ?>
            <div class="help-block"></div>
        </div>
        <div class="col-sm-2">
            <?php
            $number_array = array();
            for ($i = 1; $i <= 100; $i++) {
                if ($i >= 1 && $i <= 10) {
                    $number_array[$i] = $i;
                } else {
                    if ($i % 5 == 0) {
                        $number_array[$i] = $i;
                    }
                }
            }
            //print_r($number_array);
            //die();
            ?>
            <?= Html::dropDownList('n', $n, $number_array, array('class' => 'form-control', 'id' => 'n', 'prompt' => '# Tax')) ?>
            <div class="help-block"></div>
        </div>

        <div class="col-md-2"><?= Html::submitButton('Submit', ['class' => 'btn btn-success button-submit']) ?></div>


        <?= Html::endForm(); ?>

    </div>
</div>


<div class="customer-form" style="margin-top: 10px;">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <div class="padding-v-md">
        <div class="line line-dashed"></div>
    </div>
    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.item', // required: css class
        'limit' => 4, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-item', // css class
        'deleteButton' => '.remove-item', // css class
        'model' => $model[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'receive_date',
            'source',
            'receipt_no',
            'amount',
        ],
    ]); ?>
    <section class="card card-green mb-3">
        <header class="card-header">
            <i class="fa fa-bank"></i> Post Tax
            <div class="clearfix"></div>
        </header>
        <?php if (Yii::$app->session->hasFlash('success')): ?>
            <div class="alert alert-success alert-dismissable">
                <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
                <h4><i class="icon fa fa-check"></i>Saved!</h4>
                <?= Yii::$app->session->getFlash('success') ?>
            </div>
        <?php endif; ?>

        <div class="card-block">
            <div class="panel panel-success">
                <div class="panel-body container-items"><!-- widgetContainer -->
                    <div class="row">
                        <div class="col-lg-2 text-center"><h6>Sanction No.</h6></div>
                        <div class="col-lg-2 text-center"><h6>Borrower (CNIC)</h6></div>
                        <div class="col-lg-2 text-center"><h6>Recv Date</h6></div>
                        <div class="col-lg-2 text-center"><h6>Source</h6></div>
                        <div class="col-lg-2 text-center"><h6>Receipt No</h6></div>
                        <div class="col-lg-1 text-center"><h6>Amount</h6></div>
                        <div class="col-lg-1 text-center"></div>

                    </div>
                    <?php foreach ($model as $index => $modelRecovery):
                        /*print_r($model);
                        echo $index; die();*/
                        if (!empty($branch_code) && !empty($funding_line)) {
                            if (!isset($modelRecovery->sanction_no)) {
                                $modelRecovery->sanction_no = $branch_code . '-' . $funding_line . '-';
                                //print_r($modelRecovery->sanction_no);
                                //die();
                                $modelRecovery->receive_date = $receive_date;
                            }
                        }
                        //echo \Yii::$app->session->getFlash('error');
                        //echo Alert::widget();
                        ?>

                        <div class="item panel panel-default"><!-- widgetBody -->
                            <?php
                            ?>

                            <div class="row">

                                <div class="col-lg-2">
                                    <?= $form->field($modelRecovery, "[{$index}]sanction_no")->textInput(['class' => 'form-control sanction_no', 'placeholder' => 'Sanction No'])->label(false) ?>
                                </div>
                                <div class="col-lg-2">
                                    <?= HTML::textInput('name', '', array('class' => 'form-control', 'id' => "recoveries-{$index}-name", 'disabled' => 'disabled')) ?>
                                    <div class="help-block"></div>
                                </div>

                                <div class="col-lg-2">
                                    <?= $form->field($modelRecovery, "[{$index}]receive_date")->widget(\yii\jui\DatePicker::className(), [
                                        'dateFormat' => 'yyyy-MM-dd',
                                        'options' => ['class' => 'form-control', 'placeholder' => 'Recv Date']
                                    ])->label(false); ?>
                                </div>
                                <div class="col-lg-2">
                                    <?= $form->field($modelRecovery, "[{$index}]source")->textInput(['maxlength' => 20, 'placeholder' => 'Source'])->label(false) ?>
                                </div>
                                <div class="col-lg-2">
                                    <?= $form->field($modelRecovery, "[{$index}]receipt_no")->textInput(['maxlength' => true, 'placeholder' => 'Receipt No'])->label(false) ?>
                                </div>
                                <div class="col-lg-1">
                                    <?= $form->field($modelRecovery, "[{$index}]credit_tax")->textInput(['maxlength' => true, 'placeholder' => 'Tax Amount'])->label(false) ?>
                                </div>
                                <div class="col-lg-1">
                                    <button type="button" class="pull-left remove-item btn btn-danger btn-xs"><span
                                                class="glyphicon glyphicon-trash"></span></button>
                                </div>
                            <?php

                            if (!empty($modelRecovery->id)) {
                                ?>
                                <div
                                        style="opacity:1;background-color: #26B99A;position: absolute;">Tax
                                    Save
                                    Successfully
                                </div>
                                <?php
                            }
                            ?>
                            <div class="alert alert-success" style="display:none;"><?php echo $modelRecovery->id;
                                !empty($modelRecovery->id) ? 'Tax Save Successfully' : ''; ?></div>
                            <?= $form->errorSummary($modelRecovery); ?>
                            <div id="recoveries-<?php echo $index ?>-error" class="alert alert-danger"
                                 style="display:none;"></div>
                            <!--</div>-->
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php DynamicFormWidget::end(); ?>
        </div>
    </section>

    <div class="form-group">
        <?= Html::submitButton($modelRecovery->isNewRecord ? 'Save' : 'Update', ['class' => $modelRecovery->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>


<!-- Modal -->
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">×</span>
                </button>
                <p>Are you sure to add tax in previous month?</p>

                <button type="button" class="btn btn-default modal-submit" data-dismiss="modal">Yes</button>
            </div>
        </div>

    </div>
</div>