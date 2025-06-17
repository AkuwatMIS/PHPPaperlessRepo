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
        //'limit' => 10, // the maximum times, an element can be cloned (default 999)
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
                <button type="button" class="add-section-field btn btn-success btn-xs"><span
                            class="glyphicon glyphicon-plus"></span></button>
            </th>
        </tr>
        </thead>
        <tbody class="container-items">

        <?php foreach ($modelsFields as $indexField => $modelField):?>
            <tr class="section-field-item">
                <td><?= $form->field($modelField, "[{$indexField}]sort_order")->textInput(['value' => 0, 'class' => 'form-control sorting'])->label(false) ?></td>
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
                        // 'type'=>DepDrop::TYPE_SELECT2,
                        'options' => ['class' => 'form-control update-trigger field-selection', 'data-field_index' => '0'],
                        'pluginOptions' => [
                            'depends' => ['viewsectionfields-' . $indexField . '-table_name'],
                            //'initialize' => true,
                            'initDepends' => ['viewsectionfields-' . $indexField . '-table_name'],
                            'placeholder' => 'Select...',
                            'url' => Url::to(['/view-sections/fetch-columns-by-table'])
                        ],
                        'data' => $value ? [$modelField->field => $value] : []
                    ]);
                    ?>
                </td>
                <td>
                    <?= $this->render('_form-field_configs', [
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
<?php
$this->registerJs("
    var sort = 0;
    $('.dynamicform_wrapper').on('afterInsert', function(e, item) {
        sort++;
        $(item).find('.sorting').val(sort);       
    });
");
?>