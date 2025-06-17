<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Templates */
/* @var $form yii\widgets\ActiveForm */
$js = "
    $(document).ready(function () {
         $('.field-templates-subject').hide();
         $('.field-templates-email').hide();
         $('.checkbox').hide();
         $('.summernote').summernote();
        $('#templates-template_type input').on('change', function() {
            type = $('input[name=\"Templates[template_type]\"]:checked', '#templates-template_type').val();
            if(type == 'email' || type=='both'){
                $('.field-templates-subject').show();
                $('.field-templates-email').show();
            }else{
               $('.field-templates-subject').hide();
                $('.field-templates-email').hide();
            }
        });
        
        ///
        $(\"#templates-module\").on('change', function() {
            module= $(\"#templates-module\").val();
            if(module){
                $.ajax({
                    type: \"POST\",
                    url: '/templates/get-placeholders?module='+module,
                    success: function(data){
                    $('.placeholders').html(data);
                        //var obj = $.parseJSON(data);
                        /*if(obj.status_type == 'error'){
                            $('#status').append(obj.message);
                            $('#status').show();
                        }
                        else {
                            $('#status').hide();
                            $('#application-submit').removeAttr('disabled');
                        }*/
                    }
                });   
            }
        });
        ///
    });
  
		$(function() {	
		$(\"#programming-languages\").multiPicker({
				selector	: \"checkbox\",
			});
		});
		$(function() {
			$('#tags-editor-textarea').tagEditor();
		});
         document.addEventListener('dblclick', function (e) {
            //alert($('#'+e.target.id).text());
            //$('#'+e.target.id).text();
            var ctl = document.getElementById(e.target.id);
            var startPos = ctl.selectionStart;
            var endPos = ctl.selectionEnd;
            //alert(startPos + \", \" + endPos);
		    //alert($('.note-editable').html());
            //var template=$('#templates-template_text').html() + $(this).text();
            var template=$('.note-editable').html() + ' [['+$('#'+e.target.id).text()+']] ';
            // alert(template);
            $('#templates-template_text').html(template);
            $('.note-editable').html(template);
         });
		/*$(\".tag\").dblclick(function(){
            var ctl = document.getElementById('templates-template_text');
            var startPos = ctl.selectionStart;
            var endPos = ctl.selectionEnd;
            //alert(startPos + \", \" + endPos);
		    //alert($('.note-editable').html());
            //var template=$('#templates-template_text').html() + $(this).text();
            var template=$('.note-editable').html() + ' [['+$(this).text()+']] ';
            // alert(template);
            $('#templates-template_text').html(template);
            $('.note-editable').html(template);
        });*/
";
$this->registerJs($js);
?>
<style>

    .tags {
        list-style: none;
        margin: 0;
        overflow: hidden;
        padding: 0;
    }

    .tags li {
        float: left;
    }

    .tag {
        background: #eee;
        border-radius: 3px 0 0 3px;
        color: #999;
        display: inline-block;
        height: 26px;
        line-height: 26px;
        padding: 0 20px 0 23px;
        position: relative;
        margin: 0 10px 10px 0;
        text-decoration: none;
        -webkit-transition: color 0.2s;
    }

    .tag::before {
        background: #fff;
        border-radius: 10px;
        box-shadow: inset 0 1px rgba(0, 0, 0, 0.25);
        content: '';
        height: 6px;
        left: 10px;
        position: absolute;
        width: 6px;
        top: 10px;
    }

    .tag::after {
        background: #fff;
        border-bottom: 13px solid transparent;
        border-left: 10px solid #eee;
        border-top: 13px solid transparent;
        content: '';
        position: absolute;
        right: 0;
        top: 0;
    }

    .tag:hover {
        background-color: #00a8ff;
        color: white;
    }

    .tag:hover::after {
        border-left-color: #00a8ff;
    }
    .btn-md {
        font-size: 15px;
        width: 160px;
        background-color: #5A738E;
    }

    .reset {
        font-size: 15px;
        width: 160px;
    }
    .radio input {
        position: relative;
        visibility: visible;
    }
    label {
        display: inline;
    }
    /*.note-editing-area{
        height:auto
    }*/
</style>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-sm-12">
        <?= $form->field($model, 'module')->dropDownList(\common\components\Helpers\ListHelper::getLists('template_modules'),['prompt'=>'Select Module'/*'readonly'=>true*/])->label('Module') ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'template_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-2">
    </div>
    <div class="col-sm-4 checkbox">
        <?= $form->field($model, 'template_type')->radioList(['email'=>'Email','file'=>'File','both'=>'Both'], ['id'=>'templates-template_type','value'=>'file','class' => 'radio checkbox', 'required' => 'required','style' => 'font-size:20px;display:block']); ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'subject')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-6">
        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="col-sm-12 placeholders">
        <!--<label>Placeholders</label>
        <ul class="tags">
            <?php /*$i=0; foreach ($placeholders as $key=>$value){ */?>
                <li><p id="tag-<?/*= $i*/?>" href="#" class="tag"><?/*= $value*/?></p></li>
            <?php /*$i++; }*/?>
        </ul>-->
    </div>
    <div class="col-sm-12">
        <?= $form->field($model, 'template_text')->textarea(['value'=>' ','rows'=>8,'class'=>'summernote','maxlength' => true]) ?>
    </div>
   <!-- <div class="col-sm-12 checkbox-toggle">
        <?/*= $form->field($model, 'is_active')->checkbox([[1,0],'maxlength' => true]) */?>
    </div>-->
    <div class="col-sm-12">
        <?=
        $form->field($model, 'send_to',$htmlOptions=array(), false)->widget(\kartik\select2\Select2::classname(), [
            'data' => \yii\helpers\ArrayHelper::map(\common\components\Helpers\StructureHelper::getDesignations(), 'id', 'name'),
            'options' => ['placeholder' => 'Select Roles ...'],
            'pluginOptions' => [
                'allowClear' => true,
                'multiple'=>true
            ],
        ])->label('Visibility');
        ?>
    </div>
    <div style="margin-top: 3%" class="col-sm-6 checkbox-toggle">
        <input type="checkbox" name="Templates[is_active]" id="templates-is_active" checked="">
        <label for="templates-is_active">Is Active</label>
    </div>

</div>
<div class="row">
    <div class="col-sm-12">
        <?= Html::submitButton( 'Update', ['class' => 'pull-right btn btn-primary']) ?>
    </div>
</div>

<?php ActiveForm::end(); ?>
