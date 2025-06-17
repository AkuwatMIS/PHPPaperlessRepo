<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */
$this->title = $model->application_no;
$this->params['breadcrumbs'][] = ['label' => 'Loans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
$visit=new \common\models\Visits();
?>
<link rel="stylesheet" href="/css/simpleLightbox.min.css">
    <div class="container-fluid">
        <header class="section-header">
            <div class="tbl">
                <div class="tbl-row">
                    <div class="tbl-cell">
                        <h4>Visits:(<?= $model->member->cnic?>)</h4>
                    </div>
                </div>
            </div>
        </header>
        <div class="col-xl-12 col-lg-12">
            <section class="tabs-section">
                <div class="tab-content no-styled profile-tabs">
                    <div role="tabpanel" class="tab-pane active" id="tabs-2-tab-1">
                        <section class="box-typical box-typical-padding">
                            <br>

                            <?php if(isset($model->loan->tranches)) {
                                foreach ($model->loan->tranches as $tranche) {
                                    if ($tranche->date_disbursed != 0) {
                                        ?>
                                        <p><b> Date Disbursed (Tranche <?= $tranche->tranch_no; ?>
                                                ): <?= date('d M Y', $tranche->date_disbursed); ?></b></p>
                                    <?php } else { ?>
                                        <p><b> Date Disbursed (Tranche <?= $tranche->tranch_no; ?>): </b></p>

                                    <?php }
                                }
                            } ?>
                            <?php if (!empty($details)) {
                                $i=1;
                                ?>

                                <?php foreach ($details as $detail) {
                                    if($detail['is_tranche']==0){
                                        $status='First Tranche';
                                    }
                                    else if($detail['is_tranche']==1){
                                        $status='Second Tranche';
                                    } else if($detail['is_tranche']==2){
                                        $status='Random';
                                    }else if($detail['is_tranche']==3){
                                        $status='Third Tranche';
                                    }else if($detail['is_tranche']==4){
                                        $status='Fourth Tranche';
                                    }
                                    else {
                                        $status='';
                                    }
                                    ?>

                                    <p><h3><strong>Visit:</strong><?= $i ;?> &nbsp <medium>(<?= $status ;?> - <?= $detail['visit_id'] ;?>)</medium></h3></p>
                                    <?php //if($active_first_visit || in_array($detail['is_tranche'],[1,2] )) { ?>
                                    <?php echo  \yii\helpers\Html::a('Delete Pictures', ['delete-visit', 'visit_id' =>  $detail['visit_id'],'id' =>$model->id ], ['title' => 'Delete','class' =>'btn btn-success pull-right']); ?>
                                    <?php// } ?>
                                    <!---------------------------------------------Delete Visits Images Start---------------------------------------------------->

                                    <?php //if(in_array('frontend_delete-visit-imageapplications',$permissions) && $active_first_visit || in_array($detail['is_tranche'],[1,2] ))
                                    // { ?>
                                    <?= \yii\helpers\Html::button('Delete Visit Image', [
                                    'class' =>'btn btn-danger pull-right',
                                    'title' =>'Delete Images',
                                    'id' => 'BtnModalId',
                                    //'style'=>'margin-top: 30px',
                                    'data-toggle'=> 'modal',
                                    'data-target'=> '#picture-modal'.$detail['visit_id'],

                                ]) ?>

                                    <?php// }?>
                                    <?php $form = \yii\widgets\ActiveForm::begin(['action'=>'delete-visit-image?parent_id='.$model->id,'method'=>'post']); ?>
                                    <?php
                                    \yii\bootstrap\Modal::begin([

                                        'header' => '',

                                        'id' => 'picture-modal'.$detail['visit_id'],

                                        'size' => 'modal-md',

                                    ]); ?>

                                    <?php foreach ($detail['images'] as $image) { ?>
                                        <!--.gallery-col-->
                                        <article class="gallery-item col-sm-4">
                                            <img class="gallery-picture" src=<?=$image?> alt="NoImg" height="158">
                                            <div class="gallery-hover-layout">
                                                <div class="gallery-hover-layout-in">
                                                    <div class="btn-group">
                                                        <?php
                                                        $parts = parse_url($image);
                                                        parse_str($parts['query'], $query);
                                                        //echo $query['file_name'];
                                                        //echo $query['id'];
                                                        ?>
                                                        <a href="delete-visit-image?id=<?=$query['id']?>&image_name=<?=$query['file_name']?>"> <button type="button" class="btn">
                                                                <i class="font-icon font-icon-trash"></i>
                                                            </button></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </article>
                                        <!--.gallery-col-->
                                    <?php } ?>
                                    <?php \yii\bootstrap\Modal::end();
                                    ?>
                                    <?php \yii\widgets\ActiveForm::end(); ?>


                                    <!---------------------------------------------Delete Visits Images End / Update start---------------------------------------------------->

                                    <?php if(in_array('frontend_update-visitapplications',$permissions))
                                    { ?>
                                        <?= \yii\helpers\Html::button('Update Visit', [
                                        'class' =>'btn btn-success ',
                                        'id' => 'BtnModalId',
                                        'style'=>'margin-top: 30px',
                                        'data-toggle'=> 'modal',
                                        'data-target'=> '#your-modal'.$detail['visit_id'],

                                    ]) ?>

                                    <?php }?>

                                    <?php $form = \yii\widgets\ActiveForm::begin(['action'=>'update-visit?application_id='.$model->id,'method'=>'post']); ?>


                                    <?php
                                    \yii\bootstrap\Modal::begin([

                                        'header' => '',

                                        'id' => 'your-modal'.$detail['visit_id'],

                                        'size' => 'modal-md',

                                    ]); ?>
                                    <?= $form->field($visit, 'id')->hiddenInput(['value'=>$detail['visit_id']])->label(false) ?>
                                    <?= $form->field($visit, 'is_tranche')->dropDownList([0=>'1st Tranche',1=>'2nd Tranche',2=>'Random'], ['value'=>$detail['is_tranche'],'prompt'=>'Select Visit Type'])->label('Visit Type') ?>

                                    <?= $form->field($visit, 'percent')->textInput(['value'=>$detail['percentage'],'placeholder'=>'Enter Completion Percentage'])->label('Completion Percentage') ?>
                                    <?= Html::submitButton( 'Update', ['class' =>'btn btn-primary']) ?>

                                    <?php \yii\bootstrap\Modal::end();
                                    ?>
                                    <?php \yii\widgets\ActiveForm::end(); ?>


                                    <!---------------------------------------------Update Visit End---------------------------------------------------->
                                    <div class="row">
                                        <div class="imageGallery1 <?php echo $i;?>">
                                            <?php foreach ($detail['images'] as $image) { ?>
                                                <div class="col-md-4">
                                                    <a href="<?php echo $image ?>" title=""> <?php echo \yii\helpers\Html::img(/*'@web/uploads/' . */$image, ['alt' => 'No Image Found', 'class' => 'rounded profile_img','height'=>270, 'style' => 'width:100%']); ?></a>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <br>
                                            <p><?= '<b>Estimated Start Date: </b>' . $detail['estimated_start_date'] ?></p>
                                            <p><?= '<b>Etimated Completion Time: </b>' . $detail['estimated_completion_time']?></p>
                                        </div>
                                        <div class="col-sm-4">
                                            <br>
                                            <p><?= '<b>Estimated Figures: </b>'?></p>
                                            <?php if(!empty($detail['estimated_figures']) && $detail['estimated_figures'] !=null){
                                                $formated=json_decode($detail['estimated_figures']);
                                                $j=1;
                                                if (!empty($formated) && $formated!=null){
                                                    foreach($formated as $f){ ?>
                                                        <p>
                                                            <b><?= $j?>) Name: </b><?= isset($f->item)?$f->item:''?>
                                                            <b>, Quantity: </b><?= isset($f->qty)?number_format($f->qty):''?>
                                                            <b>, Amount: </b><?= isset($f->amount)?number_format($f->amount):''?>
                                                        </p>
                                                        <?php $j++; }
                                                }
                                            } ?>

                                        </div>
                                        <div class="col-sm-4">
                                            <br>
                                            <p><?= '<b>Visit Date: </b>' . $detail['visited_date'] ?></p>
                                            <p><?= '<b>Visit By: </b>' . $detail['visited_by']?></p>
                                            <p><?= '<b>Complete Percentage: </b>' . $detail['percentage'] . '%' ?></p>
                                            <p><?= '<b>Comments: </b>' . $detail['notes'] ?></p>
                                        </div>
                                    </div>
                                    <hr>
                                    <?php $i++; }  ?>
                            <?php } else{?>
                                <b>No record found</b>
                            <?php } ?>
                        </section>
                    </div>
            </section>
        </div>
    </div>
    <!--end-->
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
