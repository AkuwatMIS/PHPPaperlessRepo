<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Designations */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="designations-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'desig_label')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-6">
            <?= $form->field($model, 'sorting')->textInput() ?>
        </div>
    </div>


    <h3>Mobile App Permissions</h3>

    <table class="table">
        <tbody>
            <tr>
                <td>
                    <?= $form->field($model, 'network')->checkBox(['label' => 'Network', 'selected' => false]); ?>
                </td>
                <td>
                    <?= $form->field($model, 'progress_report')->checkBox(['label' => 'Progress Report', 'selected' => false]); ?>
                </td>
                <td>
                    <?= $form->field($model, 'projects')->checkBox(['label' => 'Projects', 'selected' => false]); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->field($model, 'districts')->checkBox(['label' => 'Districts', 'selected' => false]); ?>
                </td>
                <td>
                    <?= $form->field($model, 'products')->checkBox(['label' => 'Products', 'selected' => false]); ?>
                </td>
                <td>
                    <?= $form->field($model, 'analysis')->checkBox(['label' => 'Analysis', 'selected' => false]); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->field($model, 'search_loan')->checkBox(['label' => 'Search Loan', 'selected' => false]); ?>
                </td>
                <td>
                    <?= $form->field($model, 'news')->checkBox(['label' => 'News', 'selected' => false]); ?>
                </td>
                <td>
                    <?= $form->field($model, 'maps')->checkBox(['label' => 'Maps', 'selected' => false]); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->field($model, 'staff')->checkBox(['label' => 'Staff', 'selected' => false]); ?>
                </td>
                <td>
                    <?= $form->field($model, 'links')->checkBox(['label' => 'Links', 'selected' => false]);  ?>
                </td>
                <td>
                    <?= $form->field($model, 'filters')->checkBox(['label' => 'Filters', 'selected' => false]);  ?>
                </td>
            </tr>
            <tr>
                <td>
                    <?= $form->field($model, 'housing')->checkBox(['label' => 'Housing', 'selected' => false]); ?>
                </td>

            </tr>
            <tr>
                <td>
                    <?= $form->field($model, 'audit')->checkBox(['label' => 'Audit', 'selected' => false]); ?>
                </td>

            </tr>
        </tbody>
    </table>

	<?php if (!Yii::$app->request->isAjax){ ?>
	  	<div class="form-group">
	        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
	    </div>
	<?php } ?>

    <?php ActiveForm::end(); ?>
    
</div>
