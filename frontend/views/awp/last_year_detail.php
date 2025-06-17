<?php
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<!--<div class="table-responsive" id="main-<?php /*echo $branch_id*/?>">-->
    <table class="table table-bordered-<?php echo $branch_id?>"><!--<h4>Recovery Details(July 2018 - June 2019)</h4>-->


        <!--<thead>
        <tr>
            <th>Recovery Details(July 2018 - June 2019)</th>
            <th>No of Recoveries</th>
            <th>1st to 10</th>
            <th> 10th	11th to 20th</th>
            <th> 20th	21st to Month End</th>
        </tr>
        </thead>-->
        <tbody>
            <tr>
                <td style="width: 265px"><p style="font-size: 25px">July 2018 - June 2019</p></td>
                <td style="size:15px;width: 235px"><p style="font-size: 25px"> <?= number_format($model->recovery_count) ?></p></td>
                <td style="width: 150px"><p style="font-size: 25px"><?= number_format(($model->recovery_one_to_ten/$model->recovery_count)*100).'%'; ?></p></td>
                <td style="width: 165px"><p style="font-size: 25px"><?= number_format(($model->recovery_eleven_to_twenty/$model->recovery_count)*100).'%'; ?></p></td>
                <td style="width: 245px"><p style="font-size: 25px"><?= number_format(($model->recovery_twentyone_to_thirty/$model->recovery_count)*100).'%'; ?></p></td>
            </tr>
        </tbody>
    </table>
<!--</div>-->



<!--<div class="borrowers-index">
    <div id="ajaxCrudDatatable">
        <?php
/*        echo DetailView::widget([
            'model'=>$model,
            'attributes'=>[
                ['attribute' => 'full_name', 'label' => 'Name'],
                ['attribute' => 'parentage', 'label' => 'Parantage'],
                ['attribute' => 'cnic', 'label' => 'CNIC'],
                ['attribute' => 'dob', 'label' => 'Date of Birth','value'=>function($data){return \common\components\Helpers\StringHelper::dateFormatter($data->dob);}]
            ]
        ]);
        */?>
    </div>
</div>-->

