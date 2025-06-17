<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\UserTransfers */

$this->title = 'Create User Transfers';
$this->params['breadcrumbs'][] = ['label' => 'User Transfers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
        'types' => $types,
        'users_data' => $users_data,
        'designations' => $designations,
        'divisions' => $divisions,
    ]) ?>
    </div>

</div>
