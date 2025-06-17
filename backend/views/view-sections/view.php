<?php

use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\ViewSections */
?>
<div class="view-sections-view">
 
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'section_name',
            'section_description:ntext',
            'section_table_name',
            'sort_order',
            /*'assigned_to',
            'created_by',
            'created_at',
            'updated_at',*/
        ],
    ]) ?>

    <table class="table table-striped table-bordered detail-view">
        <thead>
        <th>Table</th>
        <th>Field</th>
        </thead>
        <tbody>
        <?php
        foreach ($section_fields as $section_field)
        { ?>
            <tr>
                <td><?=$section_field->table_name?></td>
                <td><?=$section_field->field?></td>
            </tr>
        <?php }?>
        </tbody>
    </table>

    <!--<table class="table table-striped table-bordered detail-view">
        <thead>
        <th>Key Name</th>
        <th>Value</th>
        </thead>
        <tbody>
        <?php
/*        foreach ($section_fields as $section_field)
        {
            foreach ($section_field->sectionFieldsConfigs as $fieldsConfig)
        {*/?>
            <tr>
                <td><?/*=$fieldsConfig->key_name*/?></td>
                <td><?/*=$fieldsConfig->value*/?></td>
            </tr>

        <?php /*} } */?>

        </tbody>
    </table>-->
</div>
