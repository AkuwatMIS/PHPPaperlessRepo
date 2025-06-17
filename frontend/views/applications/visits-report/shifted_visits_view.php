<?php

use yii\widgets\DetailView;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Loans */
$this->title = $model->application_no;
$this->params['breadcrumbs'][] = ['label' => 'Loans', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$permissions = Yii::$app->session->get('permissions');
$visit = new \common\models\Visits();
?>
<link rel="stylesheet" href="/css/simpleLightbox.min.css">
<div class="container-fluid">
    <div class="col-xl-12 col-lg-12">
        <section class="tabs-section">
            <div class="tab-content no-styled profile-tabs">
                <div role="tabpanel" class="tab-pane active" id="tabs-2-tab-1">
                    <section class="box-typical box-typical-padding">
                        <br>
                        <p>Tranches Disbursed</p>
                        <?php if (isset($model->loan->tranches)) {
                            foreach ($model->loan->tranches as $tranche) {
                                if ($tranche->date_disbursed != 0) {
                                    ?>
                                    <p> Tranche <?= $tranche->tranch_no; ?>:
                                     <b> <?= date('d M Y', $tranche->date_disbursed); ?></b>
                                    </p>
                                <?php }
                            }
                        } ?>
                        <?php if (!empty($details)) {
                            $i = 1;
                            ?>

                            <?php foreach ($details as $detail) {
                                if ($detail['is_tranche'] == 0) {
                                    $status = 'First Tranche';
                                } else if ($detail['is_tranche'] == 1) {
                                    $status = 'Second Tranche';
                                } else if ($detail['is_tranche'] == 2) {
                                    $status = 'Random';
                                } else if ($detail['is_tranche'] == 3) {
                                    $status = 'Third Tranche';
                                } else if ($detail['is_tranche'] == 4) {
                                    $status = 'Fourth Tranche';
                                } else {
                                    $status = '';
                                }
                                ?>

                                <div class="card">
                                    <div class="card-header">
                                        <?php if (in_array('frontend_update-visitapplications', $permissions)) { ?>
                                            <?= \yii\helpers\Html::a('Approve Visit',
                                                ['approve-shifted-visit', 'id' => $detail['visit_id']],
                                                [
                                                    'class' => 'btn btn-success',
                                                    'style' => 'margin-top: 30px',
                                                    'data-method' => 'post',
                                                ]
                                            ) ?>

                                        <?php } ?>
                                    </div>

                                    <div class="card-body">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th scope="col">CNIC</th>
                                                <th scope="col">Visit</th>
                                                <th scope="col">Visit Date</th>
                                                <th scope="col">Visit By</th>
                                                <th scope="col">Complete Percentage</th>
                                                <th scope="col">Comments</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th scope="row"><?= $model->member->cnic ?></th>
                                                <th>(<?= $status; ?> - <?= $detail['visit_id']; ?>)</th>
                                                <td><?= $detail['visited_date'] ?></td>
                                                <td><?= $detail['visited_by'] ?></td>
                                                <td><?= $detail['percentage'] . '%' ?></td>
                                                <td><?= $detail['notes'] ?></td>
                                            </tr>
                                            </tbody>
                                        </table>

                                        <hr>
                                        <div class="row">
                                            <p><b>Visit Images:</b></p>
                                            <div class="imageGallery1 <?php echo $i; ?>">
                                                <?php foreach ($detail['images'] as $image) { ?>
                                                    <div class="col-md-4">
                                                        <a href="<?php echo $image ?>"
                                                           title=""> <?php echo \yii\helpers\Html::img(/*'@web/uploads/' . */
                                                                $image, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'height' => 270, 'style' => 'width:100%']); ?></a>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <?php $i++;
                            } ?>
                        <?php } else { ?>
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
