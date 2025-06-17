<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\MobilePermissions */

$this->title = 'Permissions';
$this->params['breadcrumbs'][] = ['label' => 'Mobile Permissions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mobile-permissions-view">
    <table class="table table-striped table-bordered detail-view">
        <thead>
        <th>Screens</th>
        <th>Permissions</th>
        </thead>
        <tbody>
        <?php
        if(isset($screens)) {
            foreach ($screens as $screen)
            { ?>
                <tr>
                    <td><?=$screen->name?></td>
                    <?php if(!in_array($screen->name, $permissions)) { ?>
                        <td><span style="font-size:15px;color:red;" class="glyphicon glyphicon-remove"></span></td>
                    <?php } else { ?>
                        <td><span style="font-size:15px;color:green;" class="glyphicon glyphicon-ok"></span></td>
                    <?php } ?>

                </tr>
                <?php
            }
        } ?>
        </tbody>
    </table>
</div>
