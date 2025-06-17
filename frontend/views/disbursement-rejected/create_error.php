<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DisbursementRejected */

$this->title = 'Disbursement Rejected';
$this->params['breadcrumbs'][] = ['label' => 'Disbursement Rejecteds', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="disbursement-rejected-create">
    <?php if (!Yii::$app->request->isAjax) { ?>
        <h4><?= Html::encode($this->title) ?></h4>
    <?php } ?>
    <p style="color: red;font-weight: bold">Disbursement for this loan is not found, please contact team.</p>

</div>
