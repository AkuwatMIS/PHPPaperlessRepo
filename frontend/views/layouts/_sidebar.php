<nav class="side-menu">

    <?php
    $permissions = Yii::$app->session->get('permissions');
    /*
     $auth = Yii::$app->authManager;
     $permissionslist = ($auth->getPermissionsByUser(Yii::$app->user->getId()));
     $permissions = [];
     foreach ($permissionslist as $key => $value) {
        $permissions[] = $key;
    }*/
    ?>
    <ul class="side-menu-list">
        <li class="grey with-sub">
            <a href="/">
                <i class="font-icon font-icon-dashboard"></i>
                <span class="lbl">Dashboard</span>
            </a>
        </li>
        <?php if (in_array('frontend_indexcompositeupdates', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/composite-updates/index">
                    <i class="glyphicon glyphicon-home"></i>
                    <span class="lbl">App/Loan Updates</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_dashboardhousingreports', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/housing-reports/dashboard">
                    <i class="glyphicon glyphicon-home"></i>
                    <span class="lbl">Housing Dashboard</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_search-emergencyloans', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/loans/search-emergency">
                    <i class="glyphicon glyphicon-home"></i>
                    <span class="lbl">Add Emergency Loan</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_housing-searchloans', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/loans/housing-search">
                    <i class="glyphicon glyphicon-list-alt"></i>
                    <span class="lbl">Search By CNIC</span>
                </a>
            </li>
        <?php } ?>


        <?php if (in_array('frontend_indexcnicexpiry', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/cnic-expiry/index">
                    <i class="glyphicon glyphicon-th-list"></i>
                    <span class="lbl">Expire CNIC</span>
                </a>
            </li>
        <?php } ?>

        <?php if (in_array('frontend_nacta-verificationsite', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/site/nacta-verification">
                    <i class="glyphicon glyphicon-th-list"></i>
                    <span class="lbl">Nacta Verification</span>
                </a>
            </li>
        <?php } ?>

        <?php if (in_array('frontend_indexmembers', $permissions) || in_array('frontend_createmembers', $permissions)) { ?>
            <li class="blue with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-user"></span>
                    <span class="lbl">Members</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_createmembers', $permissions)) { ?>
                        <li><a href="/members/create"><span class="lbl">Create Members</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexmembers', $permissions)) { ?>
                        <li><a href="/members/index"><span class="lbl">Members Listing</span></a></li>
                    <?php } ?>
                </ul>

            </li>
        <?php } ?>

        <?php if (in_array('frontend_indexapplications', $permissions) || in_array('frontend_createapplications', $permissions)) { ?>
            <li class="pink with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-edit"></span>
                    <span class="lbl">Applications</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_createapplications', $permissions)) { ?>
                        <li><a href="/applications/create"><span class="lbl">Create Application</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexapplications', $permissions)) { ?>
                        <li><a href="/applications/index"><span class="lbl">Applications Listing</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_rejected-pending-applicationsapplications', $permissions)) { ?>
                        <li><a href="/applications/rejected-pending-applications"><span class="lbl">Rejected/Pending Applications</span></a>
                        </li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_createappraisals', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/appraisals/create">
                    <i class="font-icon font-icon-build"></i>
                    <span class="lbl">Appraisals</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_approve-appapplications', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/applications/approve-app">
                    <i class="font-icon font-icon-build"></i>
                    <span class="lbl">Approve Applications</span>
                </a>
            </li>
        <?php } ?>

        <?php if (in_array('frontend_visits-reportsapplications', $permissions) || in_array('frontend_visit-imagesapplications', $permissions) || in_array('frontend_visits-shifted-approvalapplications', $permissions)) { ?>
            <li class="red with-sub">
                <span>
                    <span class="fa fa-book"></span>
                    <span class="lbl">Visits</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_visits-reportsapplications', $permissions)) { ?>
                        <li><a href="/applications/visits-reports"><span class="lbl">Visits Report</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_visits-shifted-approvalapplications', $permissions)) { ?>
                        <li><a href="/applications/visits-shifted-approval"><span class="lbl">Approve Visits</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_construction-completed-approvalapplications', $permissions)) { ?>
                        <li><a href="/applications/construction-completed-approval"><span class="lbl">Approve Construction</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_visit-imagesapplications', $permissions)) { ?>
                        <li><a href="/applications/visit-images"><span class="lbl">Visits Images</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_visit-imagesapplications', $permissions)) { ?>
                        <li><a href="/applications/flood-visit-images"><span class="lbl">Flood Visits Images</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_indexgroups', $permissions) || in_array('frontend_creategroups', $permissions)) { ?>
            <li class="blue with-sub">
                <span>
                    <span class="fa fa-users"></span>
                    <span class="lbl">Groups</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_creategroups', $permissions)) { ?>
                        <li><a href="/groups/create"><span class="lbl">Create Group</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexgroups', $permissions)) { ?>
                        <li><a href="/groups/index"><span class="lbl">Groups Listing</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

        <?php if (in_array('frontend_indexloans', $permissions) || in_array('frontend_lacloans', $permissions) || in_array('frontend_pledge-indexloans', $permissions)) { ?>
            <li class="red with-sub">
                <span>
                    <span class="fa fa-tasks"></span>
                    <span class="lbl">Loan</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_lacloans', $permissions)) { ?>
                        <li><a href="/loans/lac"><span class="lbl">LAC</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexloans', $permissions)) { ?>
                        <li><a href="/loans/index"><span class="lbl">Loans Listing</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_bm-approval-listloans', $permissions)) { ?>
                        <li><a href="/loans/bm-approval-list"><span class="lbl">BM Approval Listing</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_pledge-indexloans', $permissions)) { ?>
                        <li><a href="/loans/pledge-index"><span class="lbl">Pending For Pledge</span></a></li>
                    <?php } ?>
                </ul>

            </li>
        <?php } ?>
        <?php if (in_array('frontend_ready-for-fund-requestloans', $permissions)) { ?>
            <li class="white with-sub">
                <a href="/loans/ready-for-fund-request">
                    <i class="font-icon glyphicon glyphicon-copy"></i>
                    <span class="lbl">Ready For Fund Request</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_remove-fund-requestloans', $permissions)) { ?>
            <li class="red with-sub">
                <a href="/loans/remove-fund-request">
                    <i class="fa fa-recycle"></i>
                    <span class="lbl">Remove From Fund Request</span>
                </a>
            </li>
        <?php } ?>
        <?php /*if (in_array('frontend_indexblacklist', $permissions) || in_array('frontend_indexblacklistfiles', $permissions)) { */ ?><!--

            <li class="white with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-ban-circle"></span>
                    <span class="lbl">Blacklist</span>
                </span>
                <ul>
                    <?php /*if (in_array('frontend_indexblacklist', $permissions)) { */ ?>
                        <li><a href="/blacklist/index"><span class="lbl">Blacklist</span></a></li>
                    <?php /*} */ ?>
                    <?php /*if (in_array('frontend_indexblacklistfiles', $permissions)) { */ ?>
                        <li><a href="/blacklist-files/index"><span class="lbl">Blacklist Files</span></a></li>
                    <?php /*} */ ?>
                </ul>
            </li>
        --><?php /*} */ ?>
        <?php /*if (in_array('add-bulkrecoveries', $permissions) || in_array('add-bulk-with-cash-in-handrecoveries', $permissions)) { */ ?><!--
            <li class="gold with-sub">
                    <span>
                        <i class="font-icon glyphicon glyphicon-list-alt"></i>
                        <span class="lbl">Recoveries</span>
                    </span>
                <ul>
                    <?php /*if (in_array('add-bulkrecoveries', $permissions)) { */ ?>
                        <li><a href="/recoveries/add-bulk"><span class="lbl">Post Recoveries</span></a></li>
                    <?php /*} */ ?>
                    <?php /*if (in_array('add-bulk-with-cash-in-handrecoveries', $permissions)) { */ ?>
                        <li><a href="#"><span class="lbl">Post Recoveries(CIH)</span></a></li>
                    <?php /*} */ ?>
                </ul>
            </li>
        --><?php /*} */ ?>
        <?php if (in_array('frontend_indexfundrequests', $permissions) || in_array('frontend_createfundrequests', $permissions)) { ?>
            <li class="gold with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-copy"></span>
                    <span class="lbl">Fund Request</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_createfundrequests', $permissions)) { ?>
                        <li><a href="/fund-requests/create"><span class="lbl">Create Fund Request</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexfundrequests', $permissions)) { ?>
                        <li><a href="/fund-requests/index"><span class="lbl">Fund Request Listing</span></a></li>
                    <?php } ?>
                </ul>

            </li>
        <?php } ?>
        <?php if (in_array('frontend_cheque-printloans', $permissions)) { ?>
            <li class="white with-sub">
                <a href="/loans/cheque-print">
                    <i class="fa fa-print"></i>
                    <span class="lbl">Cheque Prints</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_indexdisbursements', $permissions)) { ?>
            <li class="brown with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-folder-close"></span>
                    <span class="lbl">Disbursements</span>
                </span>
                <ul>
                    <?php /*if (in_array('frontend_indexdisbursements', $permissions)) { */ ?><!--
                        <li><a href="/disbursements/create"><span class="lbl">Create Disbursement</span></a></li>
                    --><?php /*} */ ?>
                    <?php if (in_array('frontend_add-takafloans', $permissions)) { ?>
                        <li><a href="/loans/add-takaf"><span class="lbl">Add Takaful</span></a></li>
                    <?php } ?>

                    <?php if (in_array('frontend_attendance-disbdisbursements', $permissions)) { ?>
                        <li><a href="/disbursements/attendance-disb"><span class="lbl">Create Disbursement</span></a>
                        </li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexdisbursements', $permissions)) { ?>
                        <li><a href="/disbursements/index"><span class="lbl">Disbursement Listing</span></a></li>
                    <?php } ?>
                </ul>

            </li>
        <?php } ?>
        <?php if (in_array('frontend_indexprojectfunddetail', $permissions)) { ?>
            <li class="green with-sub">
        <span>
            <span class="font-icon glyphicon glyphicon-folder-close"></span>
            <span class="lbl">Funding Line</span>
        </span>
                <ul>
                    <?php if (in_array('frontend_indexprojectfunddetail', $permissions)) { ?>
                        <li><a href="/funds/index"><span class="lbl">Create Funding Line</span></a></li>
                        <li><a href="/disbursement-details/allocate-funds"><span class="lbl">Create Batch</span></a>
                        </li>

                        <li><a href="/project-fund-detail/index"><span class="lbl">Batches</span></a></li>

                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_publish-loandisbursements', $permissions) || in_array('frontend_publisheddisbursementdetails', $permissions) || in_array('frontend_publish-filesfilesaccounts', $permissions) || in_array('frontend_indexdisbursementdetails', $permissions)) { ?>
            <li class="green with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-folder-close"></span>
                    <span class="lbl">Publish</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_publish-loandisbursements', $permissions)) { ?>
                        <li><a href="/disbursements/publish-loan"><span class="lbl">Create</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

        <?php if (in_array('frontend_indexdisbursementrejected', $permissions) || in_array('frontend_publish-loandisbursements', $permissions) || in_array('frontend_publisheddisbursementdetails', $permissions) || in_array('frontend_publish-filesfilesaccounts', $permissions) || in_array('frontend_indexdisbursementdetails', $permissions)) { ?>
            <li class="green with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-folder-close"></span>
                    <span class="lbl">Fund Transfer</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_publisheddisbursementdetails', $permissions)) { ?>
                        <li><a href="/disbursement-details/published"><span class="lbl">Pending</span></a></li>
                    <?php } ?>

                    <?php if (in_array('frontend_publisheddisbursementdetails', $permissions)) { ?>
                        <li><a href="/disbursement-details/in-process"><span class="lbl">InProcess</span></a></li>
                    <?php } ?>

                    <?php if (in_array('frontend_publish-filesfilesaccounts', $permissions)) { ?>
                        <li><a href="/files-accounts/publish-files"><span class="lbl">Transferred</span></a></li>
                    <?php } ?>

                    <?php if (in_array('frontend_publish-filesfilesaccounts', $permissions)) { ?>
                        <li><a href="/files-accounts/publish-files-reject"><span class="lbl">Reject Publish File</span></a>
                        </li>
                    <?php } ?>

                    <?php if (in_array('frontend_pin-filesfilesaccounts', $permissions)) { ?>
                        <li><a href="/files-accounts/pin-files"><span class="lbl">Pin Verification</span></a></li>
                    <?php } ?>

                    <?php if (in_array('frontend_rejected-filesfilesaccounts', $permissions)) { ?>
                        <li><a href="/files-accounts/rejected-files"><span class="lbl">Rejected</span></a></li>
                    <?php } ?>

                    <?php if (in_array('frontend_indexdisbursementrejected', $permissions)) { ?>
                        <li><a href="/disbursement-rejected/index"><span class="lbl">Rejected loan</span></a></li>
                    <?php } ?>

                    <?php if (in_array('frontend_indextemporarydisbursementrejected', $permissions)) { ?>
                        <li><a href="/temporary-disbursement-rejected/index"><span class="lbl">Temporary Rejected loan</span></a></li>
                    <?php } ?>


                    <?php if (in_array('frontend_cheque-presentedfilesaccounts', $permissions)) { ?>
                        <li><a href="/files-accounts/cheque-presented"><span class="lbl">Cheque Presented</span></a>
                        </li>
                    <?php } ?>

                    <?php if (in_array('frontend_indexdisbursementdetails', $permissions)) { ?>
                        <li><a href="/disbursement-details/index"><span class="lbl">Status</span></a></li>
                    <?php } ?>

                    <?php if (in_array('frontend_indexdisbursementdetails', $permissions)) { ?>
                        <li><a href="/disbursement-details/index-pmyp"><span class="lbl">PMYP Status</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

        <?php if (in_array('frontend_indexrecoveries', $permissions) || in_array('frontend_add-bulkrecoveries', $permissions)) { ?>
            <li class="red with-sub">
                <span>
                        <span class="font-icon glyphicon glyphicon-list-alt"></span>
                        <span class="lbl">Recoveries</span>
                    </span>
                <ul>
                    <?php if (in_array('frontend_add-bulkrecoveries', $permissions)) { ?>

                        <li><a href="/recoveries/add-bulk"><span class="lbl">Post Recoveries</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexrecoveries', $permissions)) { ?>

                        <li><a href="/recoveries/index"><span class="lbl">Recoveries Listing</span></a></li>

                    <?php } ?>
                    <?php if (in_array('frontend_add-recovery-taxrecoveries', $permissions)) { ?>

                        <li><a href="/recoveries/add-recovery-tax"><span class="lbl">Post Recovery Tax</span></a></li>

                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_indexrecoveryfiles', $permissions) || in_array('frontend_indexrecoveryerrors', $permissions) || in_array('frontend_indexarchivereports', $permissions)) { ?>
            <li class="magenta with-sub">
                    <span>
                        <span class="glyphicon glyphicon-list-alt"></span>
                        <span class="lbl">Bank Recoveries</span>
                    </span>
                <ul>
                    <?php if (in_array('frontend_indexrecoveryfiles', $permissions)) { ?>
                        <li><a href="/recovery-files/index"><span class="lbl">Recovery Files</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexrecoveryerrors', $permissions)) { ?>
                        <li><a href="/recovery-errors/index"><span class="lbl">Recovery Errors</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexarchivereports', $permissions)) { ?>
                        <li><a href="/archive-reports/index"><span class="lbl">Bank Duelists</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>

        <?php /*if (in_array('add-bulk-mdpdonations', $permissions)) { */ ?><!--
            <li class="green with-sub">
                    <span>
                        <i class="font-icon glyphicon glyphicon-gift"></i>
                        <span class="lbl">Donations</span>
                    </span>
                <ul>
                    <li><a href="/donations/add-bulk-mdp"><span class="lbl">Post MDP</span></a></li>
                </ul>
            </li>
        --><?php /*} */ ?>
        <?php if (in_array('frontend_add-bulk-mdpdonations', $permissions) || in_array('frontend_indexdonations', $permissions)) { ?>
            <li class="green with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-gift"></span>
                    <span class="lbl">Donations</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_add-bulk-mdpdonations', $permissions)) { ?>
                        <li><a href="/donations/add-bulk-mdp"><span class="lbl">Post Donations</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexdonations', $permissions)) { ?>
                        <li><a href="/donations/index"><span class="lbl">Donations Listing</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_fixes-loansfixes', $permissions)) { ?>
            <li class="pink-red">
                <a href="/fixes/fixes-loans">
                    <i class="font-icon font-icon-zigzag"></i>
                    <span class="lbl">Fixes</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_update-loanfixes', $permissions)) { ?>
            <li class="pink-red">
                <a href="/fixes/update-loan">
                    <i class="glyphicon glyphicon-remove"></i>
                    <span class="lbl">Reject loan</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_createloanwriteoff', $permissions)) { ?>
            <li class="pink-red">
                <a href="/loan-write-off/index">
                    <i class="font-icon glyphicon glyphicon-copy"></i>
                    <span class="lbl">Write Off</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_showcashinhand', $permissions)) { ?>
            <li class="pink-red">
                <a href="#">
                    <i class="font-icon font-icon-zigzag"></i>
                    <span class="lbl">Cash in hand</span>
                </a>
            </li>
        <?php } ?>

        <?php if (in_array('frontend_users-detailusers', $permissions) || in_array('frontend_branches-detailbranches', $permissions)) { ?>
            <li class="blue with-sub">
                    <span>
                        <i class="glyphicon glyphicon-cog"></i>
                        <span class="lbl">Settings</span>
                    </span>
                <ul>
                    <?php if (in_array('frontend_branches-detailbranches', $permissions)) { ?>
                        <li><a href="#"><span class="lbl">Branches</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_users-detailusers', $permissions)) { ?>
                        <li><a href="#"><span class="lbl">Staff</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php /*if (in_array('frontend_indexprogressreports', $permissions)) { */ ?><!--
            <li class="blue with-sub">
                <a href="/progress-reports/index">
                    <i class="fa fa-line-chart"></i>
                    <span class="lbl">Progress Report</span>
                </a>
            </li>
        <?php /*} */ ?>
        <?php /*if (in_array('frontend_branch-wise-progressbranches', $permissions)) { */ ?>
            <li class="gold with-sub">
                <a href="/branches/branch-wise-progress">
                    <i class="fa fa-bar-chart"></i>
                    <span class="lbl">Progress Report(GIS)</span>
                </a>
            </li>
        --><?php /*} */ ?>
        <?php if (in_array('frontend_indexusers', $permissions)) { ?>
            <li class="gold with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-user"></span>
                    <span class="lbl">Users</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_createusers', $permissions)) { ?>
                        <li><a href="/user-management/users/index"><span class="lbl">Users Listing</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexusers', $permissions)) { ?>
                        <li><a href="/user-management/transfers/index"><span class="lbl">Users Transfer</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexusers', $permissions)) { ?>
                        <li><a href="/user-management/transfers/transferred-list"><span
                                        class="lbl">Transfered Users</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_vigaloans', $permissions) || in_array('frontend_indexvigaloans', $permissions)) { ?>
            <li class="orange-red with-sub">
                <span>
                    <span class="fa fa-archive"></span>
                    <span class="lbl">Viga</span>
                </span>
                <ul>
                    <?php if (in_array('frontend_vigaloans', $permissions)) { ?>
                        <li><a href="/loans/viga"><span class="lbl">Viga Loan</span></a></li>
                    <?php } ?>
                    <?php if (in_array('frontend_indexvigaloans', $permissions)) { ?>
                        <li><a href="/viga-loans/index"><span class="lbl">Viga Listing</span></a></li>
                    <?php } ?>
                </ul>
            </li>
        <?php } ?>
        <?php /*if (in_array('frontend_indexuserhierarchychangerequest', $permissions)) { */ ?><!--
            <li class="gold with-sub">
                <a href="/user-hierarchy-change-request/index">
                    <i class="fa fa-rocket"></i>
                    <span class="lbl">Users Transfers</span>
                </a>
            </li>
        --><?php /*} */ ?>
        <?php if (in_array('frontend_indexbranches', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/branches/index">
                    <i class="font-icon glyphicon glyphicon-link"></i>
                    <span class="lbl">Branches</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_staffusers', $permissions)) { ?>
            <li class="blue with-sub">
                <a href="/users/staff">
                    <i class="font-icon glyphicon glyphicon-user"></i>
                    <span class="lbl">Staff</span>
                </a>
            </li>
        <?php } ?>
        <!-- <?php /*if (in_array('frontend_indexdynamicreports', $permissions)) { */ ?>
            <li class="gold with-sub">
                <a href="/dynamic-reports/index">
                    <i class="fa fa-rocket"></i>
                    <span class="lbl">Dynamic Reports</span>
                </a>
            </li>
        <?php /*} */ ?>
        <?php /*if (in_array('frontend_approve-reportsdynamicreports', $permissions)) { */ ?>
            <li class="red with-sub">
                <a href="/dynamic-reports/approve-reports">
                    <i class="fa fa-rocket"></i>
                    <span class="lbl">Approve Reports</span>
                </a>
            </li>
        --><?php /*} */ ?>
        <?php if (in_array('frontend_indexprojects', $permissions)) { ?>
            <li class="gold with-sub">
                <a href="/projects/index">
                    <i class="fa fa-rocket"></i>
                    <span class="lbl">Project Info</span>
                </a>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_indextemplates', $permissions)) { ?>
            <li class="gold with-sub">
                <a href="/templates/index">
                    <i class="fa fa-print"></i>
                    <span class="lbl">Templates</span>
                </a>
            </li>
        <?php } ?>
        <li class="orange-red with-sub">
        <li class="gold with-sub">
            <a href="/nadra-verisys/completed">
                <i class="fa fa-print"></i>
                <span class="lbl">Verified</span>
            </a>
        </li>

        <li class="orange-red with-sub">

        <li class="gold with-sub">
            <a href="/kamyab-pakistan-loans/rejected-nic-list">
                <i class="fa fa-print"></i>
                <span class="lbl">Nadra Rejected List</span>
            </a>
        </li>

        <?php if (in_array('frontend_indexkamyabpakistanloans', $permissions)) { ?>
            <li class="orange-red with-sub">
                <span>
                    <span class="fa fa-archive"></span>
                    <span class="lbl">NADRA VERISYS</span>
                </span>
                <ul>

                    <li class="gold with-sub">
                        <a href="/kamyab-pakistan-loans/index">
                            <i class="fa fa-print"></i>
                            <span class="lbl" style="margin: 20px;">Nadra Verisys</span>
                        </a>
                    </li>
                    <li class="gold with-sub">
                        <a href="/kamyab-pakistan-loans/rejected-submit-nic-list">
                            <i class="fa fa-print"></i>
                            <span class="lbl" style="margin: 20px;">Re Submitted</span>
                        </a>
                    </li>
                    <li class="gold with-sub">
                        <a href="/member-cnic-status/index">
                            <i class="fa fa-print"></i>
                            <span class="lbl" style="margin: 20px;">Nadra Search</span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } ?>

        <?php if (in_array('frontend_indexapplicationdetails', $permissions)) { ?>
            <li class="orange-red with-sub">
                 <span>
                    <span class="fa fa-archive"></span>
                    <span class="lbl">Kpp PMT</span>
                </span>
                <ul>
                    <li class="gold with-sub">
                        <a href="/application-details/index">
                            <i class="fa fa-print"></i>
                            <span class="lbl" style="margin: 20px;">PMT List</span>
                        </a>
                    </li>
                    <li class="gold with-sub">
                        <a href="/application-details/export-pmt-index">
                            <i class="fa fa-print"></i>
                            <span class="lbl" style="margin: 20px;">Export Data</span>
                        </a>
                    </li>

                    <li class="gold with-sub">
                        <a href="/application-details/import-pmt-index">
                            <i class="fa fa-print"></i>
                            <span class="lbl" style="margin: 20px;">Upload Data</span>
                        </a>
                    </li>
                </ul>
            </li>
        <?php } ?>
        <?php if (in_array('frontend_indexdisbursements', $permissions)) { ?>
        <li class="brown with-sub">
                <span>
                    <span class="font-icon glyphicon glyphicon-folder-close"></span>
                    <span class="lbl">Takaful</span>
                </span>
            <ul>
                <?php /*if (in_array('frontend_indexdisbursements', $permissions)) { */ ?><!--
                        <li><a href="/disbursements/create"><span class="lbl">Create Disbursement</span></a></li>
                    --><?php /*} */ ?>

                <?php if (in_array('frontend_annual-takafulloans', $permissions)) { ?>
                    <li><a href="/loans/annual-takaful"><span class="lbl">Annual Takaful</span></a></li>
                <?php } ?>
                <?php if (in_array('frontend_indextakafulpaid', $permissions)) { ?>
                    <li><a href="/takaful-paid/index"><span class="lbl">Takaful Paid</span></a></li>
                <?php } ?>
                <?php if (in_array('frontend_indextakafuldue', $permissions)) { ?>
                    <li><a href="/takaful-due/index"><span class="lbl">Takaful Due</span></a></li>
                <?php } ?>
                <?php } ?>
            </ul>

</nav><!--.side-menu-->