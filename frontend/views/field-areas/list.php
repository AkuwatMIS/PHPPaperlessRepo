<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Actions */
/* @var $form yii\widgets\ActiveForm */
?>

<table class="table table-striped table-bordered detail-view">
    <thead>
    <th>Address</th>
    <th>Longitude</th>
    <th>Latitude</th>
    <th>Action</th>
    </thead>
    <tbody>
<?php foreach ($field_areas as $field_area) { ?>
    <tr>
        <td class='areas'><?=$field_area->name?></td>
        <td><?=$field_area->longitude?></td>
        <td><?=$field_area->latitude?></td>
        <td><button class ='delete btn btn-success' id='delete'>Delete</button></td>
    </tr>
<?php } ?>
    </tbody>
</table>
