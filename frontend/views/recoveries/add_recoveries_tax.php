<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Recoveries */
$this->title = 'Post Recoveries';
$this->params['breadcrumbs'][] = ['label' => 'Recoveries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Post Recovered Tax</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_form_recoveries_tax', [
            'model' => $modelsRecovery,
            'branches' => $branches,
        ]) ?>
    </div>
</div>
