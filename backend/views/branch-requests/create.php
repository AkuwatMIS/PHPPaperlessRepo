<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */

?>
<div class="branch-requests-create">
    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array,

    ]) ?>
</div>
