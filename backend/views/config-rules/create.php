<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\ConfigRules */

?>
<div class="config-rules-create">
    <?= $this->render('_form', [
        'model' => $model,
        'array'=>$array,
    ]) ?>
</div>
