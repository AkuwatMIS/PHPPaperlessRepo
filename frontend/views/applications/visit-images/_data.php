<?php

use common\components\Helpers\ImageHelper;
use yii\helpers\Html;
//print_r($model->member->membersMobile);die();
?>


<header>
<link rel="stylesheet" href="/css/simpleLightbox.min.css">
</header>

<?php
if(isset($_GET['page']) && !empty($_GET['page'])){
    $page=$_GET['page'];
}
else {
    $page = '';
}
$image = \common\components\Helpers\MemberHelper::getProfileImage($model->member->id);
$referral = \common\components\Helpers\MemberHelper::getReferral($model->referral_id);
if (!empty($image)) {
    $pic_url=\common\components\Helpers\ImageHelper::getImageFromDisk('members',$model->member->id,$image->image_name,false);
}else{
    $pic_url =  '@web/uploads/noimage.png';
  }
$i=1;
?>
<div class="col-md-12" >
            <div class="card mb-0" style="background-color: ghostwhite">
                <div class="card-typical-section">
                    <div class="user-card-row">
                        <div class="tbl-row">
                            <div class="profile-card-photo">
                                <?php echo Html::img( $pic_url, ['alt' => '','height'=>125,'width'=>130]); ?>
                            </div>
                            <div class="tbl-cell">
                                <p><a href="/applications/visit-details?id=<?=$model->id?>" target="_blank" title="Click me for application details!" ><b>NAME &nbsp;(<?php echo  $model->member->full_name; ?>)</b></a></p>
                                <p><a href="/applications/visit-details?id=<?=$model->id?>" target="_blank" title="Click me for application details!" ><b>CNIC (<?php echo  $model->member->cnic; ?>)</b></a></p>
                                <?php $disb= \common\models\Loans::find()->where(['application_id'=>$model->id])->one();
                                if($disb->loan_amount==$disb->disbursed_amount ){ ?>
                                    <font color="#0082c6"><b>Disbusement Status (Disbursed)</b></font>
                                <?php } elseif ($disb->disbursed_amount<$disb->loan_amount && $disb->disbursed_amount>0){?>
                                <font color="#0082c6"   ><b>Disbusement Status (Partially Disbursed)</b></font>
                                <?php } elseif($disb->disbursed_amount==0){ ?>
                                <a href=""   ><b>Disbusement Status (Not Disbursed)</b></a>
                               <?php } ?>
                                <p><a href=""><b>Referred By (<?=$referral?>) </b></a></p>
                            </div>
                        </div>

                    </div>
                </div>

               <!-- <a class="card-title" >
                    <a href="view?id=<?/*=$model->id*/?>"  ><b>&nbsp;&nbsp;&nbsp;&nbsp; CNIC (<?php /*echo  $model->member->cnic; */?>)</b></a>
                </a>-->

<?php foreach($model->visits as $visit){ ?>


<article class="card-typical col-sm-11 center-block">
    <div class="card-typical-section">
        <div class="tbl-cell">
            <p>Visit <b><?= $i++; ?></b></p>
        </div>
        <div class="row">
                <?php
                foreach ($visit->images as $visit_image) {?>
                   <?php $user_image = $visit_image->image_name;
                    $pic_url = ImageHelper::getAttachmentApiPath() . '?type=visits&id=' . $visit_image->parent_id . "&file_name=" . $visit_image->image_name . '&download=false';
                    ?>
                    <div class="col-md-4">
                        <?php if($visit_image->is_published) {
                           echo  \yii\helpers\Html::a('<span class="glyphicon glyphicon-remove" style="font-size:25px; color: red;    display: inline-block;border-radius: 60px;box-shadow: 0px 0px 2px;padding: 0.2em 0.2em;"></span>',
                               ['publish-image', 'id' => $visit_image->id,'app_id'=> $model->id,'page'=>$page],['style' => "position: absolute;margin-left: 10px;margin-top: 10px;",'title' => 'Un Mark']);
                         } else {
                            echo  \yii\helpers\Html::a('<span class="glyphicon glyphicon-ok" style="font-size:25px;color: green;    display: inline-block;border-radius: 60px;box-shadow: 0px 0px 2px;padding: 0.2em 0.2em;"></span>',
                                ['publish-image', 'id' => $visit_image->id,'app_id'=> $model->id,'page'=>$page],['style' => "position: absolute;margin-left: 10px;margin-top: 10px;",'title' => 'Mark Publish']);
                         } ?>
                        <div class="imageGallery1 <?php echo $visit_image->id; ?>">
                        <a href="<?php echo $pic_url ?>" title=""><img src='<?= $pic_url ?>' height='270' width="100%" alt=""></a>
                        </div>
                    </div>
                <?php  } ?>
        </div>
    </div>
</article>
    <?php  } ?>

</div>
<?php
\yii\bootstrap\Modal::begin([
'header' => '<h4 class="modal-title">Logs</h4>',
'headerOptions' => ['style' => ['display' => 'block']],
'id' => 'model',
'size' => 'model-lg',
'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]),
]);

echo "<div id='modelContent'></div>";

\yii\bootstrap\Modal::end();

?>
<?php
$script = "$(function(){
$('#modelButton').click(function(){
$('.modal').modal('show')
.find('#modelContent')
.load($(this).attr('value'));
});
});";
$this->registerJs($script);
?>
<script src="/js/simpleLightbox.min.js"></script>
<script>
    new SimpleLightbox({elements: '.imageGallery1 a'});
    $('.customContentLink').on('click', function () {
        SimpleLightbox.open({
            content: '<div class="contentInPopup">' +
                '<h3 class="attireTitleType3">Custom content</h3>' +
                '<p class="attireTextType2">' +
                'There are times when you need to show some other type of content in lightbox beside images.' +
                'SimpleLightbox can be used to display forms, teasers or any custom html content.' +
                '</p>' +
                '</div>',
            elementClass: 'slbContentEl'
        });
    });
    $('.lightBoxVideoLink').simpleLightbox();
</script>
