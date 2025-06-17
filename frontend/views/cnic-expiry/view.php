<?php

use yii\widgets\DetailView;
use kartik\tabs\TabsX;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use common\models\Images;
/* @var $this yii\web\View */
/* @var $model common\models\Members */
$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Members', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php

$js = "
$('body').on('beforeSubmit', 'form.ImageUpload', function () {
   

     var form = $(this);
     var formData = new FormData($(\"#post-form\")[0]);
     //alert($('form')[0]);
     // return false if form still have some validation errors
    
     // submit form
     $.ajax({
          url: '/members/upload-image',
          type: 'post',
          processData: false,
         contentType: false,
          data: formData,
         datatype:'json',
          success: function (response) {
          
          
          location.reload(true);
            // $('.profile_img').attr('src', '123.png').show();
              //$('.profile_img').load(); 
               var obj = JSON.parse(response);
             
                if(obj.status_type == 'success'){
                    //alert(obj.data.message);
                   
                }else{
                  
                }
          }
     });
     return false;
});
window.setTimeout(function() {
            $(\"#statusMsg\").fadeTo(500, 0).slideUp(500, function(){
                $(this).remove(); 
            });
        }, 500);
";
$this->registerJs($js);
?>

<div class="container-fluid">
    <header class="section-header">
        <div class="tbl">
            <div class="tbl-row">
                <div class="tbl-cell">
                    <h4>View Member</h4>
                </div>
            </div>
        </div>
    </header>
    <div class="row">
        <div class="col-lg-3">
            <section class="box-typical">
                <div class="profile-card">
                    <div class="profile-card-photo">
                        <?php
                        $image = Images::findOne(['parent_id' => $model->id, 'parent_type' => 'members', 'image_type' => 'profile_pic']);
                        /*echo'<pre>';
                        print_r($a);
                        die();*/
                        //$image = \common\components\Helpers\MemberHelper::getProfileImage($model->id);
                        /*$image = \common\components\Helpers\MemberHelper::getProfileImage($model->id);

                        if (!empty($image)) {
                            $user_image = (!empty($image->image_name)) ? ($image->image_name) : 'profile_pic.png';
                            //$pic_url =  $model->id. "/" .$image->parent_type ."/" . $user_image;
                            $pic_url = $image->parent_type . "/" . $model->id . "/" . $user_image;

                        }else{
                            $pic_url =  'noimage.png';
                        }*/
                        if (!empty($image)) {
                            $profile_image=\common\components\Helpers\ImageHelper::getImageFromDisk('members',$model->id,$image->image_name,false);
                            //$pic_url =  $model->id. "/" .$image->parent_type ."/" . $user_image;


                            if ($profile_image != 'noimage.png') {
                                echo Html::img($profile_image, ['alt' => Yii::$app->name,'class' => 'rounded profile_img']);
                            } else {
                                $pic_url =  'noimage.png';
                                echo Html::img('@web/uploads/'.$pic_url, ['alt' => Yii::$app->name,'class' => 'rounded profile_img']);
                            }

                        }else{
                            $pic_url =  'noimage.png';
                            echo Html::img('@web/uploads/'.$pic_url, ['alt' => Yii::$app->name,'class' => 'rounded profile_img']);
                        }
                        ?>



                    </div>
                    <div id="statusMsg" >
                        <b><?= Yii::$app->session->getFlash('success');?></b>
                    </div>
                    <?php
                    $image=new \common\models\Images();
                    ?>
                    <div>
                        <?php $form = \yii\widgets\ActiveForm::begin([/*'action'=>'/members/upload-image',*/'options' => [
                            'class' => 'ImageUpload',
                            'id'=>'post-form',
                             ]]) ?>
                        <!--<?/*= $form->field($model, 'id')->input(['id'=>'upload','value'=>$model->id])->label(false) */?>
                        <?/*= $form->field($model, 'profile_pic')->fileInput(['style' => 'margin-left:30%'])->label(false) */?>-->
                        <?= $form->field($image, 'parent_id')->hiddenInput(['id'=>'upload','value'=>$model->id])->label(false) ?>
                        <?= $form->field($image, 'image_type')->hiddenInput(['value'=>'profile_pic'])->label(false) ?>
                        <?= $form->field($image, 'parent_type')->hiddenInput(['value'=>'members'])->label(false) ?>
                        <?= $form->field($image, 'image_data')->fileInput(['style' => 'margin-left:30%'])->label(false) ?>
                        <?= Html::submitButton('Upload', ['id'=>'save-button','class' => 'btn btn-primary btn-sm']) ?>
                    </div>
                    <br>
                    <?php \yii\widgets\ActiveForm::end() ?>
                    <div class="profile-card-name"><?= $model->full_name ?></div>
                    <div class="profile-card-status"><?= $model->parentage ?></div>
                    <div class="profile-card-location"><?= $model->cnic ?></div>
                </div><!--.profile-card-->
                <div class="profile-statistic tbl">
                    <div class="tbl-row">
                        <div class="tbl-cell">
                            <b><?= isset($model->applications) ? count($model->applications) : 0; ?></b>
                            Applications
                        </div>
                        <div class="tbl-cell">
                            <b>
                                <?php
                                echo \common\models\Loans::find()
                                    ->join('inner join','applications','applications.id=loans.application_id')
                                    ->join('inner join','members','members.id=applications.member_id')
                                    ->andFilterWhere(['=','members.id',$model->id])
                                    ->andFilterWhere(['loans.deleted'=>0])
                                    ->count();
                                ?>
                            </b>
                            Loans
                        </div>
                    </div>
                </div>
                <ul class="profile-links-list">
                    <li class="nowrap">
                        <span><b>Gender: </b></span>
                        <?= \common\models\Lists::find()->where(['list_name'=>'gender','value'=>$model->gender])->one()->label ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Date of birth: </b></span>
                        <?= date('d M Y', $model->dob) ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Mobile: </b></span>
                        <?= isset($model->membersMobile->phone) ? $model->membersMobile->phone : '-' ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Phone: </b></span>
                        <?= isset($model->membersPtcl->phone) ? $model->membersPtcl->phone : '-' ?>
                    </li>
                    <li class="nowrap">
                        <span><b>Status: </b></span>
                        <?= $model->status ?>
                    </li>
                </ul>
            </section><!--.box-typical-->
        </div><!--.col- -->
        <div class="col-xl-9 col-lg-8">
            <section class="tabs-section">
                <div class="tabs-section-nav tabs-section-nav-left">
                    <ul class="nav" role="tablist">
                        <li class="nav-link active">
                            <a class="nav-item" href="#tabs-2-tab-1" role="tab" data-toggle="tab">
                                <span class="nav-link-in">Member Info</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a class="nav-item" href="#tabs-2-tab-2" role="tab" data-toggle="tab">
                                <span class="nav-link-in">Applications</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a class="nav-item" href="#tabs-2-tab-3" role="tab" data-toggle="tab">
                                <span class="nav-link-in">Loans</span>
                            </a>
                        </li>
                        <li class="nav-link">
                            <a class="nav-item" href="#tabs-2-tab-4" role="tab" data-toggle="tab">
                                <span class="nav-link-in">CNIC</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <?php if(isset($model->membersLogs) && !empty($model->membersLogs)){
                            echo  Html::button('Logs', ['id' => 'modelButton', 'value' => \yii\helpers\Url::to(['members/logs','id' => $model->id ]), 'class' => 'nav-link nav-link-in']) ; } ?>
                         </li>
                    </ul>
                </div><!--.tabs-section-nav-->

                <div class="tab-content no-styled profile-tabs">
                    <div role="tabpanel" class="tab-pane active" id="tabs-2-tab-1">
                        <section class="box-typical box-typical-padding">
                            <div class="row">
                                <div class="col-md-4">
                                    <article class="profile-info-item">
                                        <header class="profile-info-item-header">
                                            <i class="glyphicon glyphicon-user"></i>
                                            <b>Member Information</b>
                                        </header>
                                        <div class="box-typical-inner">
                                            <p>
                                                <i class="font-icon font-icon-learn"></i>
                                                <b>Education</b> : <?= isset($model->education)?ucfirst($model->education):'Not Set'; ?>
                                            </p>
                                            <p>
                                                <i class="font-icon font-icon-build"></i>
                                                <b>Marital Status</b> : <?= isset($model->marital_status)?ucfirst($model->marital_status):'Not Set'; ?>
                                            </p>
                                            <p>
                                                <i class="font-icon font-icon-help"></i>
                                                <b>Religion</b> : <?= isset($model->religion)?ucfirst($model->religion):'Not Set'; ?>
                                            </p>
                                        </div>
                                    </article><!--.profile-info-item-->

                                </div>
                                <div class="col-md-4">
                                    <article class="profile-info-item">
                                        <header class="profile-info-item-header">
                                            <i class="fa fa-users"></i>
                                            <b>Family Information</b>
                                        </header>
                                        <div class="box-typical-inner">
                                            <p>
                                                <i class="fa fa-users"></i>
                                                <b>Family No</b> : <?= isset($model->family_no)?($model->family_no):'Not Set'; ?>
                                            </p>
                                            <p>
                                                <i class="fa fa-server"></i>
                                                <b>Family Member Name</b> : <?= isset($model->family_member_name)?($model->family_member_name):'Not Set'; ?>
                                            </p>
                                            <p>
                                                <i class="fa fa-cc-mastercard"></i>
                                                <b>Family Member CNIC</b> : <?= isset($model->family_member_cnic)?($model->family_member_cnic):'Not Set'; ?>
                                            </p>
                                            <p>
                                                <i class="fa fa-header"></i>
                                                <b>Family Head</b> : <?= isset($model->family_head)?($model->family_head):'Not Set'; ?>
                                            </p>
                                        </div>
                                    </article><!--.profile-info-item-->

                                </div>
                                <div class="col-md-4">
                                    <article class="profile-info-item">
                                        <header class="profile-info-item-header">
                                            <i class="font-icon font-icon-view-rows"></i>
                                            <b>Credit Structure Information</b>
                                        </header>
                                        <div class="box-typical-inner">
                                            <p>
                                                <b>Region</b>
                                                : <?= isset($model->region->name) ? $model->region->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Area</b>
                                                : <?= isset($model->area->name) ? $model->area->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Branch</b>
                                                :<?= isset($model->branch->name) ? $model->branch->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Team</b>
                                                : <?= isset($model->team->name) ? $model->team->name : 'Not Set'; ?>
                                            </p>
                                            <p>
                                                <b>Field</b>
                                                : <?= isset($model->field->name) ? $model->field->name : 'Not Set'; ?>
                                                <?= isset($model->field->userStructureMapping->user->username) ? '('.$model->field->userStructureMapping->user->username.')' : '(--)'; ?>
                                            </p>
                                            <!--</article>-->
                                        </div>
                                    <!--.profile-info-item-->
                                </div>
                            </div>
                           <!--<?/*= DetailView::widget([
                                'model' => $model,
                                'attributes' => [
                                    [
                                        'attribute' => 'education',
                                        'label' => 'Education',
                                    ],
                                    'marital_status',
                                    'family_no',
                                    'family_member_name',
                                    'family_member_cnic',
                                    'religion'
                                ],
                            ]) */?>-->
                            <?php

                            $dataProviderAddresses = new ArrayDataProvider([
                                'allModels' => $model->membersAddresses,
                            ]);

                            ?>
                            <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h6 class="address-heading"><span class="glyphicon glyphicon-home"></span>
                                            Addresses Detail</h6>
                                    </div>
                                </div>
                            </header>

                            <?= GridView::widget([
                                'dataProvider' => $dataProviderAddresses,
                                //'filterModel' => $model->membersAddresses,
                                'summary' => "",
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    [
                                        'attribute' => 'address',
                                    ],
                                    [
                                        'attribute' => 'address_type',
                                    ],
                                    [
                                        'attribute' => 'is_current',
                                      'format'=>'raw',
                                        'value'=>function($data){
                                            if ($data->is_current == '1') {
                                                return  '<span class="glyphicon glyphicon-ok" style="color:green"></span>';
                                            } else {
                                                return '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
                                            }
                                        }
                                    ],
                                ],
                            ]); ?>
                            <?php

                            $dataProviderAccounts = new ArrayDataProvider([
                                'allModels' => $model->membersAccountAll,
                            ]);

                            ?>
                            <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h6 class="address-heading"><span class="glyphicon glyphicon-home"></span>
                                            Accounts Detail</h6>
                                    </div>
                                </div>
                            </header>

                            <?= GridView::widget([
                                'dataProvider' => $dataProviderAccounts,
                                //'filterModel' => $model->membersAddresses,
                                'summary' => "",
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    [
                                        'attribute' => 'bank_name',
                                    ],
                                    [
                                        'attribute' => 'account_no',
                                    ],
                                    [
                                        'attribute' => 'is_current',
                                        'format'=>'raw',
                                        'value'=>function($data){
                                            if ($data->is_current == '1') {
                                                return  '<span class="glyphicon glyphicon-ok" style="color:green"></span>';
                                            } else {
                                                return '<span class="glyphicon glyphicon-remove" style="color:red"></span>';
                                            }
                                        }
                                    ],
                                ],
                            ]); ?>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane" id="tabs-2-tab-2">
                        <section class="box-typical box-typical-padding">
                            <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h6 class="address-heading"><span class="glyphicon glyphicon-edit"></span>
                                            Applications</h6>
                                    </div>
                                </div>
                            </header>
                            <a class="btn btn-primary pull-right" href="/applications/create?id=<?php echo $model->id?>">Add Application</a>
                            <br>
                            <br>

                            <?php

                            $dataProviderAddresses = new ArrayDataProvider([
                                'allModels' => $model->applications,
                            ]);

                            ?>
                            <?= GridView::widget([
                                'dataProvider' => $dataProviderAddresses,
                                //'filterModel' => $model->membersAddresses,
                                'summary' => "",
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    [
                                        'attribute' => 'application_no',
                                    ],
                                    [
                                        'attribute' => 'project_id',
                                        'value'=>'project.name',
                                        'label'=>'Project',
                                    ],
                                    [
                                        'attribute' => 'no_of_times',
                                    ],
                                ],
                            ]); ?>
                        </section>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane" id="tabs-2-tab-3">
                        <section class="box-typical box-typical-padding">
                            <header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h6 class="address-heading"><span class="glyphicon glyphicon-tag"></span>
                                            Loans</h6>
                                    </div>
                                </div>
                            </header>
                            <?php

                            $dataProviderAddresses = new ArrayDataProvider([
                                /*'allModels' => $model->applications->loan,*/
                            ]);

                            ?>
                            <?= GridView::widget([
                                'dataProvider' => $dataProviderAddresses,
                                //'filterModel' => $model->membersAddresses,
                                'summary' => "",
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],
                                    [
                                        'attribute' => 'sanction_no',
                                    ],
                                    [
                                        'attribute' => 'loan_amount',
                                    ],
                                    [
                                        'attribute' => 'date_disbursed',
                                        'value'=>function($data){return date('Y-m-d',$data->date_disbursed);}
                                    ],
                                ],
                            ]); ?>
                        </section>
                    </div><!--.tab-pane-->
                    <div role="tabpanel" class="tab-pane" id="tabs-2-tab-4">
                        <section class="box-typical box-typical-padding">
                            <!--<header class="box-typical-header">
                                <div class="tbl-row">
                                    <div class="tbl-cell tbl-cell-title">
                                        <h6 class="address-heading"><span class="glyphicon glyphicon-"></span>
                                            Documents</h6>
                                    </div>

                                </div>
                            </header>-->
                            <!--<?php /*echo \yii\helpers\Html::a('<span class="btn btn-primary pull-right" style="margin-bottom: 2px">Add CNIC Pictures</span>', ['add-document', 'id' => $model->id],['target'=>'blank'], ['role' => 'modal-remote','title' => 'Add Document']); */?>-->

                            <div class="row">
                                <div class="col-sm-6">
                                    <h6><b>Front CNIC</b></h6>
                                            <?php
                                            $image = \common\components\Helpers\MemberHelper::getFCnic($model->id);

                                            if (!empty($image)) {
                                                $f_cnic=\common\components\Helpers\ImageHelper::getImageFromDisk('members',$model->id,$image->image_name,false);
                                                echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);


                                            }else{
                                                $pic_url =  'noimage.png';
                                                echo Html::img('@web/uploads/'.$pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                            }
                                            ?>
                                    <?php $doc_1=new \common\models\Images()?>
                                    <?php $form = \yii\widgets\ActiveForm::begin(['action'=>'add-document?id='.$model->id]); ?>


                                    <?= $form->field($doc_1, 'parent_id')->hiddenInput(['id'=>'upload','value'=>$model->id])->label(false) ?>
                                    <?= $form->field($doc_1, 'parent_type')->hiddenInput(['value'=>'members'])->label(false) ?>
                                    <?= $form->field($doc_1, 'image_type')->hiddenInput(['value'=>'cnic_front'],['prompt'=>'Select Type'])->label(false) ?>
                                    <br>
                                    <?= $form->field($doc_1, 'image_data')->fileInput([/*'style' => 'margin-left:30%'*/])->label(false) ?>
                                    <br>

                                    <?php if (!Yii::$app->request->isAjax){ ?>
                                        <div class="form-group">
                                            <?= Html::submitButton($doc_1->isNewRecord ? 'Upload CNIC Front' : 'Upload CNIC Front', ['class' => $doc_1->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                        </div>
                                    <?php } ?>

                                    <?php \yii\widgets\ActiveForm::end(); ?>
                                </div>
                                <div class="col-sm-6">
                                    <h6><b>Back CNIC</b></h6>
                                    <?php
                                    $image = \common\components\Helpers\MemberHelper::getBCnic($model->id);

                                    if (!empty($image)) {
                                        $f_cnic=\common\components\Helpers\ImageHelper::getImageFromDisk('members',$model->id,$image->image_name,false);
                                        echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);


                                    }else{
                                        $pic_url =  'noimage.png';
                                        echo Html::img('@web/uploads/'.$pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                    }
                                    ?>
                                    <?php $doc_2=new \common\models\Images()?>
                                    <?php $form = \yii\widgets\ActiveForm::begin(['action'=>'add-document?id='.$model->id]); ?>


                                    <?= $form->field($doc_2, 'parent_id')->hiddenInput(['id'=>'upload','value'=>$model->id])->label(false) ?>
                                    <?= $form->field($doc_2, 'parent_type')->hiddenInput(['value'=>'members'])->label(false) ?>
                                    <?= $form->field($doc_2, 'image_type')->hiddenInput(['value'=>'cnic_back'],['prompt'=>'Select Type'])->label(false) ?>
                                    <br>
                                    <?= $form->field($doc_2, 'image_data')->fileInput([/*'style' => 'margin-left:30%'*/])->label(false) ?>
                                    <br>

                                    <?php if (!Yii::$app->request->isAjax){ ?>
                                        <div class="form-group">
                                            <?= Html::submitButton($doc_2->isNewRecord ? 'Upload CNIC Back' : 'Upload CNIC Back', ['class' => $doc_2->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                        </div>
                                    <?php } ?>

                                    <?php \yii\widgets\ActiveForm::end(); ?>
                                </div>
                            </div>
                            <hr>
                            <div class="row" style="display: none">
                                <div class="col-sm-6">
                                    <h6><b>Nadra Document</b></h6>
                                    <?php
                                    $image = \common\components\Helpers\MemberHelper::getNadraDocument($model->id);

                                    if (!empty($image)) {
                                        $f_cnic=\common\components\Helpers\ImageHelper::getImageFromDisk('members',$model->id,$image->image_name,false);
                                       // echo Html::img($f_cnic, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                       echo Html::a('Download Nadra Document', [
                                            'members/pdf',
                                            'id' => $model->id,
                                        ], [
                                            'class' => 'btn btn-primary',
                                            'target' => '_blank',
                                        ]);

                                    }else{
                                        $pic_url =  'noimage.png';
                                        echo Html::img('@web/uploads/'.$pic_url, ['alt' => 'No Image Found', 'class' => 'rounded profile_img', 'style' => 'width:70%']);
                                    }
                                    ?>
                                    <?php $doc_3=new \common\models\Images()?>
                                    <?php $form = \yii\widgets\ActiveForm::begin(['action'=>'add-nadra-document?id='.$model->id]); ?>


                                    <?= $form->field($doc_3, 'parent_id')->hiddenInput(['id'=>'upload','value'=>$model->id])->label(false) ?>
                                    <?= $form->field($doc_3, 'parent_type')->hiddenInput(['value'=>'members'])->label(false) ?>
                                    <?= $form->field($doc_3, 'image_type')->hiddenInput(['value'=>'nadra_document'],['prompt'=>'Select Type'])->label(false) ?>
                                    <br>
                                    <?= $form->field($doc_3, 'image_data',['enableClientValidation' => false])->fileInput([ 'accept'=>".pdf"/*'style' => 'margin-left:30%'*/])->label(false) ?>
                                    <br>

                                    <?php if (!Yii::$app->request->isAjax){ ?>
                                        <div class="form-group">
                                            <?= Html::submitButton($doc_3->isNewRecord ? 'Upload Nadra Document' : 'Upload Nadra Document', ['class' => $doc_3->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                        </div>
                                    <?php } ?>

                                    <?php \yii\widgets\ActiveForm::end(); ?>
                                </div>
                            </div>
                        </section>
                    </div><!--.tab-pane-->
                </div><!--.tab-content-->
            </section><!--.tabs-section-->
        </div>

    </div><!--.row-->
</div><!--.container-fluid-->

<?php

\yii\bootstrap\Modal::begin([
    'header' => '<h4 class="modal-title">Logs</h4>',
    'headerOptions' => ['style' => ['display' => 'block']],
    'id'     => 'model',
    'size'   => 'model-lg',
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