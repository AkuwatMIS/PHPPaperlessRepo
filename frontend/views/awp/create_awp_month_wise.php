<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Borrowers */

?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <strong> Monthly Work Plan ( <?php  echo (date('F,Y')) ;?> )</strong>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <h3><?= ($branch_id != 0) ?'<strong>Branch Name:</strong> '.\common\models\Branches::find()->where(['id'=>$branch_id])->one()->name : '' ?></h3>
        <div class="awp-create">
            <?= $this->render('_form_awp_month_wise', [
                'model' => $model,
                'branches'=> $branches,
                'branch_id'=>$branch_id,
            ]) ?>

        </div>
</div>