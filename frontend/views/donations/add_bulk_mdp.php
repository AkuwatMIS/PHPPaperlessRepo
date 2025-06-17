<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Recoveries */
$this->title = 'Post Donations';
$this->params['breadcrumbs'][] = ['label' => 'Donations', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Post Donation(MDP)</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
    <?= $this->render('_form_bulk_mdp', [
        'model' => $modelsDonation,
        'branches' => $branches,
        'projects' => $projects,

    ]) ?>
</div>
</div>
