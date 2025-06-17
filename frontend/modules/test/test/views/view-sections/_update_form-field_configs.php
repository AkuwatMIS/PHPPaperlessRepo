<?php
use yii\helpers\Html;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ViewSections */
/* @var $form yii\widgets\ActiveForm */
?>




    <?php

    DynamicFormWidget::begin([
        //'id' => 'dynamic-form-widget',
        'widgetContainer' => 'dynamicform_inner',
        'widgetBody' => '.container-rooms',
        'widgetItem' => '.room-item',
        //'limit' => 10,
        'min' => 1,
        'insertButton' => '.add-room',
        'deleteButton' => '.remove-room',
        'model' =>$modelsFieldsConfigs[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'key_name',
            'value',
            'parent_id',

        ],
    ]); ?>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>Key</th>
            <th>Value</th>
            <th>Parent Id</th>
            <th class="text-center">
                <button type="button" class="add-room btn btn-success btn-xs add-config"><span class="glyphicon glyphicon-plus"></span></button>
            </th>
        </tr>
        </thead>
        <tbody class="container-rooms">
        <?php foreach ($modelsFieldsConfigs as $indexFieldsConfig => $modelFieldsConfig): ?>
            <tr class="room-item">
                <td class="vcenter">
                    <?php
                    // necessary for update action.
                    if (! $modelFieldsConfig->isNewRecord) {
                        echo Html::activeHiddenInput($modelFieldsConfig, "[{$indexField}][{$indexFieldsConfig}]id", ['class'=> 'form-control key-id']);
                    }
                    ?>
                    <?= $form->field($modelFieldsConfig, "[{$indexField}][{$indexFieldsConfig}]key_name")->label(false)->textInput(['maxlength' => true, 'class'=> 'form-control keys']) ?>
                </td>
                <td>  <?= $form->field($modelFieldsConfig, "[{$indexField}][{$indexFieldsConfig}]value")->label(false)->textInput(['maxlength' => true]) ?> </td>
                <td> <?php echo $form->field($modelFieldsConfig, "[{$indexField}][{$indexFieldsConfig}]parent_id")->label(false)
                        ->dropDownList($list,[
                            'options' => [
                                    'prompt' => 'select...'
                            ],

                            'data-config_index'=> '0',
                            'class'=> 'form-control update-keys index-config-0' ,
                            'maxlength' => true
                        ])
                    /*$value = !empty($modelFieldsConfig->parent_id)? $modelFieldsConfig->parent_id:null;
                    echo $form->field($modelFieldsConfig, "[{$indexField}][{$indexFieldsConfig}]parent_id")->label(false)->widget(DepDrop::classname(), [
                        'type'=>DepDrop::TYPE_SELECT2,
                            'pluginOptions'=>[
                            'depends'=>['viewsectionfields-'.$indexField.'-table_name','viewsectionfields-'.$indexField.'-field'],
                            //'initialize' => true,
                            'initDepends'=>['viewsectionfields-'.$indexField.'-table_name','viewsectionfields-'.$indexField.'-field'],
                            'placeholder'=>'Select...',
                            'url'=>Url::to(['/view-sections/fetch-keys-by-column']),
                        ],
                        'data' => $value?[$modelFieldsConfig->parent_id => $value]:[]
                    ] );*/

                   /* echo $form->field($modelFieldsConfig, "[{$indexField}][{$indexFieldsConfig}]parent_id")->label(false)->widget(DepDrop::classname(), [
                        //'type'=>DepDrop::TYPE_SELECT2,
                            'pluginOptions'=>[
                            'depends'=>['viewsectionfields-'.$indexField.'-table_name', 'viewsectionfields-'.$indexField.'-field'],
                            //'initialize' => true,
                            'initDepends'=>['viewsectionfields-'.$indexField.'-table_name', 'viewsectionfields-'.$indexField.'-field'],
                            'placeholder'=>'Select...',
                            'url'=>Url::to(['/view-sections/fetch-keys-by-column']),
                        ],
                        'data' => $value?[$modelFieldsConfig->parent_id => $value]:[]
                    ] );*/
                    ?> </td>
                <td class="text-center vcenter" style="width: 90px;">
                    <button type="button" class="remove-room btn btn-danger btn-xs"><span class="glyphicon glyphicon-minus"></span></button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php DynamicFormWidget::end(); ?>


<?php
/*$this->registerJs("

 $('#viewsectionfields-".$indexField."-field').on('change', function(e, item) {
      
         var table = $('#viewsectionfields-".$indexField."-table_name').val();
      
         var field = $('#viewsectionfields-".$indexField."-field').val();
         console.log(table);
         console.log(field);
        $.ajax({
                url    : 'fetch-keys-by-column',
                type   : 'post',
                data: {keylist: [table,field]},
                success: function (data)
                {
                    var list = jQuery.parseJSON(data);
                    var list = (list['output']);
                         $('#sectionfieldsconfigs-".$indexField."-".$indexFieldsConfig."-parent_id').empty();
                    for (row in list) {
               
                        $('#sectionfieldsconfigs-".$indexField."-".$indexFieldsConfig."-parent_id').append($('<option></option>').attr('value', list[row]['id']).text(list[row]['name']));
                    }
                       
                },
                error  : function ()
                {
                    console.log('internal server error');
                }
            });
            return false;
    });
     
    ");*/

?>
