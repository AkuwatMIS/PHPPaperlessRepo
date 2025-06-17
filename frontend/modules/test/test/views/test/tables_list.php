<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Actions */

$this->title = 'Tables List';
$this->params['breadcrumbs'][] = ['label' => 'TablesList', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tables-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <table id="myTable" class=" table table-bordered table-stripped order-list">
        <thead>
        <th>Table Name</th>
        <th>Add columns</th>
        </thead>
        <tbody>

        <?php if(isset($tables_list)) {
            foreach ($tables_list as $table_list) { ?>
        <tr>
            <td><?= Html::label($table_list) ?></td>
            <td><?= Html::a('Add More Columns',['test/update','table_name'=>$table_list]) ?></td>
        </tr>
        <?php } } ?>
        </tbody>
    </table>

</div>
