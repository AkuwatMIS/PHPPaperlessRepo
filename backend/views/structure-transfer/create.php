<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\StructureTransfer */

?>
<div class="structure-transfer-create">
    <?= $this->render('_form', [
        'model' => $model,
        'obj_types' => $obj_types,
    ]) ?>
</div>
