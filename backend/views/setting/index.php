<?php

/* @var $this yii\web\View */

$this->title = 'Paperless Admin Portal';
?>

<div class="container">
    <div class="jumbotron">
        <h2 style="margin-top: 3%">Cron Tabs</h2>
        <p>Please click required button to run cron</p>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
        <a href="/setting/response-rejected" class="btn btn-info">Response Rejected</a><br/><br/>
        <a href="/setting/recoveries-post" class="btn btn-info">Recoveries Post</a><br/><br/>
        <a href="/setting/account-no-update" class="btn btn-info">Account No Update</a><br/><br/>
        <a href="/setting/accounts-report" class="btn btn-info">Generate Accounts Report</a><br/><br/>
        <a href="/setting/progress-report" class="btn btn-info">Generate Progress Report</a><br/><br/>
        <a href="/setting/aging-report" class="btn btn-info">Generate Aging Report</a><br/><br/>
        <a href="/setting/aging-account-report" class="btn btn-info">Account Aging Report</a><br/><br/>
        <a href="/setting/publish" class="btn btn-info">Publish Accounts tranche 8</a><br/><br/>
        <a href="/setting/publish-tranche" class="btn btn-info">Publish Accounts tranche 6</a><br/><br/>
        <a href="/setting/add-tranche" class="btn btn-info">Add New Tranche</a><br/><br/>
        <a href="/setting/team-update-index" class="btn btn-info">Update Team</a><br/><br/>
        <a href="/setting/beneficiary-update" class="btn btn-info">Beneficiary Update</a><br/><br/>
    </div>
    <div class="col-md-4">
        <a href="/setting/publish-account-update" class="btn btn-info">Publish Accounts Update</a><br/><br/>
        <a href="/setting/dynamic-report" class="btn btn-info">Generate Dynamic Report</a><br/><br/>
        <a href="/setting/update-progress-report" class="btn btn-info">Update Progress Report</a><br/><br/>
        <a href="/setting/add-monthly-progress-report" class="btn btn-info">Add Monthly Progress Report</a><br/><br/>
        <a href="/setting/add-daily-progress-report" class="btn btn-info">Add Daily Progress Report</a><br/><br/>
        <a href="/setting/update-receipt" class="btn btn-info">Recovery & Donation Receipt Update</a><br/><br/>
        <a href="/setting/reject-loan" class="btn btn-info">Reject Loan by type</a><br/><br/>
        <a href="/setting/add-activities" class="btn btn-info">Add Activities</a><br/><br/>
        <a href="/setting/member-docs" class="btn btn-info">Member Docs</a><br/><br/>
        <a href="/setting/due-loans" class="btn btn-info">Due Report</a><br/><br/>
        <a href="/setting/member-update" class="btn btn-info">Member Update</a>
    </div>

    <div class="col-md-4">
        <a href="/setting/awp" class="btn btn-info">Run Awp update</a><br><br>
        <a href="/setting/upload-index" class="btn btn-info">Ledger Regenerate</a><br><br>
        <a href="/setting/fixes-index" class="btn btn-info">Ledger Fixes</a><br><br>
        <a href="/setting/export-index" class="btn btn-info">Export Active Loans</a><br><br>
        <a href="/setting/disbursed-date" class="btn btn-info">Update Disbursement Date</a><br><br>
        <a href="/setting/update-loan" class="btn btn-info">Loans Update</a><br><br>
        <a href="/setting/update-project" class="btn btn-info">App & Loan Project Update</a><br><br>
        <a href="/setting/update-donation-recovery-date" class="btn btn-info">Donation Recovery Date Update</a><br><br>
        <a href="/setting/kpp-ledger-re-generate" class="btn btn-info">Kpp Ledger ReGenerate</a><br><br>
        <a href="/setting/cib-generate" class="btn btn-info">Cib generate</a><br><br>
        <a href="/setting/fixes-housing-index" class="btn btn-info">Housing Ledger and Fixes</a>
    </div>
</div>

<div class="body-content" style="margin-left: 18%">


</div>
