<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

//print_r($model);
//die();

?>

<div class="borrowers-index">
    <div class="panel panel-info">
        <div class="panel-heading">
            <div><h3 class="panel-title">
                    <span><?php echo $member->full_name; ?></span>
                <span class="pull-right">

                </span>
            </h3></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Form No</th>
                            <th>Member's Name</th>
                            <th>Sur Name</th>
                            <th>CNIC</th>
                            <th>Religion</th>
                            <th>Education</th>
                            <th>Date Of Birth</th>

                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><?php echo $member->full_name; ?></td>
                                <td><?php echo $member->cnic ?></td>
                                <td><?php echo $member->religion ?></td>
                                <td><?php echo $member->education ?></td>
                                <td><?php echo date('d-M-Y', $member->dob) ?></td>


                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
