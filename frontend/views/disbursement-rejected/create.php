<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DisbursementRejected */

$this->title = 'Create Disbursement Rejected';
$this->params['breadcrumbs'][] = ['label' => 'Disbursement Rejecteds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disbursement-rejected-create">
    <?php if (!Yii::$app->request->isAjax) { ?>
        <h4><?= Html::encode($this->title) ?></h4>
    <?php } ?>
    <?= $this->render('_form', [
        'model' => $model,
        'disbursement_detail_id'=>$disbursement_detail_id,
        'project_id'=>$project_id
    ]) ?>

</div>
