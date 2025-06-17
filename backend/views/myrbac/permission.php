<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\RolesActions */

$this->params['breadcrumbs'][] = ['label' => 'Roles Actions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<div class="roles-actions-view">
    <?= Html::beginForm(); ?>

    <div class="box-body no-padding">
        <table class="table table-bordered table-striped">
            <thead>
            <th></th>
            <?php

            foreach ($action_heading as $heading) { ?>
            <th><?= $heading ?></th>
            <?php } ?>
            </thead>
            <tbody>
            <?php
            foreach ($action_array as $key_name => $key_value) {
                ?>
                <?php ?>
                <tr>
                    <td><?= $key_name ?></td>
                    <?php

                    foreach ($action_array[$key_name] as $key => $value) {
                        if(isset($value['display'])){
                            if($value['display'] == 1){
                            ?>
                            <td> <?= Html::dropDownList('Permission[' . $key_name . '][' . $key . ']', $value['value'], $role_permission) ?></td>
                        <?php } else {?>
<td></td>
                    <?php } } } ?>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?= Html::submitButton( 'Permission', ['class' => 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
    <?php Html::endForm(); ?>

    <div class="container">
        <div class="row">
            <div class="col-sm-12 col-md-12">
                <div class="panel-group" id="accordion">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion" href="#collapseOne"><span class="fa fa-user">
                            </span> Users</a>
                            </h4>
                        </div>
                        <div id="collapseOne" class="panel-collapse collapse in">
                            <div class="panel-body">
                                <table class="table">
                                    <thead>
                                        <th>User Name</th>
                                        <th>Email</th>
                                    </thead>
                                    <tr>
                                        <td>
                                            <?= Html::a('Select Users', ['userlist', 'name' => $name],['class' => 'btn btn-success'])?>
                                        </td>
                                    </tr>
                                    <?php foreach ($user_list as $u) { ?>
                                    <tr>
                                        <td><?=isset($u->users[0]->username)?$u->users[0]->username:''?></td>
                                        <td><?=isset($u->users[0]->email)?$u->users[0]->email:''?></td>
                                        <td><?= Html::a('Remove', ['deleteuser', 'id' => isset($u->users[0]->id)?$u->users[0]->id:'' , 'name' => isset($u->item_name)?$u->item_name:''])?></td>
                                    </tr>
                                   <?php } ?>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
</div>
</div>
    <?php

    \yii\bootstrap\Modal::begin([
        'header' => '<h4>Users</h4>',
        'id'     => 'model',
        'size'   => 'model-lg',
    ]);

    echo "<div id='modelContent'></div>";

    \yii\bootstrap\Modal::end();

    ?>
<?php
$script = "$(function(){
    $('#modelButton').click(function(){
        $('.modal').modal('show')
            .find('#modelContent')
            .load($(this).attr('value'));
    });
});";
    $this->registerJs($script);
    ?>