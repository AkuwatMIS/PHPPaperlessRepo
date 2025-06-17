<?php

use johnitvn\ajaxcrud\CrudAsset;
use \fruppel\googlecharts\GoogleCharts;
use yii\bootstrap\Modal;
use yii\helpers\Html;
//use yii\grid\GridView;
use kartik\grid\GridView;
/* @var $this yii\web\View */
/* @var $searchModel common\models\search\LoansSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Summary Report';
$this->params['breadcrumbs'][] = $this->title;
CrudAsset::register($this);

?>
    <div class="container-fluid">
        <div class="box-typical box-typical-padding">
            <h6 class="address-heading"><span class="fa fa-list"></span> Summary</h6>

    <?php  echo $this->render('_search', ['model' => $searchModel, 'result_regions' => $result_regions, 'result_projects' => $result_projects]) ?>
        </div>

<div class="container-fluid">
    <?php if(isset($result) && !empty($result)){ ?>
    <header class="section-header">
    </header>
        <section class="card mb-3">
            <header class="card-header card-header-lg color-blue">
                        Applications Info
            </header>
            <div class="profile-info-item" style="margin-top: 15px;">
                <div class="row">
                    <div class="col-md-6">
                            <b>Total Applications :</b> <?=$result[0]['appCount']?>
                        <hr>
                    </div>
                   <!-- <div class="col-md-6">
                        <h4>

                        </h4>

                    </div>-->
                    <div class="col-md-6">

                            <b>Total Applications Tranche Two:</b> <?=$result[1]['counttranchtwo']?>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Pending Application :</b> <?= $result[0]['PendingApplications']+$result[0]['ApprovedApplicationsNoGroup']?>
                        </p>
                    </div>
                   <!-- <div class="col-md-6">
                        <h4>

                        </h4>

                    </div>-->
                    <div class="col-md-6">
                        <p>
                            <b>* Pending Application :</b> <?=0?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Approved Application :</b> <?=$result[0]['ApprovedApplications']-$result[0]['DisbursedApplications'] ?>
                        </p>
                    </div>
                   <!-- <div class="col-md-4">
                        <p>
                        </p>
                    </div>-->
                    <div class="col-md-6">
                        <p>
                            <b>* Approved Application :</b> <?= $result[1]['apptranchapproved']?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Rejected Application :</b> <?= $result[0]['RejectedApplications']?>
                        </p>
                    </div>
                   <!-- <div class="col-md-6">
                        <p>

                        </p>

                    </div>-->
                    <div class="col-md-6">
                        <p>
                            <b>* Rejected Application :</b> <?=0?>
                        </p>

                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b> * Disbursed Applications :</b>  <?= $result[0]['DisbursedApplications']?>
                        </p>
                    </div>
                    <!--<div class="col-md-6">
                        <p>

                        </p>

                    </div>-->
                    <div class="col-md-6">
                        <p>
                            <b> * Disbursed Applications :</b> <?= $result[1]['apptranchDisbursed'] ?>
                        </p>
                    </div>
                </div>
        </section>

    <section class="card mb-3">
        <header class="card-header card-header-lg color-blue">
            Bank Accounts Verification Info
        </header>
        <div class="profile-info-item" style="margin-top: 15px;">
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <b>* Total Bank Accounts</b> <?=$result[0]['verified']+$result[0]['bankaccountUnverified']?>
                    </p>
                    <hr>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <b>* Verified Bank Accounts :</b> <?=$result[0]['verified']?>
                    </p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <p>
                        <b>* Verified Bank Accounts </b> (FAC Completed) : <?= $result[0]['verifiedFACcompleted'] ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <b>* Unverified Bank Accounts :</b> <?= $result[0]['bankaccountUnverified'] ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <!-- Unverified Bank Account(ABL) :--><?/*= $result[2]['bank_name'] */?>
                        <b> * Unverified Bank Accounts(HBL) :  </b><?= $result[0]['hblUnverified'] ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <!-- Unverified Bank Account(HBL) : --><?/*= $result[3]['bank_name'] */?>
                        <b> * Unverified Bank Accounts(ABL) :</b> <?= $result[0]['ablUnverified'] ?>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <p>
                        <!-- Unverified Bank Account(HBL) : --><?/*= $result[3]['bank_name'] */?>
                        <b>* Unverified Bank Accounts </b> (FAC Completed) : <?= $result[0]['UnverifiedaccountsFACcompleted'] ?>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="card mb-3">
            <header class="card-header card-header-lg color-blue">
             Visits Info
            </header>
            <div class="profile-info-item" style="margin-top: 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b> Tranche One</b>
                        </p>
                        <hr>
                    </div>


                    <div class="col-md-6">
                        <p>
                            <b> Tranche Two </b>
                       </p>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Total Visits Assigned :</b>  <?=$result[0]['ApprovedApplications']?>
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p>
                            <b>* Total Visits Assigned:</b> <?=$result[1]['counttranchtwo']?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Pending Visits :</b> <?= $result[0]['pendingvisits'] ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>* Pending Visits:</b> <?= $result[1]['pendingvisitsTranchtwo'] ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b> * Visited :</b> <?=$result[0]['visitedCount']?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>* Visited:</b> <?= $result[1]['tranchtwoVisited'] ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <p>
                            <b> * Rejected Visited :</b> <?=$result[0]['rejectedvisitedCount']?>
                        </p>
                    </div>
                </div>

        </section>

        <section class="card mb-3">
            <header class="card-header card-header-lg color-blue">
                FAC Info
            </header>
            <div class="profile-info-item" style="margin-top: 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b> Tranche One </b>
                        </p><hr>
                    </div>
                    <!--<div class="col-md-4">
                        <p>

                        </p>
                    </div>-->
                    <div class="col-md-4">
                        <p>
                            <b> Tranche Two </b>
                        </p><hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Pending :</b> <?=$result[0]['FACPending']?>
                        </p>
                    </div>
                   <!-- <div class="col-md-4">
                        <p>

                        </p>
                    </div>-->
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Pending  :</b> <?=$result[1]['FACPendingtranchtwo']?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved(Not Disbursed) :</b> <?= $result[0]['FACLoanDone']-$result[1]['Disb_disbursed'] ?>
                            <!--  -$result[1]['FACrejected']-->
                         </p>
                     </div>
                    <!-- <div class="col-md-4">
                         <p>
                         </p>
                     </div>-->
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved(Not Disbursed) :</b> <?= $result[1]['FACApprovedNotDisbTranchtwo'] ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved Amount(Not Disbursed) :</b> <?= number_format($result[1]['FACApprovedNotDisbAmount']) ?>
                        </p>
                    </div>
                   <!-- <div class="col-md-4">
                        <p>
                        </p>
                    </div>-->
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved Amount(Not Disbursed) :</b> <?= number_format($result[1]['FACApprovedNotDisbAmountTranchtwo']) ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved(Disbursed) :</b> <?= $result[1]['Disb_disbursed'] ?>
                        </p>
                    </div>
                   <!-- <div class="col-md-4">
                        <p>
                        </p>
                    </div>-->
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved (Disbursed) :</b><?= $result[1]['Disb_disbursedtranchtwo'] ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved Amount(Disbursed) :</b> <?= number_format($result[1]['FACApprovedDisbAmount']) ?>
                        </p>
                    </div>
                   <!-- <div class="col-md-4">
                        <p>
                        </p>
                    </div>-->
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved Amount(Disbursed) :</b> <?= number_format($result[1]['FACApprovedDisbAmountTranchtwo']) ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved(Permanently Rejected) :</b> <?= ($result[1]['FACrejected']) ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>* FAC Approved(Permanently Rejected) :</b> <?=$result[1]['FACrejectedTranchtwo'] ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section class="card mb-3">
            <header class="card-header card-header-lg color-blue">
                Fund Request Info
            </header>
            <div class="profile-info-item" style="margin-top: 15px;">
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>Tranche One </b>
                        </p><hr>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b> Tranche Two</b>
                        </p><hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Fund Request In-Process:</b> <?=$result[1]['fundrequestInprocess']?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>* Fund Request In-Process :</b> <?=$result[1]['fundrequestInprocesstranchtwo']?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Fund Request Pending :</b> <?= $result[1]['fundrequestPending'] ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>* Fund Request Pending:</b> <?= $result[1]['fundrequestPendingtranchtwo'] ?>
                        </p>
                    </div>
                </div>
            </div>
        </section>
        <section class="card mb-3">
            <header class="card-header card-header-lg color-blue">
                Disbursment Info
            </header>
            <div class="profile-info-item" style="margin-top: 15px;">
                <div class="row">
                    <div class="col-md-6">
                            <b>Tranche One</b>
                        <hr>
                    </div>

                    <div class="col-md-6">
                            <b>Tranche Two </b>
                        <hr>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Pending For Disbursement :</b>  <?= $result[1]['fundrequestInprocess']?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>* Pending For Disbursement :</b>  <?= $result[1]['fundrequestInprocesstranchtwo']?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Pending For Publish :</b> <?= $result[1]['disb_publishing']?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>* Pending For Publish :</b> <?= $result[1]['disb_publishingtranchtwo']?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Pending For Funds Transfer :</b> <?=$result[1]['disb_fundtransfer'] ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p>
                            <b>* Pending For Funds Transfer :</b> <?=$result[1]['disb_fundtransfertranchtwo'] ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p>
                            <b>* Disbursed :</b> <?= $result[1]['Disb_disbursed'] ?>
                        </p>
                    </div>

                    <div class="col-md-6">
                        <p>
                            <b>* Disbursed :</b> <?= $result[1]['Disb_disbursedtranchtwo'] ?>
                        </p>
                    </div>
                </div>

        </section>
    <?php } ?>

   <!-- <section class="card mb-3">
        <div class="profile-info-item" style="margin-top: 15px;">
            <div class="row">
                <div class="col-md-4">
                    <p>
                        <b> No Record to Display.</b>
                    </p><hr>
                </div>
            </div>
    </section>-->
</div>



