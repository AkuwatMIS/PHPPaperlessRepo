<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\FundRequests */
/* @var $form yii\widgets\ActiveForm */
/*echo '<pre>';
print_r($fund_request_detail);
print_r($fund_requests_details);
die();*/
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Fund Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = '$(".fundstatus").change(function() {
    var csc=this.className;
    var idno=csc.substring(25, 26);
    if(this.value==(\'fund not available\')){
    document.getElementsByClassName(""+csc+"-chequeno")[0].disabled = true;
    document.getElementsByClassName(""+csc+"-chequeno")[0].value = ""; 
       $("#fundrequestsdetails-"+idno+"-cheque_no").prop(\'required\',false);

    }
    else if(this.value==(\'fund available\')){
    document.getElementsByClassName(""+csc+"-chequeno")[0].disabled = false;
      $("#fundrequestsdetails-"+idno+"-cheque_no").prop(\'required\',true);
    }
});

';
$this->registerJs($js);
?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>RA Fund Request Process</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <h4><b>Branch Details (<?= $model->branch->name.'/ Code: '.$model->branch->code.')'?></b></h4>
        <div class="fund-requests-form">
            <?php $form = ActiveForm::begin(); ?>
            <table id="table-edit" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="2">No.</th>
                    <th>Project</th>
                    <th>No. of Loans</th>
                    <th>Requested Amount</th>
                    <th>Status</th>
                    <th>Cheque No</th>
                    <th>Payment Method</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                $total_loans = 0;
                $total_amount = 0;
                $branch_id = 0;
                ?>
                <?php if(isset($errors)){
                    echo $form->errorSummary($errors); }?>
                <div id="recoveries-<?php echo '1' ?>-error" class="alert alert-danger"
                     style="display:none;"></div>
                <?php
                foreach ($fund_request_detail as $key => $f) { ?>
                    <?= $form->field($fund_request_detail[$key], "[{$key}]id")->hiddenInput(['value' => $f->id])->label(false) ?>
                    <?= $form->field($fund_request_detail[$key], "[{$key}]branch_id")->hiddenInput(['value' => $f->branch_id])->label(false) ?>
                    <?= $form->field($fund_request_detail[$key], "[{$key}]project_id")->hiddenInput(['value' => $f->project_id])->label(false) ?>
                    <?= $form->field($fund_request_detail[$key], "[{$key}]fund_request_id")->hiddenInput(['value' => $f->fund_request_id])->label(false) ?>
                    <?= $form->field($fund_request_detail[$key], "[{$key}]total_loans")->hiddenInput(['value' => $f->total_loans])->label(false) ?>
                    <?= $form->field($fund_request_detail[$key], "[{$key}]total_requested_amount")->hiddenInput(['value' => $f->total_requested_amount])->label(false) ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= \common\models\Projects::findOne($f->project_id)->name ?></td>
                        <td><?= number_format($f['total_loans']) ?></td>
                        <td><?= number_format($f['total_requested_amount']) ?></td>
                        <td><?= $form->field($fund_request_detail[$key], "[{$key}]status")->textInput([])->dropDownList( \common\components\Helpers\ListHelper::getFundRequestDetailStatus(),array('class'=>'form-control fundstatus -'.$key))->label(false) ?></td>
                        <td><?= $form->field($fund_request_detail[$key], "[{$key}]cheque_no")->textInput(['class'=>'form-control input-sm fundstatus -'.$key.'-chequeno','required'=>'required'])->label(false) ?></td>
                        <th><?= $form->field($fund_request_detail[$key], "[{$key}]payment_method_id")->dropDownList( \yii\helpers\ArrayHelper::map(\common\models\PaymentMethods::find()->select(["id",'CONCAT(name, "(",type,")") as name'])->all(),'id','name'))->label(false) ?></th>
                    </tr>
                    <?php
                    $i++;
                    $total_loans += $f['total_loans'];
                    $total_amount += $f['total_requested_amount'];
                    $branch_id = $f['branch_id'];
                } ?>
                <th></th>
                <th>Total:</th>
                <th><?= number_format($total_loans) ?></th>
                <th><?= number_format($total_amount) ?></th>
                <th></th>
                <th></th>
                <th></th>
                </tbody>
            </table>

            <?= $form->field($model, 'status')->hiddenInput(['value' => 'processed'])->label(false) ?>
            <?= $form->field($model, 'processed_by')->hiddenInput(['value' => Yii::$app->user->getId()])->label(false) ?>
            <?= $form->field($model, 'processed_on')->hiddenInput(['value' => time()])->label(false) ?>
            <?php if ($model->status =='approved') { ?>
                <br>
                <?php if (!Yii::$app->request->isAjax) { ?>
                    <div class="form-group">
                        <?= Html::submitButton('Processed Request', ['class' => 'btn btn-success pull-right ']) ?>
                    </div>
                <?php } ?>
            <?php } ?>

            <?php ActiveForm::end(); ?>
<br>
        </div>
    </div>
</div>
<div class="container-fluid">

    <div class="box-typical box-typical-padding">
        <div class="fund-requests-form">
            <?= \yii\grid\GridView::widget([
                'id' => 'crud-datatable',
                'dataProvider' => $dataProviderLoans,
                'filterModel' => $searchModelLoans,
                'showFooter' => true,
                'columns' => require(__DIR__ . '/_columns_loans.php'),

            ]) ?></div>
    </div>
</div>