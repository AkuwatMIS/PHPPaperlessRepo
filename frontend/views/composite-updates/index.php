<?php

use yii\bootstrap\Modal;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Loans';
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
?>
<div class="container-fluid">
    <div class="box-typical box-typical-padding">
        <p>Select from below boxes for update Operation</p>
        <div class="row">
            <div class="card text-white bg-primary mb-3" style="max-width: 20rem;margin-left: 20px">
                <div class="card-header">MEMBER</div>
                <div class="card-body">
                    <h5 class="card-title">
                        <a class="comp-update" href="/composite-updates/member-search" style="color: white;">
                            <span class="lbl">Update</span>
                        </a>
                    </h5>
                    <p class="card-text">ممبر کی انفارمیشن کو اپلیکیشن کی منظوری سے پہلے تک اپڈیٹ کیا جا سکتا ہے۔</p>
                </div>
            </div>
            <div class="card text-white bg-success mb-3" style="max-width: 20rem;margin-left: 20px">
                <div class="card-header">APPLICATION</div>
                <div class="card-body">
                    <h5 class="card-title">
                        <a class="comp-update" href="/composite-updates/application-search" style="color: white;">
                            <span class="lbl">Update / Reject</span>
                        </a>
                    </h5>
                    <p class="card-text">ممبر کی درخواست کو لون بننے سے پہلے تک اپڈیٹ کیا جا سکتا ہے۔۔اور اگر ممبر کی
                        کوئی انفارمیشن غلط ہے تو اپلیکیشن کینسل کر کے نیو اپلیکیشن انڑرکرنا  ہوگی۔</p>
                </div>
            </div>
            <div class="card text-white bg-danger mb-3" style="max-width: 20rem;margin-left: 20px">
                <div class="card-header">APPRAISALS</div>
                <div class="card-body">
                    <h5 class="card-title">
                        <a class="comp-update" href="/composite-updates/appraisal-search" style="color: white;">
                            <span class="lbl">Update / Delete</span>
                        </a>
                    </h5>
                    <p class="card-text">اپپریزلز کو لون بننے سے پہلے تک اپڈیٹ کیا جا سکتا ہے اس کے بعد کوئی
                        تبدیلی نہی ہو گی</p>
                </div>
            </div>
            <div class="card text-white bg-warning mb-3" style="max-width: 20rem;margin-left: 20px">
                <div class="card-header">LOAN</div>
                <div class="card-body">
                    <h5 class="card-title">
                        <a class="comp-update" href="/composite-updates/composite-loan-search" style="color: white;">
                            <span class="lbl">Update / Delete / Reject</span>
                        </a>
                    </h5>
                    <p class="card-text">لون اگر ایکٹیو یا پبلش نہی ہے تو اسے ڈیلیٹ کر کے اسکی درخواست کو اپڈیٹ کیا جائے
                        گا۔ اور لون درست معلومات کے ساتھ دوبارہ بنایا جائے گا۔</p>
                </div>
            </div>
            <div class="card text-white bg-info mb-3" style="max-width: 20rem;margin-left: 20px">
                <div class="card-header">LOAN TRANCHE</div>
                <div class="card-body">
                    <h5 class="card-title">
                        <a class="comp-update" href="/composite-updates/composite-tranche-search" style="color: white;">
                            <span class="lbl">Create / Delete</span>
                        </a>
                    </h5>
                    <p class="card-text">Project manager will create or delete tranche</p>
                </div>
            </div>
            <div class="card text-white bg-warning mb-3" style="max-width: 20rem;margin-left: 20px">
                <div class="card-header">RECOVERY</div>
                <div class="card-body">
                    <h5 class="card-title">
                        <a class="comp-update" href="/composite-updates/composite-recovery-index" style="color: white;">
                            <span class="lbl">Update / Delete</span>
                        </a>
                    </h5>
                    <p class="card-text">Previous month recovery could be deleted by project manager or by providing
                        date and sanction No.</p>
                </div>
            </div>
            <div class="card text-white bg-primary mb-3" style="max-width: 20rem;margin-left: 20px">
                <div class="card-header">DONATION</div>
                <div class="card-body">
                    <h5 class="card-title">
                        <a class="comp-update" href="/composite-updates/composite-donation-index" style="color: white;">
                            <span class="lbl">Update / Delete</span>
                        </a>
                    </h5>
                    <p class="card-text">Previous month donation could be deleted by project manager or by providing
                        date and sanction No..</p>
                </div>
            </div>
        </div>
    </div>
</div>
