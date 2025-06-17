<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserHierarchyChangeRequest */

$this->title = 'Create User Hierarchy Change Request';
$this->params['breadcrumbs'][] = ['label' => 'User Hierarchy Change Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-hierarchy-change-request-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
