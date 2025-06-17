<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Blacklist */
$this->title = $model->cnic;
$this->params['breadcrumbs'][] = ['label' => 'Blacklist', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Add to Blacklist</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <div class="blacklist-update">

            <?= $this->render('_form', [
                'model' => $model,
            ]) ?>
        </div>
    </div>
</div>