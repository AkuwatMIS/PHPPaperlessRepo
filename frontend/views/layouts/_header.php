<?php

use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php
$permissions = Yii::$app->session->get('permissions');
/*$auth = Yii::$app->authManager;
$permissionslist = ($auth->getPermissionsByUser(Yii::$app->user->getId()));
$permissions = [];
foreach ($permissionslist as $key => $value)
{
    $permissions[] = $key;
}*/
?>
<style>
    .site-header .site-header-collapsed .site-header-collapsed-in {
        margin-right: 130px;
        zoom: 1;
    }
</style>
<header class="site-header">
    <div class="container-fluid">
        <a href="/" class="site-logo">
            <?php echo Html::img('@web/images/logo.png', ['alt' => Yii::$app->name,'class'=>'hidden-md-down']); ?>
            <?php echo Html::img('@web/images/akhuwat-logo.png', ['alt' => Yii::$app->name,'class'=>'hidden-lg-down']); ?>
        </a>

        <button id="show-hide-sidebar-toggle" class="show-hide-sidebar">
            <span>toggle menu</span>
        </button>

        <button class="hamburger hamburger--htla">
            <span>toggle menu</span>
        </button>
        <div class="site-header-content">
            <div class="site-header-content-in">
                <div class="site-header-shown">

                    <?php
                    if (Yii::$app->user->isGuest) {

                    } else {
                    $user_image = (!empty(Yii::$app->user->identity->image)) ? (Yii::$app->user->identity->image) : 'noimage.png';
                    $pic_url = Url::to('@web/uploads/users/' . $user_image, true);
                    ?>

                        <div class="dropdown user-menu">
                            <button class="dropdown-toggle" id="dd-user-menu" type="button" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                <span class="font-icon glyphicon glyphicon-user"></span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dd-user-menu">
                                <?= Html::a("<span
                                            class=\"font-icon glyphicon glyphicon-user\"></span>Profile", ['/users/profile'],['class'=>'dropdown-item']) ?>
                                <div class="dropdown-divider"></div>
                                <?php
                                echo Html::beginForm(['/site/logout'], 'post');
                                echo Html::submitButton(
                                    "<i class='fa fa-sign-out pull-left'> Logout</i>",
                                    ['class' => 'btn btn-link logout','style'=>'width:100%;']
                                );
                                echo Html::endForm();
                                ?>
                                <!--<a class="dropdown-item" href="#"><span class="font-icon glyphicon glyphicon-log-out"></span>Logout</a>-->
                            </div>
                        </div>
                        <?php
                        }
                        ?>


                        <button type="button" class="burger-right">
                            <i class="font-icon-menu-addl"></i>
                        </button>
                </div><!--.site-header-shown-->

                <div class="mobile-menu-right-overlay"></div>
                <div class="site-header-collapsed">
                    <div class="site-header-collapsed-in">
                        <?php if (
                                in_array('frontend_createawp', $permissions)
                                || in_array('frontend_indexawptargetvsachievement', $permissions)
                                || in_array('frontend_indexawp', $permissions)
                                || in_array('frontend_awp-reportawp', $permissions)
                                || in_array('frontend_awp-project-wiseawp', $permissions)
                                || in_array('frontend_awp-project-wise-budgetawp', $permissions)
                                || in_array('frontend_create-month-wiseawp', $permissions)
                                || in_array('frontend_indexprojects', $permissions)
                        ){?>
                            <div class="dropdown">
                                <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Annual Work Plan
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                    <?php if (in_array('frontend_indexawptargetvsachievement', $permissions)) { ?>
                                        <a class="dropdown-item" href="/awp-target-vs-achievement/index">AWP Dasboard</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_create-month-wiseawp', $permissions)) { ?>
                                        <a class="dropdown-item" href="/awp/create-month-wise">Create AWP Month Wise</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_indexawp', $permissions)) { ?>
                                        <a class="dropdown-item" href="/awp/index">Annual Work Plan</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_awp-reportawp', $permissions)) { ?>
                                          <a class="dropdown-item" href="/awp/awp-report">Detailed AWP</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_awp-project-wise-budgetawp', $permissions)) { ?>
                                        <a class="dropdown-item" href="/awp/awp-project-wise-budget">Project Wise AWP</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_create-yearlyawp', $permissions)) { ?>
                                        <a class="dropdown-item" href="/awp/create-yearly">Create AWP(Annualy)</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_indexprojects', $permissions)) { ?>
                                        <a class="dropdown-item" href="/projects/index">Projects</a>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (in_array('frontend_indexprogressreports', $permissions) || in_array('frontend_branch-wise-progressbranches', $permissions)
                            || in_array('frontend_duelistloans', $permissions) || in_array('frontend_overdue-listloans', $permissions)
                            || in_array('frontend_chequewisereportloans', $permissions) || in_array('frontend_family-member-reportloans', $permissions)
                            || in_array('rontend_duevsrecoveryloans', $permissions) || in_array('frontend_portfolioloans', $permissions)
                            || in_array('frontend_writeoffloans', $permissions) || in_array('frontend_referral-reportloans', $permissions)
                            || in_array('frontend_indexemergencyloans', $permissions) || in_array('frontend_emergency-loans-city-wiseemergencyloans', $permissions)
                            || in_array('frontend_indexapplications-cib', $permissions) || in_array('frontend_housingrecoveries', $permissions)
                            || in_array('frontend_creditloans', $permissions) || in_array('frontend_indexkamyabpakistanloans', $permissions)
                            ||in_array('frontend_indexsteps', $permissions)
                        ) { ?>
                            <div class="dropdown">
                                <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Reports
                                </button>
                                &nbsp;
                                <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                    <?php if ( in_array('frontend_indexprogressreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/progress-reports/index">Progress Report</a>
                                    <?php } ?>
                                    <?php if ( in_array('frontend_branch-wise-progressbranches', $permissions)) { ?>
                                        <a class="dropdown-item" href="/branches/branch-wise-progress">Progress Report(GIS)</a>
                                    <?php } ?>
                                    <?php if ( in_array('frontend_duelistloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/duelist">Due List</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_overdue-listloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/overdue-list">Overdue List</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_overdue-listloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/overdue-charges-list">Overdue Charges List</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_chequewisereportloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/chequewisereport">Cheque Wise report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_duevsrecoveryloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/duevsrecovery">Due Vs Recovery</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_portfolioloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/portfolio">Portfolio Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_family-member-reportloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/family-member-report">Family Member Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_mega-disbursement-reportloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/mega-disbursement-report">Mega Disbursement Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_referral-reportloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/referral-report">Referral Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_writeoffloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/writeoff">Write Off</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_indexemergencyloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/emergency-loans/index">Emergency Loans</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_emergency-loans-city-wiseemergencyloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/emergency-loans/emergency-loans-city-wise">Emergency Loans City Wise</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_indexapplicationscib', $permissions)) { ?>
                                        <a class="dropdown-item" href="/applications-cib/index">CIB Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_housingrecoveries', $permissions)) { ?>
                                        <a class="dropdown-item" href="/recoveries/housing">Housing Recovery Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_creditloans', $permissions)) { ?>
                                        <a class="dropdown-item" href="/loans/credit">Audit Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_indexsteps', $permissions)) { ?>
                                        <a class="dropdown-item" href="/steps/index">Verification Checklist</a>
                                    <?php } ?>
                                    <?php  ?>
                                        <a class="dropdown-item" href="/applications/own-housing-report">Apni Chatt</a>
                                    <?php  ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (in_array('frontend_disbursement-summaryaccountreports', $permissions)
                            || in_array('frontend_recovery-summaryaccountreports', $permissions)
                            || in_array('frontend_disbursementreportoverallaccountreports', $permissions)
                            || in_array('frontend_donation-summaryaccountreports', $permissions)
                            || in_array('frontend_recoveryreportoverallaccountreports', $permissions)
                            || in_array('frontend_application-reportaccountreports', $permissions)
                            || in_array('frontend_application-details-reportaccountreports', $permissions)
                            || in_array('frontend_disbursement-detail-responsedisbursementdetails', $permissions)
                            || in_array('frontend_fund-request-reportaccountreports', $permissions)) { ?>
                            <div class="dropdown">
                                <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Accounts Reports
                                </button>
                                &nbsp;
                                <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                    <?php if (in_array('frontend_disbursement-summaryaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/disbursement-summary">Disbursement Summary</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_recovery-summaryaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/recovery-summary">Recovery Summary</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_donation-summaryaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/donation-summary">Donation Summary</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_disbursementreportoverallaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/disbursementreportoverall">Disbursement Report Region Wise</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_recoveryreportoverallaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/recoveryreportoverall">Recovery Report Region Wise</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_donationreportoverallaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/donationreportoverall">Donation Report /Borrower</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_donationreportoverallcommulativeaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/donationreportoverallcommulative">Donation Report Commulative</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_application-reportaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/application-report">Application Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_application-details-reportaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/application-details-report">Application Disbursement Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_fund-request-reportaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/fund-request-report">Fund Request Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_disbursement-detail-responsedisbursementdetails', $permissions)) { ?>
                                        <a class="dropdown-item" href="/disbursement-details/disbursement-detail-response">Transaction Report</a>
                                    <?php } ?><?php if (in_array('frontend_indextakafulsummary', $permissions)) { ?>
                                        <a class="dropdown-item" href="/takaful-summary/index">Takaful Summary</a>
                                    <?php } ?>
                                    <!--<?php /*if (in_array('operations-summaryoperations', $permissions)) { */?>
                                        <a class="dropdown-item" href="/operations/operations-summary">Operation Summary</a>
                                  <?php /*} */?>-->
                                </div>
                            </div>
                        <?php } ?>
                        <?php if (in_array('frontend_index-aging-accountagingreports', $permissions) || in_array('frontend_indexagingreports', $permissions) || in_array('frontend_indexdynamicreports', $permissions) || in_array('frontend_approve-reportsdynamicreports', $permissions) || in_array('frontend_ dynamic-reportsaccountreports', $permissions)) {
                        ?>
                            <div class="dropdown">
                                <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Crons
                                </button>
                                &nbsp;
                                <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                    <?php if (in_array('frontend_indexagingreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/aging-reports/index">Aging Report</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_index-aging-accountagingreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/aging-reports/index-aging-account">Account Report</a>
                                    <?php } ?>
                                    <?php if(Yii::$app->user->identity->designation_id == 8){ ?>
                                        <?php if (in_array('frontend_accdynamicreports', $permissions)) { ?>
                                            <a class="dropdown-item" href="/dynamic-reports/acc">Dynamic Reports</a>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <?php if (in_array('frontend_indexdynamicreports', $permissions)) { ?>
                                            <a class="dropdown-item" href="/dynamic-reports/index">Dynamic Reports</a>
                                        <?php } ?>
                                    <?php  } ?>
                                    <?php if (in_array('frontend_approve-reportsdynamicreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/dynamic-reports/approve-reports">Approve Reports</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_dynamic-reportsaccountreports', $permissions)) { ?>
                                        <a class="dropdown-item" href="/account-reports/dynamic-reports">Monthly Progress</a>
                                    <?php } ?>

                                </div>
                            </div>
                        <?php
                       }?>

                        <?php if (in_array('frontend_indexblacklist', $permissions) || in_array('frontend_indexblacklistfiles', $permissions) || in_array('frontend_rejected-applicationsapplications', $permissions)) {
                            ?>
                            <div class="dropdown">
                                <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Blacklist
                                </button>
                                &nbsp;
                                <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                    <?php if (in_array('frontend_indexblacklist', $permissions)) { ?>
                                        <a class="dropdown-item" href="/blacklist/index">Blacklist</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_indexblacklistfiles', $permissions)) { ?>
                                        <a class="dropdown-item" href="/blacklist-files/index">Blacklist Files</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_rejected-applicationsapplications', $permissions)) { ?>
                                        <a class="dropdown-item" href="/applications/rejected-applications">Rejected Applications Report</a>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php
                        }?>

                        <?php if (in_array('frontend_bankaccountapplications', $permissions) || in_array('frontend_indexfilesaccounts', $permissions)) {
                            ?>
                            <div class="dropdown">
                                <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Bank Accounts
                                </button>
                                &nbsp;
                                <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                    <?php if (in_array('frontend_bankaccountapplications', $permissions)) { ?>
                                        <a class="dropdown-item" href="/applications/bankaccount">Bank Accounts Status</a>
                                    <?php } ?>
                                    <?php if (in_array('frontend_indexfilesaccounts', $permissions)) { ?>
                                        <a class="dropdown-item" href="/files-accounts/index">Bank Account Verification(File Upload)</a>
                                    <?php } ?>
                                   <?php if (in_array('frontend_rejected-filesfilesaccounts', $permissions)) { ?>
                                        <a class="dropdown-item" href="/files-accounts/rejected">Rejected Account (Files Upload)</a>
                                    <?php }?>
                                    <!--   <?php /*if (in_array('frontend_indexdisbursementdetails', $permissions)) { */?>
                                        <a class="dropdown-item" href="/disbursement-details/index">Bank Transactions</a>
                                    --><?php /*} */?>
                                </div>
                            </div>
                            <?php
                        }?>

                        <!--<?php /*if (in_array('frontend_branch-wise-progressbranches', $permissions)) { */?>
                            <div class="dropdown">
                                <button class="btn btn-rounded dropdown-toggle" id="dd-header-add" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Progress Report(GIS)
                                </button>
                                &nbsp;
                                <div class="dropdown-menu" aria-labelledby="dd-header-add">
                                    <?php /*if (in_array('frontend_disbursement-summaryloans', $permissions)) { */?>
                                        <a class="dropdown-item" href="/branches/branch-wise-progress">Progress Report(GIS)</a>
                                    <?php /*} */?>
                                </div>
                            </div>
                        <?php /*} */?>-->


                        <div class="site-header-search-container">
                            <form class="site-header-search closed">
                                <input type="text" placeholder="Search"/>
                                <button type="submit">
                                    <span class="font-icon-search"></span>
                                </button>
                                <div class="overlay"></div>
                            </form>
                        </div>
                    </div><!--.site-header-collapsed-in-->
                </div><!--.site-header-collapsed-->
            </div><!--site-header-content-in-->
        </div><!--.site-header-content-->
    </div><!--.container-fluid-->
</header><!--.site-header-->
