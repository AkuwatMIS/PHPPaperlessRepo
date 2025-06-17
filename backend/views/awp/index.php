<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\grid\GridView;
use johnitvn\ajaxcrud\CrudAsset;
use johnitvn\ajaxcrud\BulkButtonWidget;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BanksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'AWP';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="banks-index">
    <div id="ajaxCrudDatatable">
        <h4>Unlock Awp of current month</h4>
        <?php $form = \yii\widgets\ActiveForm::begin(['action' => 'unlock-awp']); ?>

        <div class="form-group">
            <?= Html::submitButton('Unlock AWP', ['class' => 'btn btn-success']) ?>
        </div>

        <?php \yii\widgets\ActiveForm::end(); ?>
    </div>
    <hr>
    <!--<div id="ajaxCrudDatatable">
        <h4>Lock Awp of current month</h4>
        <?php /*$form = \yii\widgets\ActiveForm::begin(['action' => 'lock-awp']); */?>

        <div class="form-group">
            <?/*= Html::submitButton('Lock AWP', ['class' => 'btn btn-success']) */?>
        </div>

        <?php /*\yii\widgets\ActiveForm::end(); */?>
    </div>-->
    <hr>
    <div id="ajaxCrudDatatable">
        <h4>Create Awp</h4>
        <?php $form = \yii\widgets\ActiveForm::begin(['action' => 'create-awp-branch']);
        $data = \yii\helpers\ArrayHelper::map(\common\models\Branches::find()->where(['status' => 1])->all(), 'id', 'name');
        ?>

        <div class="row">
            <div class="col-sm-6">
                <?php
                echo \kartik\select2\Select2::widget([
                    'name' => 'branch_id',
                    'data' => $data,
                    'options' => [
                        'placeholder' => 'Select Branches ...',
                        'multiple' => true
                    ],
                ]);
                ?>
               <!-- <div class="form-group">
                    <?/*= Html::dropDownList('branch_id', null,
                        \yii\helpers\ArrayHelper::map(\common\models\Branches::find()->where(['status' => 1])->all(), 'id', 'name'), ['placeholder' => 'Select Branch to create Awp','required'=>true, 'class' => 'form-control']) */?>
                </div>-->
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                   <?php echo \kartik\date\DatePicker::widget([
                    'name' => 'month',
                    'value' => date('Y-m'),
                    'options' => ['placeholder' => 'Select AWP Month ...','required'=>true],
                    /*'pluginOptions' => [
                    'format' => 'yyyy-mm',
                    'todayHighlight' => true,
                    ]*/
                       'pluginOptions' => [
                           'autoclose' => true,
                           'startView'=>'year',
                           'minViewMode'=>'months',
                           'format' => 'yyyy-mm'
                       ]
                    ]);       ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <?= Html::submitButton('Create AWP', ['class' => 'btn btn-success']) ?>
        </div>
    <?php \yii\widgets\ActiveForm::end(); ?>
</div>
</div>