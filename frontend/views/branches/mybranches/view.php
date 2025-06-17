<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Branches', 'url' => ['branches-detail']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h3><?= Html::encode($this->title) ?></h3>
                </div>
            </div>
        </div>
    </header>

    <div class="box-typical box-typical-padding">
        <p>
            <?= Html::a('Update', ['/branches/quick-update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?php /*if(!$model->manager){ */?><!--
            <?/*= Html::a('Add Branch Manager', ['/branches/add-branch-manager', 'id' => $model->id], ['class' => 'btn btn-success']) */?>
        --><?php /*} */?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => 'Branch',
                    'value' => $model->name,
                ],
                [
                    'label' => 'Region',
                    'value' => $model->region->name,
                ],
                [
                    'label' => 'Area',
                    'value' => $model->area->name,
                ],
                [
                    'label' => 'Province',
                    'value' => $model->province->name,
                ],
                [
                    'label' => 'City',
                    'value' => $model->city->name,
                ],
                [
                    'label' => 'Division',
                    'value' => $model->division->name,
                ],
                [
                    'label' => 'District',
                    'value' => $model->district->name,
                ],
                /*[
                    'label' => 'Tehsil',
                    'value' => $model->tehsil->name,
                ],*/
                /*[
                    'label' => 'Country',
                    'value' => $model->country->name,
                ],*/
                'short_name',
                'mobile',
                [
                    'label' => 'Branch Code',
                    'value' => $model->code,
                ],
                [
                    'label' => 'Union Council',
                    'value' => $model->uc,
                ],
                'address',
                'latitude',
                'longitude',
                'description',
            ],
        ]) ?>
    </div>
</div>
<div class="branches-view">



</div>
