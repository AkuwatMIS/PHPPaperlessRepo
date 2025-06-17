<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Disbursements */
$this->title = 'AM Approval';
$this->params['breadcrumbs'][] = ['label' => 'Loans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = '';
$js .= "
$('body').on('beforeSubmit', 'form.UpdateTranch', function () {

     var id = this.id;
     var id_array = this.id.split(\"-\");
     var serial_no = id_array[2];
     var loan_id = $('#loantranches-'+serial_no+'-id').val();
     //alert(loan_id);
     var form = $(this);
     // return false if form still have some validation errors
     if (form.find('.has-error').length) {
          return false;
     }
     
         
if (confirm('Are you sure you want to Approve this Tranch?')) {
    

     // submit form
     $.ajax({
          url: '/loans/am-approve-tranch?id='+loan_id,
          type: 'post',
          data: form.serialize(),
          success: function (response) {
               var obj = JSON.parse(response);
                if(obj.status_type == 'success'){
                    $('#save-button-'+serial_no).prop('disabled', true);
                    $('#save-button-'+serial_no).prop('innerText', 'Approved');
                }
          }
     });
     } else {
     }
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
    span.green {
        background: #5EA226;
        border-radius: 0.8em;
        -moz-border-radius: 0.8em;
        -webkit-border-radius: 0.8em;
        color: #ffffff;
        display: inline-block;
        font-weight: bold;
        line-height: 1.6em;
        margin-right: 15px;
        text-align: center;
        width: 1.6em;
    }
</style>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Tranches Detail</h4>
                </div>
            </div>
        </div>
    </header>
        <div class="box-typical box-typical-padding">
            <div class="row">
                <!--<section class="box-typical">-->
                <div class="col-md-6">
                    <article class="profile-info-item">
                        <header class="profile-info-item-header">
                            <i class="font-icon font-icon-doc"></i>
                            <b>Loan Information</b>
                        </header>
                        <div class="box-typical-inner">
                            <p>
                                <b>Sanction No</b> : <?= isset($loans->sanction_no)?($loans->sanction_no):'Not Set'; ?>
                            </p>
                            <p>
                                <b>Project</b> : <?= $loans->project->name ?>
                            </p>
                            <p>
                                <b>Disbursement Date</b> : <?= date('d,M-Y',$loans->date_disbursed) ?>
                            </p>

                        </div>
                    </article><!--.profile-info-item-->

                </div>
                <div class="col-md-6">
                    <article class="profile-info-item">
                        <header class="profile-info-item-header">
                            <i class="font-icon font-icon-view-rows"></i>
                            <b>Credit Structure Information</b>
                        </header>
                        <div class="box-typical-inner">
                            <p>
                                <b>Region</b>
                                : <?= isset($loans->region->name) ? $loans->region->name : 'Not Set'; ?>
                            </p>
                            <p>
                                <b>Area</b>
                                : <?= isset($loans->area->name) ? $loans->area->name : 'Not Set'; ?>
                            </p>
                            <p>
                                <b>Branch</b>
                                : <?= isset($loans->branch->name) ? $loans->branch->name : 'Not Set'; ?>
                            </p>
                            <p>
                                <b>Team</b>
                                : <?= isset($loans->team->name) ? $loans->team->name : 'Not Set'; ?>
                            </p>
                            <p>
                                <b>Field</b>
                                : <?= isset($loans->field->name) ? $loans->field->name : 'Not Set'; ?>
                                <?= isset($loans->field->userStructureMapping->user->username) ? '('.$loans->field->userStructureMapping->user->username.')' : '(--)'; ?>

                            </p>
                        </div>
                    </article><!--.profile-info-item-->
                </div>

                <!--</section>--><!--.box-typical-->
            </div>
        </div>
    <div class="box-typical box-typical-padding">
        <!--<h6>Showing <b><? /*= ($pages->page) * $pages->pageSize + 1 */ ?>
                        -<? /*= ($pages->page) * $pages->pageSize + count($loans) */ ?></b> of
                    <b><? /*= $pages->totalCount */ ?></b> items. </h6>-->
        <div class="row">
            <?php $j = 1;
            foreach ($loans->tranches as $key => $tranch) { ?>
                <div class="col-lg-4" style="margin-left: 10%">
                    <section class="box-typical">
                        <div class="profile-card">
                                <?php $form = ActiveForm::begin(['action' => '#', 'id' => 'loans-form-' . $j, 'options' => [
                                    'class' => 'UpdateTranch'
                                ]
                                ]);
                                ?>
                                <?= $form->field($tranch, "[{$j}]id")->hiddenInput(['name' => 'LoanTranches[id]'])->label(false) ?>
                            <div class="profile-card-name"><span class="green"><?= isset($tranch->tranch_no) ? $tranch->tranch_no : '' ?></span></div>

                                <hr>
                                <div class="profile-card-status"><b>Tranch Amount:  </b><?= isset($tranch->tranch_amount) ? $tranch->tranch_amount : '' ?></div>
                                <br>
                                <?php if ($tranch->status == 2) { ?>
                                        <?= Html::submitButton('Approve', ['id' => 'save-button-' . $j, 'class' => 'btn btn-success btn-sm disb']) ?>
                                    <?php } ?>

                                <?php $form->end(); ?>
                        </div>
                    </section>
                </div>

                <?php
                $j++;
            } ?>
        </div>
    </div>
</div>
