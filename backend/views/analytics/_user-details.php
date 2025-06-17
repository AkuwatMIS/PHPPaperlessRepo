<?php
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\data\ActiveDataProvider;
use common\models\Users;

/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$query = Users::find()->where(['in','users.id',$user_array])
    ->andFilterWhere(['=', 'users.status', 1])
    ->andFilterWhere(['=', 'users.is_block', 0]);
    //->andFilterWhere(['not in', 'designations.code', array('LO','DEO','AA','BM','RC','RA','ITE','DA')]);;
$query->joinWith('designation');
$dataProvider = new ActiveDataProvider([
    'query' => $query,
]);
$dataProvider->pagination->pageSize = 100;
?>
<div class="users-index">

    <h3>Users Detail</h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'fullname',
            'email:email',
            [
                'attribute'=>'designation.name',
                'label'=>'Designation',
            ]
        ],
    ]); ?>
</div>
