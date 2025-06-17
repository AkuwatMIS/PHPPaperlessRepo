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

$js = "
$(\"#create_find_request\").click(function(){
 $('#create_find_request').attr(\"disabled\", true);
  $('#w1').submit();
});
";
$this->registerJs($js)
?>

<div class="fund-requests-form">
    <table id="table-edit" class="table table-bordered table-hover">
        <thead>
        <tr>
            <th width="2">No.</th>
            <th>Project</th>
            <th>No. of Loans</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        $total_loans = 0;
        $total_amount = 0;
        $branch_id = 0;
        foreach ($fund_request_detail as $f) { ?>
            <tr>
                <td><?= $i ?></td>
                <td><?= \common\models\Projects::findOne($f['project_id'])->name ?></td>
                <td><?= number_format($f['total_loans']) ?></td>
                <td><?= number_format($f['total_requested_amount']) ?></td>
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
        </tbody>
    </table>
    <?php $form = ActiveForm::begin(); ?>
        <?php $i = 0; foreach ($fund_request_detail as $d){ ?>
            <?= $form->field($fund_requests_details[$i], "[{$i}]branch_id")->hiddenInput(['value'=>$d['branch_id']])->label(false) ?>
            <?= $form->field($fund_requests_details[$i], "[{$i}]project_id")->hiddenInput(['value'=>$d['project_id']])->label(false) ?>
            <?= $form->field($fund_requests_details[$i], "[{$i}]total_loans")->hiddenInput(['value'=>$d['total_loans']])->label(false) ?>
            <?= $form->field($fund_requests_details[$i], "[{$i}]total_requested_amount")->hiddenInput(['value'=>$d['total_requested_amount']])->label(false) ?>
        <?php $i++;} ?>
    <?php $branch = \common\models\Branches::findOne($branch_id); ?>
    <?= $form->field($model, 'requested_amount')->hiddenInput(['value'=>$total_amount])->label(false) ?>
    <?= $form->field($model, 'total_loans')->hiddenInput(['value'=>$total_loans])->label(false) ?>
    <?= $form->field($model, 'region_id')->hiddenInput(['value'=>$branch->region_id])->label(false) ?>
    <?= $form->field($model, 'area_id')->hiddenInput(['value'=>$branch->area_id])->label(false) ?>
    <?= $form->field($model, 'branch_id')->hiddenInput(['value'=>$branch->id])->label(false) ?>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton('Create Request', ['id'=>'create_find_request','class' => 'btn btn-success']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>
</div>
