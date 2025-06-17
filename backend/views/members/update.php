<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Members */
?>
<div class="members-update">

    <?= $this->render('_form', [
        'model' => $model,
        'membersAddress' => $membersAddress,
        'membersPhone' => $membersPhone,
        'membersEmail' => $membersEmail,
        'membersAccount' => $membersAccount,
        'branches' => $branches,
    ]) ?>

</div>
