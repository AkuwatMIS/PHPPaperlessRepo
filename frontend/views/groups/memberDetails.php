<?php
use yii\widgets\DetailView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

if(isset($model->applications)){
?>
<div class="borrowers-index">
    <div id="ajaxCrudDatatable">
        <table id="table-edit" class="table table-bordered table-hover">
            <thead>
            <tr>
                <th width="2">No.</th>
                <th>Full Name</th>
                <th>Parentage</th>
                <th>CNIC</th>
                <th>Application No</th>
                <th>Sanction No</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $i = 1;
                foreach ($model->applications as $key => $a) { ?>
                    <tr>
                        <td><?= $i ?></td>
                        <td><?= isset($a->member->full_name) ? $a->member->full_name : '' ?></td>
                        <td><?= isset($a->member->parentage) ? $a->member->parentage : '' ?></td>
                        <td><?= isset($a->member->cnic) ? $a->member->cnic : '' ?></td>
                        <td><?= isset($a->application_no) ? $a->application_no : '-' ?></td>
                        <td><?= isset($a->loan->sanction_no) ? $a->loan->sanction_no : '-' ?></td>
                    </tr>
                    <?php
                    $i++;
                } ?>
            </tbody>
        </table>
    </div>
</div>
<?php } ?>