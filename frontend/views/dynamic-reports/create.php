<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\DynamicReports */

?>

<div class="container-fluid">

    <div class="box-typical box-typical-padding">

        <?= $this->render('_form', [
            'model' => $model,
            'reports_list' => $reports_list,
            'regions' => $regions,
            'projects' => $projects,
            'modelReferrals' => $modelReferrals
        ]) ?>
    </div>
</div>