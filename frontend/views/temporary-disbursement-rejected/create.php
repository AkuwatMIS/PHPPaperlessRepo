<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DisbursementRejected */

$this->title = 'Create Temporary Disbursement Rejected';
$this->params['breadcrumbs'][] = ['label' => 'Temporary Disbursement Rejected', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disbursement-rejected-create">
    <?php if (!Yii::$app->request->isAjax) { ?>
        <h4><?= Html::encode($this->title) ?></h4>
    <?php } ?>
    <?= $this->render('_form', [
        'model' => $model,
        'disbursement_detail_id'=>$disbursement_detail_id,
        'tranche_no' => $tranche_no,
    ]) ?>

</div>
