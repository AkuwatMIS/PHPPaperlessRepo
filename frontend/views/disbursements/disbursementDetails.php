<?php
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php
    if(isset($model)){
    ?>
        <div class="disbursements-index">
        <div id="ajaxCrudDatatable">
            <table id="table-edit" class="table table-bordered table-hover">
                <thead>
                <tr>
                    <th width="2">No.</th>
                    <th>Name</th>
                    <th>Cnic</th>
                    <th> Sanction No</th>
                    <th>Tranch No</th>
                    <th>Tranch Amount</th>
                    <th>Inst. Amount</th>
                    <th>inst. Type</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($model as $model) { ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= isset($model->loan->application->member->full_name) ? $model->loan->application->member->full_name : '' ?></td>
                        <td><?= isset($model->loan->application->member->cnic) ? $model->loan->application->member->cnic : '' ?></td>
                        <td><?= isset($model->loan->sanction_no) ? $model->loan->sanction_no : '' ?></td>
                        <td><b><?= isset($model->tranch_no) ? number_format($model->tranch_no) : '' ?></b></td>
                        <td><?= isset($model->tranch_amount) ? number_format($model->tranch_amount) : '' ?></td>
                        <td><?= isset($model->loan->inst_amnt) ? number_format($model->loan->inst_amnt) : '' ?></td>
                        <td><?= isset($model->loan->inst_type) ? $model->loan->inst_type : '' ?></td>
                    </tr>
                    <?php
                    $i++;
                } ?>
                </tbody>
            </table>
        </div>
    </div>
<?php } ?>