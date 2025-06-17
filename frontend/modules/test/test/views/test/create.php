<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Actions */

$this->title = 'Create Tables';
$this->params['breadcrumbs'][] = ['label' => 'Tables', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tables-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
            'types' => $types,
        'default' => $default,
        'table_type' => $table_type,

    ]) ?>

</div>
