<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\AwpBranchSustainability */

$this->title = 'Create Awp Branch Sustainability';
$this->params['breadcrumbs'][] = ['label' => 'Awp Branch Sustainabilities', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="awp-branch-sustainability-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
