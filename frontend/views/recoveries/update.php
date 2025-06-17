<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Recoveries */
?>
<?php if (Yii::$app->session->hasFlash('success')): ?>
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <?= Yii::$app->session->getFlash('success') ?>
    </div>
<?php endif; ?>

// display error message
<?php if (Yii::$app->session->hasFlash('error')): ?>
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        <?= Yii::$app->session->getFlash('error') ?>
    </div>
<?php endif; ?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3>Update Recovery</h3>
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
