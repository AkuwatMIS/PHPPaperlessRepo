<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use johnitvn\ajaxcrud\CrudAsset;
use yii\widgets\DetailView;
use common\models\AuthItem;

/* @var $this yii\web\View */
/* @var $model common\models\BranchRequests */

$this->title = $model->fullname;
$this->params['breadcrumbs'][] = ['label' => 'User Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$js = '';
$js = '
    $(function(){
        $(\'#modelButton\').click(function(){
            $(\'.modal\').modal(\'show\')
                .find(\'#modelContent\')
                .load($(this).attr(\'value\'));
        });
    });
    $(function(){
        $(\'#modalButton\').click(function(){
            $(\'.modal\').modal(\'show\')
                .find(\'#modelContent\')
                .load($(this).attr(\'value\'));
        });
    });
    $(document).ready(function () {
        var designation = "' . key($auth['roles']) . '";
        $(\'#hierarchy_fields\').hide();
        $(\'#user_role\').hide();
        
        $("#usertransfers-type").change(function(){
            var a=$(\'#usertransfers-type\').val();
            if(a==\'transfer\'){
                if(designation=="AA" || designation=="AAA"){
                     $(\'.field-usertransfers-team_id\').hide();
                     $(\'.field-usertransfers-field_id\').hide();
                     $(\'.field-usertransfers-branch_id\').hide();
                }
                $(\'#user_role\').hide();
                $(\'#hierarchy_fields\').show();

            } else if(a==\'promotion\'){
                $(\'#hierarchy_fields\').show();
                $(\'#user_role\').show();
                $("#usertransfers-role").change(function(){
                    var a=$(\'#usertransfers-role\').val();
                    if(a==\'LO\'){
                        $(\'.field-usertransfers-team_id\').show();
                        $(\'.field-usertransfers-field_id\').show();
                        $(\'.field-usertransfers-branch_id\').show();
                    } else if(a==\'BM\'){
                       $(\'.field-usertransfers-team_id\').hide();
                        $(\'.field-usertransfers-field_id\').hide();
                        $(\'.field-usertransfers-branch_id\').show();
                    }else if(a==\'RC\' || a==\'RM\'){
                       $(\'.field-usertransfers-team_id\').hide();
                        $(\'.field-usertransfers-field_id\').hide();
                        $(\'.field-usertransfers-branch_id\').hide();
                        $(\'.field-usertransfers-area_id\').hide();
                    } else {
                       $(\'.field-usertransfers-team_id\').hide();
                        $(\'.field-usertransfers-field_id\').hide();
                        $(\'.field-usertransfers-branch_id\').hide();
                    }

                });

            }
            else {
                $(\'#user_role\').hide();
                $(\'#hierarchy_fields\').hide();

            }
        });
        
    });';
$this->registerJs($js);
?>
<div class="container-fluid">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="row">

        <div class="col-md-7">

            <div class="box-typical">
                <header class="box-typical-header-sm">User Detail</header>
                <div class="row">
                    <div class="col-md-4">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-notebook-bird"></i>
                                Basic Information
                            </header>
                            <div class="box-typical-inner">
                                <p>
                                    <b>User Name</b> : <?= isset($model->username)?($model->username):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Full Name</b> : <?= isset($model->fullname)?($model->fullname):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Father Name</b> : <?= isset($model->father_name)?($model->father_name):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Cnic</b> : <?= isset($model->cnic)?($model->cnic):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Employee Code</b> : <?= isset($model->emp_code)?($model->emp_code):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Joining Date</b> : <?= isset($data->joining_date) ? date('Y-M-d', $data->joining_date) : 'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Email  Address</b> : <?= isset($model->email)?($model->email):'Not Set'; ?>
                                </p>
                                <p>
                                    <b>Mobile</b> : <?= isset($model->mobile)?($model->mobile):'Not Set'; ?>
                                </p>
                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                    <div class="col-md-4">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-case"></i>
                                Current Hierarchy
                            </header>
                            <div class="box-typical-inner">
                                <?php if (key($auth['roles']) == 'RC' || key($auth['roles']) == 'RM' || key($auth['roles']) == 'RA' || key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'AAA' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                                <p>
                                    <b>Designation</b>
                                    : <?= (key($auth['roles'])); ?>
                                </p>
                                <p>
                                    <b>Region</b> : <?= isset($array['region']->userRegion->name) ? $array['region']->userRegion->name : '-' ?>
                                </p>
                                <?php }?>
                                <?php if (key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'AAA' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                                <p>
                                    <b>Area</b> : <?= isset($array['area']->userArea->name) ? $array['area']->userArea->name : '-' ?>
                                </p>
                                <?php }?>
                                <?php if (key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                                <p>
                                    <b>Branch</b> : <?= isset($array['branch']->userBranch->name) ? $array['branch']->userBranch->name : '-' ?>
                                </p>
                                <?php }?>
                                <?php if (key($auth['roles']) == 'LO') {?>
                                <p>
                                    <b>Team</b> :  <?= isset($array['team']->userTeam->name) ? $array['team']->userTeam->name : '--' ?>
                                </p>
                                <p>
                                    <b>Field</b> : <?= isset($array['field']->userField->name) ? $array['field']->userField->name : '--' ?>
                                </p>
                                <?php }?>
                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                    <div class="col-md-4">
                        <article class="profile-info-item">
                            <header class="profile-info-item-header">
                                <i class="font-icon font-icon-award"></i>
                                Demographic Hierarchy
                            </header>
                            <div class="box-typical-inner">

                            </div>
                        </article><!--.profile-info-item-->
                    </div>
                </div>
            </div>
            </section><!--.box-typical-->

        </div>
        <div class="col-md-5">
            <section class="box-typical">
                <header class="box-typical-header-sm">Transfer Details</header>
                <article class="profile-info-item">
                    <header class="profile-info-item-header">
                        <i class="font-icon font-icon-award"></i>
                        User Transfer
                    </header>
                    <?php

                    //die();
                    ?>
                    <div class="box-typical-inner">
                        <?php $form = \yii\widgets\ActiveForm::begin(); ?>

                        <?= $form->field($change_model, 'type')->dropDownList($types,['prompt' => 'Select Type']) ?>

                        <div id="user_role">
                            <?= $form->field($change_model, 'role')->dropDownList($designations,['prompt' => 'Select Designation'])?>
                        </div>
                        <div id="hierarchy_fields">
                            <?php
                            if (key($auth['roles']) == 'RC' || key($auth['roles']) == 'RM' || key($auth['roles']) == 'RA' || key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' ||  key($auth['roles']) == 'AAA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {
                                //$change_model->region_id = $array['region']->obj_id;
                                echo $form->field($change_model, 'region_id')->dropDownList($regions, ['prompt' => 'Select Region'])->label('Region');
                            } ?>
                            <?php
                            if (key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' ||  key($auth['roles']) == 'AAA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {
                                $value = !empty($change_model->area_id) ? $change_model->area->name : null;
                                echo $form->field($change_model, 'area_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                                    'pluginOptions' => [
                                        'depends' => ['usertransfers-region_id'],
                                        'initialize' => true,
                                        'initDepends' => ['usertransfers-region_id'],
                                        'placeholder' => 'Select Area',
                                        'url' => \yii\helpers\Url::to(['/user-management/structure/fetch-area-by-region'])
                                    ],
                                    'data' => $value ? [$change_model->area_id => $value] : []
                                ])->label('Area');
                            }
                            ?>
                            <?php
                            if (key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'AAA'/* || in_array('BM',$designations) || in_array('LO',$designations)*/) {

                                $value = !empty($change_model->branch_id) ? $change_model->id : null;
                                echo $form->field($change_model, 'branch_id')->widget(\kartik\depdrop\DepDrop::classname(), [
                                    'pluginOptions' => [
                                        'depends' => ['usertransfers-area_id'],
                                        'initialize' => true,
                                        'initDepends' => ['usertransfers-area_id'],
                                        'placeholder' => 'Select Branch',
                                        'url' => \yii\helpers\Url::to(['/user-management/structure/fetch-branch-by-area'])
                                    ],
                                    'data' => $value ? [$change_model->branch_id => $value] : []
                                ])->label('Branch');
                            }
                            ?>
                            <?php
                            if (key($auth['roles']) == 'LO' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'AAA' /*|| in_array('LO',$designations)*/) {
                                $value = !empty($change_model->team_id) ? $change_model->team_id : null;
                                echo $form->field($change_model, 'team_id')->widget(kartik\depdrop\DepDrop::classname(), [
                                    'pluginOptions' => [
                                        'depends' => ['usertransfers-branch_id'],
                                        'initialize' => true,
                                        'initDepends' => ['usertransfers-branch_id'],
                                        'placeholder' => 'Select Team',
                                        'url' => \yii\helpers\Url::to(['/user-management/structure/fetch-team-by-branch'])
                                    ],
                                    'data' => $value ? [$change_model->team_id => $value] : []
                                ])->label('Team');
                            }
                            ?>
                            <?php
                            if (key($auth['roles']) == 'LO'|| key($auth['roles']) == 'AA' || key($auth['roles']) == 'AAA'  /*|| in_array('LO',$designations)*/) {
                                $value = !empty($change_model->field_id) ? $change_model->field_id : null;
                                echo $form->field($change_model, 'field_id')->widget(kartik\depdrop\DepDrop::classname(), [
                                    'pluginOptions' => [
                                        'depends' => ['usertransfers-team_id'],
                                        'initialize' => true,
                                        'initDepends' => ['usertransfers-team_id'],
                                        'placeholder' => 'Select Field',
                                        'url' => \yii\helpers\Url::to(['/user-management/structure/fetch-field-by-team'])
                                    ],
                                    'data' => $value ? [$change_model->field_id => $value] : []
                                ])->label('Field');
                            }

                            ?>
                        </div>
                        <?= $form->field($change_model, "created_by")->hiddenInput(['value' => yii::$app->user->getId()])->label(false) ?>
                        <?= $form->field($change_model, "user_id")->hiddenInput(['value' => $model->id])->label(false) ?>
                        <?= $form->field($change_model, "assigned_to")->hiddenInput(['value' => isset($auth['rm_id']->user_id)?$auth['rm_id']->user_id:'0'])->label(false) ?>
                        <?= $form->field($change_model, "recommended_by")->hiddenInput(['value' => '0'])->label(false) ?>
                        <?= $form->field($change_model, "status")->hiddenInput(['value' => 'pending'])->label(false) ?>


                        <?php   if (key($auth['roles']) == 'RC' || key($auth['roles']) == 'RM' || key($auth['roles']) == 'RA' || key($auth['roles']) == 'AM' || key($auth['roles']) == 'AA' || key($auth['roles']) == 'AAA' || key($auth['roles']) == 'DEO' || key($auth['roles']) == 'BM' || key($auth['roles']) == 'LO') {?>
                            <div class="form-group" style="margin-top: 20px">
                                <?= \yii\helpers\Html::submitButton('Transfer', ['class' => 'btn btn-primary']) ?>
                            </div>
                            <br>
                        <?php }?>
                    <?php \yii\widgets\ActiveForm::end(); ?>
                    </div>
                </article><!--.profile-info-item-->
            </section><!--.box-typical-->


    </div>

    </div>
</div>
<?php

Modal::begin([
    'header' => '<h4>Branch Request</h4>',
    'id' => 'model',
    'size' => 'model-lg',
]);

echo "<div id='modelContent'></div>";

Modal::end();

?>
