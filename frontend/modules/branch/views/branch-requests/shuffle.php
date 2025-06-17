<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */

$this->title = 'Create Branch Requests';
$this->params['breadcrumbs'][] = ['label' => 'Branch Requests', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Branch Shuffle Request</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">

    <?= $this->render('_form_shuffle', [
        'model' => $model,
    ]) ?>

    </div>
</div>
