<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\MobilePermissions */

$this->title = 'Create Mobile Permissions';
$this->params['breadcrumbs'][] = ['label' => 'Mobile Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mobile-permissions-create">

    <div class="box-body no-padding">
        <div class="designations-create">
            <?= $this->render('_form', [
                'mobile_screens' => $mobile_screens,
                'mobile_permissions' => $mobile_permissions,
            ]) ?>
        </div>

    </div>
</div>
