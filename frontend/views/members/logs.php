<?php

use yii\helpers\Html;
use common\widgets\LogsWidget;

/* @var $this yii\web\View */
/* @var $model common\models\ViewSections */

?>
<div class="container-fluid">
<div class="view-logs">
    <?= LogsWidget::widget(['table' => 'members','field'=> $field, 'id' => $id]) ?>
</div>
</div>