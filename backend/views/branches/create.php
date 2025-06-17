<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Branches */

?>
<div class="branches-create">
    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array,
        //'model_branchwitproject' => $model_branchwitproject,

    ]) ?>
</div>
