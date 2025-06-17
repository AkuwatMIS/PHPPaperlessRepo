<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 11/6/2019
 * Time: 4:04 PM
 */?>


<?= \yii\helpers\Html::a('Send Email', ['forgot-password', 'id' => $id],['class' => 'btn btn-success','role'=>'modal-remote','data-toggle'=>'tooltip','title'=>'Forgot Password'])?>

<?= \yii\helpers\Html::a('Send Message', ['forgot-password-sms', 'id' => $id],['class' => 'btn btn-success','role'=>'modal-remote','data-toggle'=>'tooltip','title'=>'Forgot Password'])?>

