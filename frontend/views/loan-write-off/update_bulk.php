<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\LoanWriteOff */

$this->title = 'Approve/Reject: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Loan Write Offs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="loan-write-off-update">
   
    <?=
    $this->render('update_form_bulk', [
        'model' => $model,
        'idArray' => $idArray,
    ]) ?>

</div>
