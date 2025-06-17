<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\depdrop\DepDrop;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ViewSections */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="view-sections-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'section_name')->textInput() ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'section_table_name')->dropDownList($tables_list, ['prompt' => 'Select Table']) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'sort_order')->textInput() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'section_description')->textarea(['rows' => 4]) ?>
        </div>
    </div>

    <?php DynamicFormWidget::begin([
        //'id' => 'dynamic-form-widget',
        'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-items', // required: css class selector
        'widgetItem' => '.section-field-item', // required: css class
        'limit' => 40, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-section-field', // css class
        'deleteButton' => '.remove-section-field', // css class
        'model' => $modelsFields[0],
        'formId' => 'dynamic-form',
        'formFields' => [
            'section_id',
            'table_name',
            'field',
        ],
    ]); ?>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th style="width: 30px;">Sort Order</th>
            <th>Table</th>
            <th>Fields</th>
            <th>Configs</th>
            <th class="text-center" style="width: 90px;">
                <button type="button" class="add-section-field btn btn-success btn-xs"><span class="glyphicon glyphicon-plus"></span></button>
            </th>
        </tr>
        </thead>
        <tbody class="container-items">

        <?php foreach ($modelsFields as $indexField => $modelField):  ?>
            <tr class="section-field-item">
                <td><?=  $form->field($modelField, "[{$indexField}]sort_order")->textInput(['class'=> 'form-control sorting'])->label(false) ?></td>
                <td><?= $form->field($modelField, "[{$indexField}]table_name")->dropDownList($tables_list, ['data-field_index' => '0', 'prompt' => 'Select Table', 'class' => 'form-control table-selection  update-trigger '])->label(false) ?></td>

                <td class="vcenter">

                    <?php
                    if (!$modelField->isNewRecord) {
                        echo Html::activeHiddenInput($modelField, "[{$indexField}]id");
                    }
                    ?>
                    <?php
                    $value = !empty($modelField->field) ? $modelField->field : null;
                    echo $form->field($modelField, "[{$indexField}]field")->label(false)->widget(DepDrop::classname(), [
                        //'type'=>DepDrop::TYPE_SELECT2,
                        'options' => ['class' => 'form-control update-trigger field-selection', 'data-field_index' => '0'],
                        'pluginOptions' => [
                            'depends' => ['viewsectionfields-' . $indexField . '-table_name'],
                            'initialize' => true,
                            'initDepends' => ['viewsectionfields-' . $indexField . '-table_name'],
                            'placeholder' => 'Select...',
                            'url' => Url::to(['/test/view-sections/fetch-columns-by-table']),
                        ],
                        'data' => $value ? [$modelField->field => $value] : []
                    ]);
                    ?>
                </td>
                <td>
                    <?= $this->render('_update_form-field_configs', [
                        'form' => $form,
                        'list' => [],
                        'indexField' => $indexField,
                        'modelsFieldsConfigs' => $modelsFieldsConfigs[$indexField],
                    ]) ?>
                </td>
                <td class="text-center vcenter" style="width: 90px; verti">
                    <button type="button" class="remove-section-field btn btn-danger btn-xs"><span
                                class="glyphicon glyphicon-minus"></span></button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php DynamicFormWidget::end(); ?>



    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>

</div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<?php
$this->registerJs("


    var field_count = 0;
    var config_count = 1;
    var config_count1 = 1;
    var temp = 0;
    
    function getUrlVar(key){
        var result = new RegExp(key + \"=([^&]*)\", \"i\").exec(window.location.search); 
        return result && unescape(result[1]) || \"\"; 
    }
    var section_id = getUrlVar('id');
    //console.log('section_id = '+ section_id);
    
   
    $('.section-field-item').find('.table-selection','.field-selection').each(function(){
         $(this).data('field_index',field_count);
          field_count++; 
    });

    
    
    $('.section-field-item').find('.update-keys').each(function(){
         var id = $(this).attr('id');
         //console.log(id);
         var arrStr = id.split(/[--]/);
         $(this).attr('class', 'form-control update-keys index-config-'+arrStr[1]);  
         //var field_index = arrStr[1];
            
                  
    });
    
    var keys_load = function () {
    $('.section-field-item').each(function(){
         
         var field_index = $(this).find('.table-selection').data('field_index');
         var table_selection  = $(this).find('#viewsectionfields-'+field_index+'-table_name').val();
         var field_selection  = $(this).find('#viewsectionfields-'+field_index+'-field :selected').val();
         
         $(this).find('.index-config-'+ field_index).each(function() {
            var key = $(this).closest('.room-item').find('.key-id').val();
            var id = $(this).attr('id');
            $.ajax({
               url    : '?r=test/view-sections/fetch-keys-by-column1',
               type   : 'post',
               data: {keylist: [section_id,table_selection,field_selection,key]},
               success: function (data)
               {
                   var list1 = jQuery.parseJSON(data);
                   var list = (list1['output']);
                   var select = (list1['selected']);
                   //console.log(list);
              
           
                   $('#'+id).empty();
                   $('#'+id).append($('<option></option>').attr('value', '0').text('select...'));
                   var index_dep_field = 0;
                   var index_actions = 0;
                   var optgroup = '';
                   for (row in list) {
                   
                     if(list[row]['name'] == 'dependent_question')
                     { 
                        index_dep_field++;
                        optgroup = $('<optgroup>');
                        optgroup.attr('label',index_dep_field);
               
                        var option = $(\"<option></option>\");
                        option.val(list[row]['id']);
                        option.text(list[row]['name']);

                        optgroup.append(option);
                        //$('#'+id).append(optgroup);
                         // index_dep_field++;
                           //$('#'+id).append($('<optgroup></optgroup>').attr('label', index_dep_field).append($('<option></option>').attr('value', list[row]['id']).text(list[row]['name'])));
                     } 
                     else if(list[row]['name'] == 'actions')
                      {
                         var option = $(\"<option></option>\");
                          option.val(list[row]['id']);
                           option.text(list[row]['name']);
    
                            optgroup.append(option);
                            $('#'+id).append(optgroup);
                        
                      }
                      else{
                      
                       $('#'+id).append($('<option></option>').attr('value', list[row]['id']).text(list[row]['name']));
                       }
                        
                   }
                   $('#'+id + ' option').each(function(){
                        if (select != null)
                        {
                           if($(this).val() == select['id'])
                           {
                                $(this).attr('selected','selected');
                           }
                       }
                    }); 

               },
               error: function ()
               {
                   console.log('internal server error');
               }
           });
           
           
         });                 
    });
    } 
   
    setTimeout(keys_load, 4000);
    
    $('.dynamicform_wrapper').on('beforeInsert', function(e, item) {
        console.log('dynamicform_wrapper beforeInsert');
    });
    
    $('.dynamicform_inner').on('afterInsert', function(e, item) {
            //alert('.dynamicform_inner afterInsert 1');
            //console.log(item);
            var id = $(item).find('.update-keys').attr('id');
            var arrStr = id.split(/[--]/);
            
            $(item).find('.update-keys').attr('class', 'form-control update-keys index-config-'+arrStr[1]); 
            var after_insert_keys_load = function () {
                var table_selection = $(item).closest('.section-field-item').find('.table-selection').val();
                var field_selection = $(item).closest('.section-field-item').find('.field-selection').val();
                var id = $(item).find('.update-keys').attr('id');
               /* console.log(table_selection);
                console.log(field_selection);
                console.log( id);*/
                $.ajax({
                   url    : '?r=test/view-sections/fetch-keys-by-column',
                   type   : 'post',
                   data: {keylist: [section_id,table_selection,field_selection]},
                   success: function (data)
                   {
                       var list1 = jQuery.parseJSON(data);
                       var list = (list1['output']);
                  
                       $('#'+id).empty();
                       $('#'+id).append($('<option></option>').attr('value', '0').text('select...'));
                       for (row in list) {
                          
                           $('#'+id).append($('<option></option>').attr('value', list[row]['id']).text(list[row]['name']));
                            
                       }
                   },
                   error: function ()
                   {
                       console.log('internal server error');
                   }
               });
           }
            setTimeout(after_insert_keys_load, 4000);
    });
    
     $('.dynamicform_wrapper').on('afterInsert', function(e, item) {
        console.log('dynamicform_wrapper afterInsert');
        var id_name = $(item).find('.table-selection').attr('id');
        var str = id_name.split(/[--]/);
        $(item).find('.sorting').val(str[1]);
        $(item).find('.dynamicform_inner').on('afterInsert', function(e, item) {
            //alert('dynamicform_inner afterInsert');
             var id = $(item).find('.update-keys').attr('id');
             var arrStr = id.split(/[--]/);
             $(item).find('.update-keys').attr('class', 'form-control update-keys index-config-'+arrStr[1]);  
             
             var field_index = arrStr[1];
            
            var table_selection  = $('#viewsectionfields-'+field_index+'-table_name').val();
            var field_selection  = $('#viewsectionfields-'+field_index+'-field').val();
            var id = $(item).find('.update-keys').attr('id');
            //console.log(id);
            /*console.log('index :'+ field_index);
            console.log('table :'+ table_selection);
            console.log('field :'+ field_selection);*/
            $.ajax({
               url    : '?r=test/view-sections/fetch-keys-by-column',
               type   : 'post',
               data: {keylist: [section_id,table_selection,field_selection]},
               success: function (data)
               {
                   var list = jQuery.parseJSON(data);
                   var list = (list['output']);
                   $('#'+id).empty();
                   $('#'+id).append($('<option></option>').attr('value', '0').text('select...'));
                   for (row in list) {

                      $('#'+id).append($('<option></option>').attr('value', list[row]['id']).text(list[row]['name']));
                   }

               },
               error: function ()
               {
                   console.log('internal server error');
               }
           });      
           trigger_dep_drop();
        });
         
        $(item).find('.update-trigger').each(function(){
            $(this).data('field_index',field_count);
        });
        
       var update_key = function (){ 
            $(item).find('.update-keys').each(function(){
                var id = $(this).attr('id');
                var arrStr = id.split(/[--]/);
                $(this).data('config_index',arrStr[1]);
                $(this).attr('class', 'form-control update-keys index-config-'+arrStr[1]);
                console.log('index = ' +$(this).data('config_index'));
            });
        }
        update_key();
        //console.log('field_count = '+field_count);
        //console.log('config_count = '+config_count);
        
        field_count++;
        config_count++;
        trigger_dep_drop();
       
    });
   
    $('.dynamicform_wrapper').on('beforeDelete', function(e, item) {
        if (! confirm('Are you sure you want to delete this item?')) {
            return false;
        }
        return true;
    });

    $('.dynamicform_wrapper').on('afterDelete', function(e) {
        console.log('dynamicform_wrapper Deleted item!');
    });

    $('.dynamicform_wrapper').on('limitReached', function(e, item) {
        alert('dynamicform_wrapper Limit reached');
    });
    
    
    $('.dynamicform_inner').on('beforeInsert', function(e, item) {
        //alert('dynamicform_inner beforeInsert');
    });

   
    
    
    $('.dynamicform_inner').on('beforeDelete', function(e, item) {
        if (! confirm('Are you sure you want to delete this item?')) {
            return false;
        }
        return true;
    });

    $('.dynamicform_inner').on('afterDelete', function(e) {
        console.log('dynamicform_inner afterInsert Deleted item!');
    });

    $('.dynamicform_inner').on('limitReached', function(e, item) {
        alert('dynamicform_inner afterInsert Limit reached');
    });
    
   var trigger_dep_drop = function(){
   
       $('.update-trigger').change(function () {
          
           // console.log($('.update-trigger '));
            var field_index = $(this).closest('.section-field-item').find('.update-trigger').data('field_index');
            var table_selection = $(this).closest('.section-field-item').find('.table-selection').val();
            var field_selection = $(this).closest('.section-field-item').find('.field-selection').val();
            
            /*console.log('index :'+ field_index);
            console.log('table :'+ table_selection);
            console.log('field :'+ field_selection);*/
            $.ajax({
               url    : '?r=test/view-sections/fetch-keys-by-column',
               type   : 'post',
               data: {keylist: [section_id,table_selection,field_selection]},
               success: function (data)
               {
                   var list = jQuery.parseJSON(data);
                   var list = (list['output']);
                   $('.index-config-'+ field_index).empty();
                   $('.index-config-'+ field_index).append($('<option></option>').attr('value', '0').text('select...'));
                   for (row in list) {

                      $('.index-config-'+ field_index).append($('<option></option>').attr('value', list[row]['id']).text(list[row]['name']));
                   }

               },
               error: function ()
               {
                   console.log('internal server error');
               }
           });      
       });
       
   }
 
   
   trigger_dep_drop();
 
    
");
?>