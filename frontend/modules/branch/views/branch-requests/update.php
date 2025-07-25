<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */

$this->title = 'Update Branch Requests: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branch Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4><?= Html::encode($this->title) ?></h4>
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
