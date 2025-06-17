<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \common\components\Helpers\UsersHelper;
/* @var $this yii\web\View */
/* @var $model common\models\FundRequests */
/* @var $form yii\widgets\ActiveForm */

//$permissions = Yii::$app->session->get('permissions');
$this->registerJs($js);
?>
<link rel="stylesheet" href="/css/sweetalert.css">
<link rel="stylesheet" href="/css/sweet-alert-animations.min.css">
            <?php $branch = \common\models\Branches::findOne($branch_id);
            if($model->status=='pending'){
            ?>

                <div class="col-md-4">
                    <div class="card-block">
                        <p class="card-text">
                            <a href="/fund-requests/update-status?id=<?= $model->id ?>&status=approved" style="color: black" >
                              <button class="btn btn-inline btn-success-outline swal-btn-success" style="width: 150px; height: 100px; font-size: 20px;">
                                     Approve
                              </button>
                            </a>
                        </p>
                    </div>
                </div>
            <div class="col-md-4">
                <div class="card-block">
                    <p class="card-text">
                        <a href="/fund-requests/update-status?id=<?= $model->id ?>&status=rejected" >
                            <button class="btn btn-inline btn-danger-outline swal-btn-cancel" style="width: 150px; height: 100px;font-size: 20px; ">
                                Reject
                            </button>
                        </a>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-block">
                    <p class="card-text">
                        <a href="/fund-requests/view?id=<?= $model->id ?>" >
                            <button class="btn btn-inline btn-primary-outline" style="width: 150px; height: 100px; font-size: 20px;">
                                View Detail
                            </button>
                        </a>
                    </p>
                </div>
            </div>
<?php } else { ?>
            <div class="col-md-4">
                <div class="card-block">
                    <p class="card-text">
                        <a href="/fund-requests/update-status?id=<?= $model->id ?>&status=rejected" >
                            <button class="btn btn-inline btn-danger-outline swal-btn-cancel" style="width: 150px; height: 100px; font-size: 20px;">
                                Reject
                            </button>
                        </a>
                    </p>
                </div>
            </div>
<?php }?>


<script>
    $('.swal-btn-cancel').click(function(e){
        e.preventDefault();
        swal({
                title: "Are you sure?",
               // text: "You will not be able to recover this imaginary file!",
                type: "warning",
                showCancelButton: true,
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes, reject it!",
                cancelButtonText: "No, cancel plx!",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {

                    $.ajax({
                        url: '/fund-requests/update-status?id=<?= $model->id ?>&status=rejected',
                    });

                    swal({
                        title: "Rejected!",
                        text: "Fund Request has been Rejected.",
                        type: "success",
                        confirmButtonClass: "btn-success"
                    });
                } else {
                    swal({
                        title: "Cancelled",
                        text: "Fund Request has not been Rejected :)",
                        type: "error",
                        confirmButtonClass: "btn-danger"
                    });
                }
            });
    });

</script>
<script src="/js/sweetalert.min.js"></script>