<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\BorrowersSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

/*echo '<pre>';
print_r($model->analytics);
die();*/
?>

<div class="borrowers-index">
    <div class="panel panel-info">
        <div class="panel-heading">
            <div><h3 class="panel-title">
                    <span><?php //echo $model->fullname; ?></span>
                </h3></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Api</th>
                            <th>Count</th>
                            <th>Description</th>
                            <th>First Visit</th>
                            <th>Last Visit</th>
                            <th>Deleted</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($model as $m){ ?>
                            <tr>
                                <td><?php echo $m->api ?></td>
                                <td><?php echo $m->count; ?></td>
                                <td><?php echo $m->description ?></td>
                                <td><?php echo date("M j, Y h:i", ($m->created_at)); ?></td>
                                <td><?php echo date("M j, Y h:i", ($m->updated_at)); ?></td>
                                <td><?php
                                    if (!$m->deleted) {
                                        echo '<span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span>'; // or return true;
                                    } else {
                                        echo '<span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span>'; // or return false;
                                    }
                                    ?>
                                </td>

                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
