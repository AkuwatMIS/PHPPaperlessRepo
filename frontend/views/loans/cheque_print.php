<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Disbursements */
$this->title = 'Cheque Print';
$this->params['breadcrumbs'][] = ['label' => 'Loans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = '';
$js .= "
$('body').on('beforeSubmit', 'form.UpdateChequeNo', function () {
     var id = this.id;
     var id_array = this.id.split(\"-\");
     var serial_no = id_array[2];
     
     if(serial_no==0){
    
      var chequenum = $('#loantranches-'+serial_no+'-cheque_no').val();
        if( $.isNumeric(chequenum)){
      $('.chequeno').each(function () {
      $( this ).val(chequenum)
       chequenum++;
      });
      }
      
      var cheque_dt = $('#loantranches-'+serial_no+'-cheque_dt').val();
        
      $('.cheque_dt').each(function () {
      $( this ).val(cheque_dt)
      
      });
     
      
     }
     var loan_id = $('#loantranches-'+serial_no+'-id').val();
     //alert(loan_id);
     var form = $(this);
     // return false if form still have some validation errors
     if (form.find('.has-error').length) {
          return false;
     }
     
         

     // submit form
     $.ajax({
          url: '/loans/update-cheque-no?id='+loan_id,
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                
                    $('#loantranches-'+serial_no+'-cheque_no').attr('disabled','disabled');
                    $('#loantranches-'+serial_no+'-cheque_dt').attr('disabled','disabled');

                    $('#save-button-'+serial_no).prop('disabled', true);
                    $('#'+serial_no+'-tick').show();
                    var amount=parseInt(obj.amount) + parseInt($('#amount-total').text());
                    $('#amount-total').text(amount);
                }else{
                var c='';
                 $('#status-'+serial_no).addClass('error');
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
    .success {
        background: #46c35f;
        border-radius: 5px;
        color: #ffffff;
        padding: 5px;
    }

    .error {
        background: #fa424a;
        border-radius: 5px;
        color: #ffffff;
        padding: 5px;
    }
</style>

<div class="container-fluid">
    <div class="box-typical box-typical-padding">

        <?php $form = ActiveForm::begin([
            'action' => ['cheque-print'],
            'method' => 'get',
        ]); ?>
        <div class="row">
            <div class="col-sm-3">
                <?php
                echo $form->field($searchModel, 'branch_id')->dropDownList($branches, ['prompt' => 'Select Branch'])->label('Branch');
                ?>
            </div>
            <div class="col-sm-3">
                <?php
                echo $form->field($searchModel, 'project_id')->dropDownList($projects, ['prompt' => 'Select Project'])->label('Project');
                ?>
            </div>
            <!-- <div class="col-sm-3">
                <?php
            /*                echo $form->field($searchModel, 'date_disbursed')->widget(\kartik\daterange\DateRangePicker::classname(), [
                                'convertFormat'=>true,
                                'options' => ['class' => 'form-control', 'placeholder' => 'Dibursement Date'],
                                'pluginOptions'=>[
                                    'startDate'      => date("y-m-d"),
                                    'locale'=>[
                                        'format'=>'Y-m-d',
                                    ]
                                ]
                            ])->label("Disbursement Date");
                            */ ?>
            </div>-->

            <div class="form-group">
                <?= Html::submitButton('Search', ['class' => 'btn btn-primary pull-right', 'style' => 'margin-top:30%']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Add Cheque No.</h4>
                </div>
            </div>
        </div>
    </header>
    <?php if (!empty($loans)) { ?>
        <div class="box-typical box-typical-padding">
                        <form action="/loans/cheque-print">
                            <input type="hidden" name="LoansSearch[branch_id]" value=<?php echo  $searchModel->branch_id ?>>
                            <input type="hidden" name="LoansSearch[project_id]" value=<?php echo  $searchModel->project_id ?>>
                            <button title="Export to CSV" type="submit" name="export" value="export" class="btn btn-primary pull-right">Export to CSV</button>
                        </form>
            <button class="btn btn-info pull-left">Target Amount: <b> <?= $awp_target_amount ?></b></button>
            <br>
            <br>
            <table id="table-edit" class="table table-bordered table-hover">
                <div id="amount-total" class="label label-warning pull-right">0</div>
                <h6>Showing <b><?= ($pages->page) * $pages->pageSize + 1 ?>
                        -<?= ($pages->page) * $pages->pageSize + count($loans) ?></b> of
                    <b><?= $pages->totalCount ?></b> items. </h6>
                <thead>
                <tr>
                    <th width="1">#</th>
                    <th>Name</th>
                    <th>Parentage</th>
                    <th>Cnic</th>
                    <th>Tranch Amount</th>
                    <th>Sanction No</th>
                    <th>Group No</th>
                    <th>Tranch No</th>
                    <th>Cheque Date/Expected Disb. Date</th>
                    <th>Cheque No.</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php $i = ($pages->page) * $pages->pageSize + 1;
                $j = 0;
                foreach ($loans as $key => $loan) { ?>
                    <tr>
                        <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $j, 'options' => [
                            'class' => 'UpdateChequeNo'
                        ]
                        ]);
                        ?>
                        <td><?= $i ?></td>

                        <td><?= isset($loan->loan->application->member->full_name) ? $loan->loan->application->member->full_name : '' ?></td>
                        <td><?= isset($loan->loan->application->member->parentage) ? $loan->loan->application->member->parentage : '' ?></td>
                        <td><?= isset($loan->loan->application->member->cnic) ? $loan->loan->application->member->cnic : '' ?></td>
                        <td><?= number_format($loan->tranch_amount) ?></td>
                        <td><?= $loan->loan->sanction_no ?></td>
                        <td><?= isset($loan->loan->group->grp_no) ? $loan->loan->group->grp_no : '' ?></td>
                        <td><b><?= isset($loan->tranch_no) ? $loan->tranch_no : '' ?></b></td>
                        <?php $loan->cheque_date=strtotime(date('Y-m-d'))?>
                        <?= $form->field($loan, "[{$j}]id")->hiddenInput(['name' => 'LoanTranches[id]'])->label(false) ?>
                        <!-- <? /*= $form->field($loan, "[{$i}]id")->hiddenInput(['class' => 'id','value'=>$loan->id])->label(false) */ ?>-->
                        <td>
                            <?= $form->field($loan, "[{$j}]cheque_date")->widget(\yii\jui\DatePicker::className(),[
                                'dateFormat' => 'yyyy-MM-dd',
                                'options' => ['class' => 'form-control input-sm cheque_dt', 'placeholder' => 'Cheque Date','name' => 'LoanTranches[cheque_date]','required' => true],
                                'clientOptions' => [
                                    'defaultDate' => date('Y-m-d'),
                                    ]
                            ])->label(false);  ?>
                        </td>
                        <td><?= $form->field($loan, "[{$j}]cheque_no")->textInput(['value'=>(in_array($loan->loan->project_id,\common\components\Helpers\StructureHelper::trancheProjects()))?'123123':$loan->cheque_no,'maxlength' => true, 'class' => 'form-control input-sm chequeno', 'name' => 'LoanTranches[cheque_no]', 'required' => true, 'oninvalid' => "this.setCustomValidity('Cheque No can not be blank.')", "oninput" => "this.setCustomValidity('')"])->label(false) ?></td>
                        <td>
                            <?= Html::submitButton('save', ['id' => 'save-button-' . $j, 'class' => 'btn btn-success btn-rounded btn-sm disb']) ?>
                            <span class="glyphicon glyphicon-ok" style="color:green;display: none;"
                                  id= <?= $j . "-tick" ?>></span>
                            <div id="status-<?php echo $j; ?>" style="display: none;" class="status"></div>
                        </td>
                        <?php $form->end(); ?>

                    </tr>
                    <?php
                    $j++;
                    $i++;
                } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="box-typical box-typical-padding">
            <div class="disbursements-form">
                <h3>No loan found to add cheque no.</h3>
            </div>
        </div>
    <?php } ?>
    <?php echo \yii\widgets\LinkPager::widget(['pagination' => $pages]);?>

</div>
