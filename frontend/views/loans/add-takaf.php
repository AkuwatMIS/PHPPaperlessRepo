<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Disbursements */
$this->title = 'Add Takaful';
$this->params['breadcrumbs'][] = ['label' => 'Disbursements', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = '';
$js .= '$(".takaf").change(function() {
    var csc=this.className;
    if($(this).not(\':checked\')){
    document.getElementsByClassName(""+csc+"-receive-date")[0].disabled = true;
    document.getElementsByClassName(""+csc+"-credit")[0].disabled = true;
    document.getElementsByClassName(""+csc+"-receipt-no")[0].disabled = true;
    }
    if($(this).is(\':checked\')){
    document.getElementsByClassName(""+csc+"-receive-date")[0].disabled = false;
    document.getElementsByClassName(""+csc+"-credit")[0].disabled = false;
    document.getElementsByClassName(""+csc+"-receipt-no")[0].disabled = false;
    }
});

';
$js .= "
$('body').on('beforeSubmit', 'form.formInstantDisb', function () {
     
     var id = this.id;
     var id_array = this.id.split(\"-\");
     var serial_no = id_array[2];
     //alert(serial_no);
     var loan_id = $('#loans-'+serial_no+'-id').val();

     //alert(loan_id);
     var form = $(this);
     // return false if form still have some validation errors
     if (form.find('.has-error').length) {
          return false;
     }
     // submit form
     $.ajax({
          url: '/loans/save-takaf-loans?id='+loan_id,
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    $('#loans-'+serial_no+'-status').attr('disabled','disabled');
                    $('#save-button-'+serial_no).prop('disabled', true);
                    $('#'+serial_no+'-tick').show();
                    $('#status-'+serial_no).hide();
                   
                }else{
                    var c='';
                    $('#status-'+serial_no).addClass('error');
                    $('#status-'+serial_no).text(obj.errors);
                    jQuery.each(obj.errors, function(index, item) {
                    if(c!=''){
                      c += ',';
                    }
                        c +=item;
                  });
                  $('#status-'+serial_no).text(c);
                  $('#status-'+serial_no).show();
                }
          }
     });
     return false;
});

window.setTimeout(function() {
    $(\".sttus\").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 500);
";
$this->registerJs($js);
?>
<style>
    .success{
        background: #46c35f;
        border-radius: 5px;
        color: #ffffff;
        padding: 5px;
    }
    .error{
        background: #fa424a;
        border-radius: 5px;
        color: #ffffff;
        padding: 5px;
    }
</style>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4>Search Branch for Add Takaful</h4>
                    </div>
                </div>
            </div>
        </header>
        <div class="disbursements-form">
            <?php $form = ActiveForm::begin(['id' => 'add-takaf','method'=>'get']); ?>
            <div class="row">
                <div class="col-md-4">
                    <?= $form->field($loans_search, 'branch_id')->dropDownList($branches,['prompt'=>'Select Branch'])->label('Select Branch') ?>
                </div>
                <div class="col-md-4" style="margin-top:1.3%">
                    <?= Html::submitButton('Submit', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Add Takaful</h4>
                </div>
            </div>
        </div>
    </header>
    <?php if (!empty($loans)) { ?>
        <div class="box-typical box-typical-padding">
            <table id="table-edit" class="table table-bordered table-hover">
                <h6>Showing <b><?= ($pagination->page) * $pagination->pageSize + 1?>-<?= ($pagination->page) * $pagination->pageSize + count($loans->getModels())?></b> of <b><?= $pagination->totalCount ?></b> items. </h6>

                <thead>
                <tr>
                    <th width="1">#</th>
                    <th>Group No</th>
                    <th>Sanction No</th>
                    <th>Name</th>
                    <th>Parentage</th>
                    <th>Req Amount</th>
                    <th></th>
                    <th>Takaful Date</th>
                    <th>Takaful Receipt</th>
                    <th>Takaful Amount</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 0;
                foreach ($loans->getModels() as $key=>$loan) { ?>
                <?php $nadraStatus = \common\components\Helpers\LoanHelper::VerifyNadraVerysisTakaf($loan);
                if ($nadraStatus) { ?>
                    <tr>
                        <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $i, 'options' => [
                            'class' => 'formInstantDisb'
                        ]
                        ]);
                        ?>
                        <td><?= $i+1 ?></td>
                        <td><?= isset($loan->group->grp_no)?$loan->group->grp_no:'Not Set' ?></td>
                        <td><?= isset($loan->sanction_no)?$loan->sanction_no:'Not Set' ?></td>
                        <td><?= isset($loan->application->member->full_name)?$loan->application->member->full_name:'Not Set' ?></td>
                        <td><?= isset($loan->application->member->parentage)?$loan->application->member->parentage:'Not Set' ?></td>
                        <td><?= number_format($loan->loan_amount)?></td>
                        <td><input class="takaf -<?php echo $i?>" type="checkbox" id="takaf" checked="checked"></td>
                        <?= $form->field($loan, "[{$i}]id")->hiddenInput(['class' => 'id','value'=>$loan->id])->label(false) ?>
                        <!-- <td><input class='form-control input-sm takaf -<?php /*echo $i*/?>-receive-date' type="text" id="receive_date"
                                   name="Operations[receive_date]"></td>-->
                        <td>
                            <?php echo \yii\jui\DatePicker::widget([
                                'name' => 'Operations[receive_date]',
                                'value' => date('d-M-Y'),
                                'options' => ['placeholder' => 'Select date',
                                    'class'=>'form-control input-sm takaf -'.$i.'-receive-date',
                                    'type' => \kartik\date\DatePicker::TYPE_INPUT,
                                    'format' => 'dd-M-yyyy',
                                    'todayHighlight' => true,
                                ],
                            ]);?>
                        </td>
                        <td><input class='form-control input-sm takaf -<?php echo $i?>-receipt-no' type="text" id="receipt_no"
                                   name="Operations[receipt_no]"></td>
                        <td><input class="form-control input-sm takaf -<?php echo $i?>-credit" type="text" id="credit" name="Operations[credit]"  value=<?php  $Kpp=[77,78,79]; if(in_array($loan->project_id,$Kpp) ){ echo (($loan->loan_amount*0.5)/100);}else{echo (($loan->loan_amount*1)/100);}?>></td>
                        <input type="hidden" id="application_id" value="<?php echo $loan->application_id ?>"
                               name="Operations[application_id]">
                        <input type="hidden" id="operation_type_id" value="2"
                               name="Operations[operation_type_id]">
                        <input type="hidden" id="region_id" value="<?php echo $loan->region_id ?>"
                               name="Operations[region_id]">
                        <input type="hidden" id="area_id" value="<?php echo $loan->area_id ?>"
                               name="Operations[area_id]">
                        <input type="hidden" id="branch_id" value="<?php echo $loan->branch_id ?>"
                               name="Operations[branch_id]">
                        <input type="hidden" id="team_id" value="<?php echo $loan->team_id ?>"
                               name="Operations[team_id]">
                        <input type="hidden" id="field_id" value="<?php echo $loan->field_id ?>"
                               name="Operations[field_id]">
                        <input type="hidden" id="project_id" value="<?php echo $loan->project_id ?>"
                               name="Operations[project_id]">
                        <input type="hidden" id="loan_id" value="<?php echo $loan->id ?>"
                               name="Operations[loan_id]">
                        <td>
                            <span class="glyphicon glyphicon-ok" style="color:green;display: none;" id = <?= $i."-tick" ?> ></span>
                            <?= Html::submitButton('save', ['id'=>'save-button-' . $i,'class' => 'btn btn-success btn-sm disb']) ?>
                            <div id="status-<?php echo $i; ?>" style="display: none;" class="status"></div>
                        </td>
                        <?php $form->end(); ?>

                    </tr>
               <?php } ?>


                    <?php $i++;
                } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>
    <?php echo \yii\widgets\LinkPager::widget(['pagination' => $pagination]);?>

</div>
