<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Branches */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/addteams.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
?>
<style>
    .smm{

    }
</style>
<div class="branches-form">
    <div class="container-fluid">
        <div class="box-typical box-typical-padding">

            <table id="table-edit" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th><h3><b><?php echo $model->name ?></b></h3></th>
                    <?php $form = ActiveForm::begin(['action' => '#', 'options' => [
                        'class' => 'AddTeams'
                    ]
                    ]); ?>
                    <?= $form->field($teams, 'branch_id')->hiddenInput(['value' => $model->id])->label(false) ?>

                    <th>
                        <?= Html::submitButton('Create Team', ['id' => 'teams-save-button', 'class' => 'btn btn-success btn-sm pull-right']) ?>
                    </th>
                    <?php $form->end(); ?>
                </tr>

                </thead>


                <tbody>
                <?php foreach ($model->teams as $br_team){ ?>
                <tr>
                    <td>

                        <h3><b><?php echo $br_team->name ?></b></h3>
                        <?php $form = ActiveForm::begin(['action' => '#', 'options' => [
                            'class' => 'AddFields'
                        ]
                        ]); ?>
                        <?= $form->field($fields, 'team_id')->hiddenInput(['value' => $br_team->id])->label(false) ?>
                        <?= Html::submitButton('Create Field', ['id' => 'fields-save-button','style'=>'background-color:white;border:none;color:blue']) ?>

                        <?php $form->end(); ?>
                        <?php if (count($br_team->fields) == 0) {
                            ?>
                            <br>
                            <?php $form = ActiveForm::begin(['action' => '#', 'options' => [
                                'class' => 'DeleteTeam'
                            ]
                            ]); ?>
                            <?= $form->field($teams, 'id')->hiddenInput(['value' => $br_team->id])->label(false) ?>
                            <?= Html::submitButton('Delete Team', ['id' => 'fields-save-button','style'=>'background-color:white;border:none;color:blue']) ?>

                            <?php $form->end(); ?>
                        <?php }
                        ?>
                    </td>
                    <td>
                        <?php ?>

                        <?php foreach ($br_team->fields as $field) { ?>
                            <?php $form = ActiveForm::begin(['action' => '#', 'options' => [
                                'class' => 'DeleteField'
                            ]
                            ]); ?>
                            <?= $form->field($fields, 'id')->hiddenInput(['value' => $field->id])->label(false) ?>
                            <?= Html::submitButton('Delete Field', ['id' => 'fields-delete-button','class'=>'btn btn-danger btn-sm pull-right']) ?>
                            <?php $form->end(); ?>
                            <h4><b>Field Name:</b> <?php echo $field->name ?></h4>

                            <h4><b>Loan Officer:</b>
                            <?php if ($field->assigned_to != 0) { ?>

                                &nbsp;&nbsp;<?php echo common\components\Helpers\StructureHelper::getUserNameFromId($field->assigned_to)->username ?>
                            <?php } ?>
                            </h4>

                            <?= Html::a('Add Loan Officer', ['userlist', 'field_id' => $field->id], ['style'=>'color: blue;']) ?>
                            <hr>
                            <?php
                        } ?>
        </div>
        </td>

        </tr>

        </thead>
        <?php } ?>
        </table>
    </div>
</div>
</div>
