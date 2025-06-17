<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserHierarchyChangeRequest */

$this->title = 'Update User Hierarchy Change Request: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Hierarchy Change Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="user-hierarchy-change-request-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
