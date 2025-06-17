<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\MobilePermissions */

$this->title = 'Update Mobile Permissions: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Mobile Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="mobile-permissions-update">

    <?= $this->render('_form', [
        'mobile_screens' => $mobile_screens,
        'mobile_permissions' => $mobile_permissions,
    ]) ?>

</div>
