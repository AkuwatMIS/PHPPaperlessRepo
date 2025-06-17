<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\RandomMembers */
?>
<div class="random-members-update">

    <?= $this->render('_form', [
        'model' => $model,
        'array'=>([
            'provinces'=>$provinces,
            'cities'=>$cities,
        ]),

    ]) ?>

</div>
