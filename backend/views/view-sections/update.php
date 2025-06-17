<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\ViewSections */
?>
<div class="view-sections-update">

    <?= $this->render('_update_form', [
        'model' => $model,
        'modelsFields' => $modelsFields,
        'modelsFieldsConfigs' => $modelsFieldsConfigs,
        'tables_list' => $tables_list,
    ]) ?>

</div>
