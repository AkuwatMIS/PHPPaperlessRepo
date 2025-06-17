<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\search\UsersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="users-search">
    <?php $form = ActiveForm::begin([
       // 'action' => ['userlist','name' =>$name],
        'method' => 'get',
        /*'data-pjax' => '',*/
    ]); ?>
    <?php // echo $form->field($model, 'id') ?>
<div class="row">
    <div class="form-group">
    <?= $form->field($model, 'username') ?>
    </div>

    <?php // echo $form->field($model, 'password') ?>

    <?php // echo $form->field($model, 'auth_key') ?>

    <?php // echo $form->field($model, 'password_hash') ?>

    <?php // echo $form->field($model, 'password_reset_token') ?>

    <?php // echo $form->field($model, 'last_login_at') ?>

    <?php // echo $form->field($model, 'last_login_token') ?>

    <?php // echo $form->field($model, 'access_token') ?>

    <?php // echo $form->field($model, 'fullname') ?>

    <?php // echo $form->field($model, 'father_name') ?>
    <div class="form-group">
    <?= $form->field($model, 'email') ?>
    </div>
    <?php // echo $form->field($model, 'image') ?>

    <?php // echo $form->field($model, 'mobile') ?>

    <?php // echo $form->field($model, 'joining_date') ?>

    <?php // echo $form->field($model, 'role') ?>

    <?php // echo $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'modified') ?>

    <?php // echo $form->field($model, 'designation_id') ?>

    <?php // echo $form->field($model, 'emp_code') ?>

    <?php // echo $form->field($model, 'branch_id') ?>

    <?php // echo $form->field($model, 'area_id') ?>

    <?php // echo $form->field($model, 'region_id') ?>

    <?php // echo $form->field($model, 'isblock') ?>

    <?php // echo $form->field($model, 'reason') ?>

    <?php // echo $form->field($model, 'block_date') ?>

    <?php // echo $form->field($model, 'team_name') ?>

    <?php // echo $form->field($model, 'status') ?>

    <?php // echo $form->field($model, 'created_on') ?>

    <?php // echo $form->field($model, 'updated_on') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'created_at') ?>
</div>
    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
