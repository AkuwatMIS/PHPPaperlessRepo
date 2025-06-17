<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\UserTransfers */

$this->title = 'Update User Transfers: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'User Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="container-fluid">

    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>User Management</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_form', [
            'model' => $model,
        ]) ?>
    </div>


</div>
