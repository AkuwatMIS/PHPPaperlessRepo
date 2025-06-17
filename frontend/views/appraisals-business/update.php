<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\AppraisalsBusiness */
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>Update Business Appraisal</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="box-typical box-typical-padding">
        <?= $this->render('_form', [
            'model' => $model,
            'fixed_business_assets_dropdown' => $fixed_business_assets_dropdown,
            'running_capital_dropdown' => $running_capital_dropdown,
            'business_expenses_dropdown' => $business_expenses_dropdown,
            'new_required_assets_dropdown' => $new_required_assets_dropdown,
        ]) ?>
    </div>
</div>

