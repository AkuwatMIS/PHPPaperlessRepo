<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Roles */

$this->title = 'Create Roles';
$this->params['breadcrumbs'][] = ['label' => 'Roles', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="roles-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form_role', [
        'model' => $model,
    ]) ?>

</div>
