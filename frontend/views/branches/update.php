<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */
$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branches', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $this->title, 'url' => ['view', 'id' => $model->id]];
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
        'array'=>$array,
       // 'model_branchwitproject' => $model_branchwitproject,

    ]) ?>

</div>
</div>
