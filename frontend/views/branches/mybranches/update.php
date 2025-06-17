<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */

$this->title = 'Update Branches: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branches', 'url' => ['branches-detail']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['quick-view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>


<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3>Update Branches</h3>
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