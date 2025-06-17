<?php

use common\components\Helpers\ImageHelper;
use yii\helpers\Html;


$user_image=$model->image;
$pic_url=ImageHelper::getAttachmentApiPath(). '?type=users&file_name=' . $user_image .'&download=false';
if(!isset($user_image)&& empty($user_image)) {
    $user_image = 'noimage.png';
    $pic_url = ImageHelper::getAttachmentApiPath() . '?type=users&file_name=' . $user_image . '&download=false';
}
?>
        <div class="column size-1of4">
            <div class="col-sm-4">
                <div class="card-grid-col">
                    <article class="card-typical">
                        <div class="card-typical-section">
                            <div class="user-card-row">
                                <div class="tbl-row">
                                    <div class="profile-card-photo">
                                        <img src='<?= $pic_url ?>' width='100' height='100' alt="" >
                                    </div>
                                    <div class="tbl-cell">
                                        <p class="color-blue-gray-darkar"> &nbsp;&nbsp; <h4> <?=$model->fullname ?></h4></p>
                                        <p class="color-black-blue"><h6><?=$model->role->itemName->description ?></h6></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-typical-section card-typical-content">
                            <p>
                               <font size="4" color="#663399">Area</font>
                                <b>&nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp; &nbsp; <?=$model->area->userArea->name ?></b>
                            </p>
                            <p>
                           <font size="4" color="#663399">Region</font>
                               <b>&nbsp; &nbsp; &nbsp;  <?=$model->region->userRegion->name ?></b>
                            </p>
                            <p>
                            <font color="#00008b">Mobile</font>
                                </font>&nbsp; &nbsp; &nbsp;&nbsp;&nbsp; &nbsp; <?= $model->mobile ?>
                            </p>
                            <p>
                           <font  color="#00008b">Email</font>
                                &nbsp; &nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;  <?= $model->email ?>
                            </p>
                            <p>
                            <font color="#00008b">Emp. Code </font>
                                &nbsp;  <?=$model->emp_code ?>
                            </p>
                        </div>
                    </article>   <br>
                    <!--.card-typical-->
                </div>
            </div>
        </div>