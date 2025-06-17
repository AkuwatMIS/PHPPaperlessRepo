<?php

use app\models\Branches;
use kartik\date\DatePicker;
use kartik\depdrop\DepDrop;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\search\LoansSearch */
/* @var $form yii\widgets\ActiveForm */
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3>Update Bank Accounts</h3>
                </div>
            </div>
        </div>
    </header>

    <div class="box-typical box-typical-padding">

        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>

    </div>
</div>
