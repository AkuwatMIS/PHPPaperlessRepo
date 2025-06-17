<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Actions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tables-form">
    <?= Html::beginForm(); ?>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <?= Html::label('Table Type')?>
                <?=Html::dropDownList('table_type',null,$table_type,['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <?= Html::label('Table Name')?>
                <?= Html::textInput('table_name',null, ['class' => 'form-control']) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <?= Html::label('Generate Model')?>
                <?= Html::checkbox('gen_model', false,['class' => 'checkbox', 'style'=> ['width'=> '20px','height' => '20px']]) ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <?= Html::label('Generate Crud')?>
                <?= Html::checkbox('gen_crud', false,['class' => 'checkbox', 'style'=> ['width'=> '20px','height' => '20px']]) ?>
            </div>
        </div>
    </div>
    <table id="myTable" class=" table table-bordered table-stripped order-list">
        <thead>
        <th>Column Name</th>
        <th>Type</th>
        <th>Length</th>
        <th>Default Value</th>
        <th>Null</th>
        <th><button type="button" class="add-item btn btn-success btn-xs" id="addrow"><i class="glyphicon glyphicon-plus"></i></button></th>
        </thead>
        <tbody>
        <tr>
            <td><?= Html::textInput('att[0][col_name]', null, ['class' => 'form-control']) ?></td>
            <td><?= Html::dropDownList('att[0][data_type]',null,$types, ['class' => 'form-control']) ?></td>
            <td><?= Html::textInput('att[0][length]',null, ['class' => 'form-control']) ?></td>
            <td><?= Html::dropDownList('att[0][default_value]',null,   $default, ['class' => 'form-control']) ?></td>
            <td><?= Html::checkbox('att[0][is_null]', false,['class' => 'form-control', 'style'=> ['width'=> '20px','height' => '30px']]) ?></td>
            <!--<td><?/*= Html::radio('att[0][is_ai]',false,['class' => 'form-control']) */?></td>-->
            <td><a class="deleteRow"></a></td>
        </tr>
        </tbody>
    </table>


    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= Html::submitButton( 'Create', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
    <?php Html::endForm(); ?>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script>
    $(document).ready(function () {
        var counter = 1;
        var defaults = <?php echo json_encode($default) ?>;
        $.each(defaults, function(key, value) {
            console.log('stuff : ' + key  + value);
        });
        var types = <?php echo json_encode($types) ?>;

        $('#addrow').on('click', function () {
            var newRow = $('<tr>');
            var cols = '';
            cols += '<td><input type="text" class="form-control" name="att[' + counter + '][col_name]"/></td>';
            cols += '<td><select type="text" class="form-control" name="att[' + counter + '][data_type]">';
            $.each(types, function(key, value) {
                cols += '<option class="form-control" value= '+ key +' > '+ value +'</option>';
            });
            cols += '</select></td>';
            cols += '<td><input type="text" class="form-control" name="att[' + counter + '][length]"/></td>';
            cols += '<td><select type="text" class="form-control" name="att[' + counter + '][default_value]">';
            $.each(defaults, function(key, value) {
                cols += '<option class="form-control" value= '+ key +' > '+ value +'</option>';
            });
            cols += '</select></td>';
            cols += '<td><input type="checkbox" class="form-control" style="width: 20px;height:30px;" name="att[' + counter + '][is_null]"/></td>';
            cols += '<td><input type="button" class="ibtnDel btn btn-md btn-danger "  value="Delete"></td>';
            newRow.append(cols);
            $('#myTable').append(newRow);
            counter++;
        });
        $('#myTable').on('click', '.ibtnDel', function (event) {
            $(this).closest('tr').remove();
            counter -= 1
        });
    });
</script>
<?php
/*$this->registerJs(
    " $(document).ready(function () {
    var counter = 1;
    var words =  echo json_encode($default);
        $.each(words, function(key, value) {
            console.log('stuff : ' + key  + value);

        });
    $('#addrow').on('click', function () {
    var newRow = $('<tr>');
        var cols = '';
        cols += '<td><input type=\"text\" class=\"form-control\" name=\"att[' + counter + '][col_name]\"/></td>';
        cols += '<td><select type=\"text\" class=\"form-control\" name=\"att[' + counter + '][data_type]\"></select></td>';
        cols += '<td><input type=\"text\" class=\"form-control\" name=\"att[' + counter + '][length]\"/></td>';
        cols += '<td><select type=\"text\" class=\"form-control\" name=\"att[' + counter + '][default_value]\"></select></td>';
        cols += '<td><input type=\"checkbox\" class=\"form-control\" name=\"att[' + counter + '][is_null]\"/></td>';
        cols += '<td><input type=\"radio\" class=\"form-control\" name=\"att[' + counter + '][is_ai]\"/></td>';
        cols += '<td><input type=\"button\" class=\"ibtnDel btn btn-md btn-danger \"  value=\"Delete\"></td>';
        newRow.append(cols);
        $('#myTable').append(newRow);
        counter++;
    });
    $('#myTable').on('click', '.ibtnDel', function (event) {
        $(this).closest('tr').remove();       
        counter -= 1
    });
    });");
*/?>
