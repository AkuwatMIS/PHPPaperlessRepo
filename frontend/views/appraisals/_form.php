<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\web\JsExpression;
/* @var $this yii\web\View */
/* @var $model common\models\Appraisals */
/* @var $form yii\widgets\ActiveForm */
$this->registerJsFile('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/js/bootstrap-datepicker.min.js',['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerCssFile('https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.4.1/css/bootstrap-datepicker3.css');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/appraisals.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$this->registerJsFile('https://rawgit.com/RobinHerbots/jquery.inputmask/3.x/dist/jquery.inputmask.bundle.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
$js = "
window.setTimeout(function() {
    $(\".alert\").fadeTo(3000, 0).slideUp(3000, function(){
        $(this).remove(); 
    });
}, 500);
$('#applications-appraisal_id').on('change', function() {
  if(this.value === '1'){
    var application_id = $(\"#applications-id\").val();
    $.ajax({
        type: \"POST\",
        url: '/appraisals/project?id='+application_id,
        success: function(data){
            var obj = $.parseJSON(data);
              if(obj.project_id === 4 || obj.project_id === 67 ){
              $('#appraisalssocial-poverty_index').attr('required', 'required');   
              }else{
              $(\".field-appraisalssocial-poverty_index\").hide();
              }
        }
    });
  }
});
    

 $(\"#btn-create\").click(function(){
  var pi = $(\"#appraisalssocial-poverty_index\").val();
  var application_id = $(\"#applications-id\").val();
    $.ajax({
        type: \"POST\",
        url: '/appraisals/project?id='+application_id,
        success: function(data){
            var obj = $.parseJSON(data);
              if(obj.project_id === 4 || obj.project_id === 67 ){
                 if(pi === '0'){
                  alert('Poverty Score should be greater than 0');
                  }
              }else{
              $(\"#appraisalssocial-poverty_index\").removeAttr('required');
              $(\".field-appraisalssocial-poverty_index\").remove();
              }
        }
    });
  });
";
$this->registerJs($js);
?>


<!--<div class="appraisals-form">

    <?php /*$form = ActiveForm::begin(); */?>

    <?/*= $form->field($model, 'name')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'appraisal_table')->textInput(['maxlength' => true]) */?>

    <?/*= $form->field($model, 'status')->textInput() */?>

  
	<?php /*if (!Yii::$app->request->isAjax){ */?>
	  	<div class="form-group">
	        <?/*= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) */?>
	    </div>
	<?php /*} */?>

    <?php /*ActiveForm::end(); */?>
    
</div>-->

<?php if (Yii::$app->session->hasFlash('success')) { ?>
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
        <h4><i class="icon fa fa-check"></i> Saved!</h4>
        <?= Yii::$app->session->getFlash('success')[0] ?>
    </div>
<?php }
if (Yii::$app->session->hasFlash('error')) { ?>
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">&times;</button>
        <h4><i class="icon fa fa-remove"></i> Error!</h4>
        <?= Yii::$app->session->getFlash('error')[0] ?>
    </div>
<?php } ?>
<div class="applications-form">

    <?php $form = ActiveForm::begin(['id' => 'form-livestock-appraisal'
    ]); ?>
    <?= $form->errorSummary($model) ?>
    <div class="row">
        <div class="col-sm-12">
            <?php
            $url = \yii\helpers\Url::to(['/applications/search-application']);
            if (!empty($model->id)) {
                $application = \common\models\Applications::findOne($model->id);
                $cityDesc = '<strong>Application No</strong>: ' . $application->application_no . ' <strong>Member Name</strong>: ' . $application->member->full_name;
            } else {
                $cityDesc = '';
            }
            ?>

            <?= $form->field($model, "id")->widget(\kartik\select2\Select2::classname(), [
                'initValueText' => $cityDesc, // set the initial display text
                'options' => ['placeholder' => 'Search for a Application No  / Member CNIC...', 'class' => 'file'],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'language' => [
                        'errorLoading' => new \yii\web\JsExpression("function () { return 'Waiting for results...'; }"),
                    ],
                    'ajax' => [
                        'url' => $url,
                        'dataType' => 'json',
                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                    ],
                    'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                    'templateResult' => new JsExpression('function(city) { return city.text; }'),
                    'templateSelection' => new JsExpression('function (city) { return city.text; }'),
                ],
            ])->label('Select Application');

            ?>
                <?php
                $value = !empty($model->appraisal_id) ? $model->appraisal_id : null;
                echo $form->field($model, 'appraisal_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                    'pluginOptions' => [
                        'depends' => ['applications-id'],
                        'initialize' => true,
                        'initDepends' => ['applications-id'],
                        'placeholder' => 'Select Appraisal',
                        'url' => \yii\helpers\Url::to(['/structure/fetch-appraisals-by-project'])
                    ],
                    'data' => $value ? [$model->appraisal_id => $value] : []
                ])->label('Appraisal');
                ?>
        </div>
    </div>
    <div id="total" class="label label-success pull-right" style="display: none;">
        <div class="row">
            <div class="col-sm-4" style="margin-left: 20px">
                Total Income:<b id="total-income">0</b>
            </div>
            <div class="col-sm-4" style="margin-left: 50px">
                Total Expenses:<b id="total-expenses">0</b>
            </div>
        </div>
    </div>
    <br><br>
    <header class="section-header" id="project-header">
    </header>

    <div class="row" id="project-details">
    </div>

    <div class="col-md-6" id="livestock-project-details-existing-assets">
        <table class="table table-bordered border-primary">
            <thead>
            <tr>
                <th></th>
                <th>
                    <p style="text-align: right">پہلے سے موجود جانور</p>
                </th>
            </tr>
            <tr>
                <th scope="col">قسم جانور</th>
                <th scope="col">تعداد</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td><input type="text" name='fishing'  value='fishing' class="form-control" readonly /></td>
                <td><input type="number" name='fishing_count'  placeholder='Enter Fishing Count' class="form-control"/></td>
            </tr>
            <tr>
                <td><input type="text" name='poultry'  value=poultry' class="form-control"  readonly/></td>
                <td><input type="number" name='poultry_count'  placeholder='Enter poultry Count' class="form-control"/></td>
            </tr>
            <tr>
                <td><input type="text" name='buffalo'  value='buffalo' class="form-control"  readonly/></td>
                <td><input type="number" name='buffalo_count'  placeholder='Enter buffalo Count' class="form-control"/></td>
            </tr>
            <tr>
                <td><input type="text" name='cow'  value='cow' class="form-control"  readonly/></td>
                <td><input type="number" name='cow_count'  placeholder='Enter cow Count' class="form-control"/></td>
            </tr>
            <tr>
                <td><input type="text" name='goat'  value='goat' class="form-control"  readonly/></td>
                <td><input type="number" name='goat_count'  placeholder='Enter goat Count' class="form-control"/></td>
            </tr>
            <tr>
                <td><input type="text" name='sheep'  value='sheep' class="form-control"  readonly/></td>
                <td><input type="number" name='sheep_count'  placeholder='Enter sheep Count' class="form-control"/></td>
            </tr>
            <tr>
                <td><input type="text" name='others'  value='others' class="form-control"  readonly/></td>
                <td><input type="number" name='others_count'  placeholder='Enter others Count' class="form-control"/></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="col-md-6" id="livestock-project-details-new-assets">
        <table class="table table-bordered border-primary">
            <thead>
            <tr>
                <th></th>
                <th></th>
                <th>
                    <p style="text-align: right">قرض کی رقم سے خریدے جانے والے جانور</p>
                </th>
            </tr>
            <tr>
                <th scope="col">قسم جانور</th>
                <th scope="col">تعداد</th>
                <th scope="col">قیمت خرید</th>
            </tr>
            </thead>
            <tbody>
                <tr>
                    <td><input type="text" name='fishing_buy'  value='fishing' id="fishing_buy" class="form-control" readonly/></td>
                    <td><input type="number" name='fishing_buy_count'  placeholder='Enter Fishing Count' class="form-control"/></td>
                    <td><input type="number" name='fishing_buy_amount'  placeholder='Enter Animal Count' class="form-control"/></td>
                </tr>
                <tr>
                    <td><input type="text" name='poultry_buy'  value=poultry' id="poultry_buy" class="form-control" readonly/></td>
                    <td><input type="number" name='poultry_buy_count'  placeholder='Enter poultry Count' class="form-control"/></td>
                    <td><input type="number" name='poultry_buy_amount'  placeholder='Enter Animal Count' class="form-control"/></td>
                </tr>
                <tr>
                    <td><input type="text" name='buffalo_buy'  value='buffalo' id="buffalo_buy" class="form-control" readonly/></td>
                    <td><input type="number" name='buffalo_buy_count'  placeholder='Enter buffalo Count' class="form-control"/></td>
                    <td><input type="number" name='buffalo_buy_amount'  placeholder='Enter Animal Count' class="form-control"/></td>
                </tr>
                <tr>
                    <td><input type="text" name='cow_buy'  value='cow' id="cow_buy" class="form-control" readonly/></td>
                    <td><input type="number" name='cow_buy_count'  placeholder='Enter cow Count' class="form-control"/></td>
                    <td><input type="number" name='cow_buy_amount'  placeholder='Enter Animal Count' class="form-control"/></td>
                </tr>
                <tr>
                    <td><input type="text" name='goat_buy'  value='goat' id="goat_buy" class="form-control" readonly/></td>
                    <td><input type="number" name='goat_buy_count'  placeholder='Enter goat Count' class="form-control"/></td>
                    <td><input type="number" name='goat_buy_amount'  placeholder='Enter Animal Count' class="form-control"/></td>
                </tr>
                <tr>
                    <td><input type="text" name='sheep_buy'  value='sheep' id="sheep_buy" class="form-control" readonly/></td>
                    <td><input type="number" name='sheep_buy_count'  placeholder='Enter sheep Count' class="form-control"/></td>
                    <td><input type="number" name='sheep_buy_amount'  placeholder='Enter Animal Count' class="form-control"/></td>
                </tr>
                <tr>
                    <td><input type="text" name='others_buy'  value='others' id="others_buy" class="form-control" readonly/></td>
                    <td><input type="number" name='others_buy_count'  placeholder='Enter others Count' class="form-control"/></td>
                    <td><input type="number" name='others_buy_amount'  placeholder='Enter Animal Count' class="form-control"/></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php if (!Yii::$app->request->isAjax) { ?>
        <div class="form-group">
            <?= Html::submitButton(/*$model->isNewRecord ? */'Create'/* : 'Update',*/, ['class' => /*$model->isNewRecord ?*/ 'btn btn-success'/* : 'btn btn-primary'*/,'id'=>'btn-create']) ?>
        </div>
    <?php } ?>

    <?php ActiveForm::end(); ?>
</div>

<?php
$script = <<< JS

$( document ).ready(function() {
    $("#livestock-project-details-existing-assets").hide();
    $("#livestock-project-details-new-assets").hide();
});

$("#applications-appraisal_id").change(function(){
  var project_id_check = $("#applications-appraisal_id").val();
 if(project_id_check === '6'){
     $("#livestock-project-details-existing-assets").show();
    $("#livestock-project-details-new-assets").show();
  }
  
});
$("#form-livestock-appraisal").onsubmit(function() {
  var a = $("#fishing_buy").val();  
  var b = $("#poultry_buy").val();
  var c = $("#buffalo_buy").val();  
  var d = $("#cow_buy").val();  
  var e = $("#goat_buy").val();  
  var f = $("#sheep_buy").val();  
  var g = $("#others_buy").val();
  
  if(a==='' && b===''&& c===''&& d===''&& e===''&& f===''&& g===''){
      alert('The input of animals purchased with loan money is essential!');
     e.preventDefault();   
  }
  
  
});

JS;
$this->registerJs($script);
?>