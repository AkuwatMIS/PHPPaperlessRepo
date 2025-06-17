<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\UsersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>-->
<div class="users-index">
    <?php  echo $this->render('_search_users', ['model' => $searchModel, 'name' => $name]); ?>

    <?= Html::a('Select',null, ['class' => 'btn btn-primary', 'id'=> 'selectuser']) ?>
   <!-- --><?php /*Pjax::begin(); */?>
    <?= GridView::widget([
            'id' => 'grid',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        //'pjax' => false,
        'columns' => [
           // ['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\CheckboxColumn',
                'checkboxOptions' => function($model, $key, $index, $widget) {
                    return ['value' => $model['id'] ];
                },
                ],
            //'id',
            'username',
            //'password',
           // 'auth_key',
           // 'password_hash',
            //'password_reset_token',
            //'last_login_at',
            //'last_login_token',
            //'access_token',
            //'fullname',
            //'father_name',
            'email:email',
            //'image',
            //'mobile',
            //'joining_date',
            //'role',
            //'created',
            //'modified',
            //'designation_id',
            //'emp_code',
            //'area_id',
            //'region_id',
            //'branch_id',
            //'isblock',
            //'reason',
            //'block_date',
            //'team_name',
            //'status',
            //'created_on',
            //'updated_on',
            //'updated_at',
            //'created_at',

        ],
    ]); ?>
   <!-- --><?php /*Pjax::end(); */?>
</div>

<?php
$script = "$(function(){
    $('#selectuser').click(function(){
       var keys =($('#grid').yiiGridView('getSelectedRows'));
       
       $.post({
        url: 'userlist?name=$name',
        dataType: 'json',
        data: {keylist: keys},
        success: function(data) {
            alert('users assign to role')
        },
    });
       
    });
});";
$this->registerJs($script);
?>
