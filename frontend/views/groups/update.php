<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Groups */
$this->title = $model->grp_no;
$this->params['breadcrumbs'][] = ['label' => 'Groups', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
/*print_r($dataProvider);
print_r($searchModel);
die('here');*/
$session = Yii::$app->session;
/*echo '<pre>';
print_r($session['applications']);
die();*/
//echo $session->getFlash('group_error');
/*print_r(Yii::$app->session->hasFlash('success'));
print_r(Yii::$app->session->getFlash('success'));
die();*/
$js = "
window.setTimeout(function() {
    $(\".alert\").fadeTo(500, 0).slideUp(500, function(){
        $(this).remove(); 
    });
}, 500);
";
$js.='
//var form = document.getElementById("form");
document.getElementById("remove-application").addEventListener("click", function (event) {
if (confirm(\'Are you sure you want delete this application ?\')) {
   
} else {
  event.preventDefault();
    // Do nothing!
}
});



';
$this->registerJs($js);
$this->registerJs($js);
?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Update Group</h4>
                </div>
            </div>
        </div>
    </header>
    <?php if (Yii::$app->session->hasFlash('success')) { ?>
        <div class="alert alert-success alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
            <h4><i class="icon fa fa-check"></i> Saved!</h4>
            <?= Yii::$app->session->getFlash('success')[0] ?>
        </div>
    <?php }
    if (Yii::$app->session->hasFlash('error')) { ?>
        <div class="alert alert-danger alert-dismissable">
            <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
            <h4><i class="icon fa fa-remove"></i> Error!</h4>
            <?= Yii::$app->session->getFlash('error')[0] ?>
        </div>
    <?php } ?>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_search_application_update', [
            'application' => $searchModel,
            'model'=>$model
        ]) ?>
    </div>

    <?php
    if (isset($session['applications']) && !empty($session['applications'])) {
        $applications = $session['applications'];
        if (!empty($applications)) {
            ?>
            <div class="box-typical box-typical-padding">
            <?php $form = ActiveForm::begin([
                'action' => 'update?id='.$model->id,
                'method' => 'post',
            ]); ?>
            <?= $form->errorSummary($model) ?>
            <?= $form->field($model, 'group_name')->textInput(['maxlength' => true]) ?>
            <?php
            /*print_r($applications);
            die('here');*/
            ?>
            <table id="table-edit" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="1">#</th>
                    <th>Application No</th>
                    <th>Name</th>
                    <th>Parentage</th>
                    <th>CNIC</th>
                    <th>Req Amount</th>
                    <th>Activity</th>
                    <th>Product</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php $i = 1;
                foreach ($applications as $a) { ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= $a->application_no ?></td>
                        <td><?= $a->member->full_name ?></td>
                        <td><?= $a->member->parentage ?></td>
                        <td><?= $a->member->cnic ?></td>
                        <td><?= $a->req_amount ?></td>
                        <td><?= isset($a->activity->name) ? $a->activity->name : '' ?></td>
                        <td><?= $a->product->name ?></td>
                        <?php  if(isset($a->loan) && !empty($a->loan)){?>
                        <td></td>
                        <?php }
                        else{?>
                            <td><a href="/groups/removemember?id=<?= $a->id ?>&&grp_id=<?php echo $model->id?>" id="remove-application" class="btn btn-danger"><i
                                        class="fa fa-trash"></i></a></td>
                       <?php }
                        ?>

                    </tr>
                    <?php $i++;
                } ?>
                </tbody>
            </table>
        <?php }
        ?>

        <div class="form-group">
            <?= Html::submitButton('Update Group', ['class' => 'btn btn-success btn-sm']) ?>
        </div>

        <?php ActiveForm::end(); ?>
        </div>
        <?php
    } ?>


</div>

