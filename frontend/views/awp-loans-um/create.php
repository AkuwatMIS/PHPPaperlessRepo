<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AwpLoansUm */

$this->title = 'Create Awp Loans Um';
$this->params['breadcrumbs'][] = ['label' => 'Awp Loans Ums', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-loans-um-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
