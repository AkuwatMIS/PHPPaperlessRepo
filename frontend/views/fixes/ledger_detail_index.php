<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Actions */
/* @var $form yii\widgets\ActiveForm */

$js = "
$('#fix-schedule').on('click', function() {
});
window.setTimeout(function() {
    $(\".alert\").fadeTo(3000, 0).slideUp(1000, function(){
        $(this).remove(); 
    });
}, 500);
";
$js .= "
window.setTimeout(function() {
    $(\".sttus\").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 500);
";
$this->registerJs($js);
$permissions = Yii::$app->session->get('permissions');
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
    <?php if (Yii::$app->session->hasFlash('error')) { ?>
        <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
            <h4><i class="icon fa fa-remove"></i> Error!</h4>
            <?= Yii::$app->session->getFlash('error')[0] ?>
        </div>
    <?php } ?>
    <?php if (isset($model->id)) { ?>
        <div class="box-typical box-typical-padding">
            <div class="row">
                <div class="col-md-12">
                    <h4>
                        <b><?php echo isset($model->application->member->full_name) ? $model->application->member->full_name : 'Not Set';
                            echo isset($model->application->member->cnic) ? ' (' . $model->application->member->cnic . ')' : '(Not Set)';
                            ?></b>
                        <a id='fix-schedule' href="/fixes/fixes-loans?id=<?php echo $model->id ?>"
                           class="btn btn-primary pull-right mr-2">Fixes</a>
                    </h4>
                </div>
            </div>
            <br>
            <?php if (Yii::$app->session->hasFlash('success')) { ?>
                <div class="alert alert-success alert-dismissable">
                    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
                    <h4><i class="icon fa fa-check"></i> Loan Details!</h4>
                    <?= Yii::$app->session->getFlash('success')[0] ?>
                </div>
            <?php }?>
            <div class="row">
                <?php if (isset($model->id)) { ?>
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th colspan="4">
                                <h4>Payment Details:</h4>
                            </th>
                        </tr>
                        <tr>
                            <th scope="col">OLP</th>
                            <th scope="col">RENT</th>
                            <th scope="col">TAX</th>
                            <th scope="col">PAYABLE</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <th scope="row"><?php echo $olp; ?></th>
                            <td scope="row"><?php echo $rent; ?></td>
                            <td scope="row"><?php echo $tax; ?></td>
                            <td scope="row"><?php echo $payable; ?></td>
                        </tr>

                        </tbody>
                    </table>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
