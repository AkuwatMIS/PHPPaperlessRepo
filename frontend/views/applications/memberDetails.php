<?php
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="borrowers-index">
    <div id="ajaxCrudDatatable">
        <?php
            echo DetailView::widget([
                'model'=>$model,
                'attributes'=>[
                    ['attribute' => 'full_name', 'label' => 'Name'],
                    ['attribute' => 'parentage', 'label' => 'Parantage'],
                    ['attribute' => 'cnic', 'label' => 'CNIC'],
                    ['attribute' => 'dob', 'label' => 'Date of Birth','value'=>function($data){return \common\components\Helpers\StringHelper::dateFormatter($data->dob);}]
                ]
            ]);
        ?>
    </div>
</div>
