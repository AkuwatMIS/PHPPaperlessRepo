<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Applications */
$this->title = $model->application_no;
$this->params['breadcrumbs'][] = ['label' => 'Applications', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3>Update Applications</h3>
                </div>
            </div>
        </div>
    </header>

    <div class="box-typical box-typical-padding">

        <?= $this->render('_form', [
            'model' => $model,
            'projects' => $projects,
            'branches'=>$branches,
            'cib_model'=>$cib_model
        ]) ?>

    </div>
</div>
