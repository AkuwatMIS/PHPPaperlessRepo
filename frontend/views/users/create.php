<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Members */
$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3>Create User</h3>
                </div>
            </div>
        </div>
    </header>

    <div class="box-typical box-typical-padding">

        <?= $this->render('_form_create', [
            'model' => $model,
            'array'=>$array
        ]) ?>

    </div>
</div>
