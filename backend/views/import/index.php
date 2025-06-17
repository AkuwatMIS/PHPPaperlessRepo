<?php
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\ActiveForm;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\ListsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Lists';
$this->params['breadcrumbs'][] = $this->title;

CrudAsset::register($this);

?>
<div class="import-index">
    <div class="panel panel-primary">
        <div class="panel-heading">Import Mdp</div>
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['action' => ['import-mdp'], 'options' => ['enctype' => 'multipart/form-data']]); ?>

            <div class="col-md-6">
                <input type="file" name="file" class="form-control">
            </div>
            <div class="col-md-6">
                <?php if (!Yii::$app->request->isAjax){ ?>
                    <div class="form-group">
                        <?= \yii\helpers\Html::submitButton($model->isNewRecord ? 'Import' : 'Import', ['name'=>'import', 'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                <?php } ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>