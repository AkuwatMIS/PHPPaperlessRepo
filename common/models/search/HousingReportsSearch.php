<?php


namespace common\models\search;


use common\models\Applications;
use yii\base\Model;

class HousingReportsSearch extends Applications
{
    public function rules()
    {
        return [
            [['region_id', 'area_id', 'branch_id','project_id'], 'integer'],
            //[['is_locked', 'grp_no', 'group_name', 'grp_type', 'status', 'reject_reason', 'created_at', 'updated_at'], 'safe'],
            [['project_id'], 'required'],
        ];
    }

    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function search($params)
    {
        //print_r($params);die();
       $this->load($params);
        $loan_cond = '';
        $application_cond = '';
        if (isset($params['HousingReportsSearch']['region_id']) && !empty($params['HousingReportsSearch']['region_id'])) {
            $region = $params['HousingReportsSearch']['region_id'];
            $application_cond .=' and applications.region_id= "'.$region.'"';
            $loan_cond .=' and loans.region_id= "'.$region.'"';
        }
        if(isset($params['HousingReportsSearch']['branch_id']) && !empty($params['HousingReportsSearch']['branch-id'])) {
            $branch = $params['HousingReportsSearch']['branch_id'];
            $application_cond .=' and applications.branch_id= "'.$branch.'"';
            $loan_cond .=' and loans.branch_id= "'.$branch.'"';
        }
        if(isset($params['HousingReportsSearch']['area_id']) && !empty($params['HousingReportsSearch']['area_id'])) {
            $area = $params['HousingReportsSearch']['area_id'];
            $application_cond .=' and applications.area_id= "'.$area.'"';
            $loan_cond .=' and loans.area_id= "'.$area.'"';
        }
        if(isset($params['HousingReportsSearch']['project_id']) && !empty($params['HousingReportsSearch']['project_id'])) {
            $project = $params['HousingReportsSearch']['project_id'];
            $application_cond .=' and applications.project_id= "'.$project.'"';
            $loan_cond .=' and loans.project_id= "'.$project.'"';
        }
        if(isset($params) && !empty($params)) {
            $query1 = 'select count(applications.id) as appCount,
                 (select count(applications.id) from applications where applications.status="pending"
                         and applications.deleted=0 ' . $application_cond . ') as PendingApplications,
                         
                 (select count(applications.id) from applications where applications.status="approved" 
                         and applications.group_id=0 and applications.deleted=0 ' . $application_cond . ') as ApprovedApplicationsNoGroup,
                         
                 (select count(applications.id) from applications where applications.status in ("approved","rejected") 
                         and applications.group_id>0 and applications.deleted=0 ' . $application_cond . ') as ApprovedApplications,
                         
                 (select count(applications.id) from applications where applications.status="rejected" 
                         and applications.deleted=0  ' . $application_cond . ' ) as RejectedApplications,
                         
                 (select count(loans.id) from loans where loans.date_disbursed >0 and loans.status IN( "collected","loan completed") 
                         and disbursed_amount>0 and loans.deleted=0 ' . $loan_cond . ' ) as DisbursedApplications,
                         
                 (select count(distinct applications.id) from applications inner join visits on applications.id=visits.parent_id /*LEFT JOIN loans on applications.id=loans.application_id*/
                         where applications.status = "approved"  and applications.deleted=0 and visits.deleted=0 
                         and applications.group_id>0 /*and loans.application_id is null*/ ' . $application_cond . ' ) as visitedCount,
                  (select count(distinct applications.id) from applications inner join visits on applications.id=visits.parent_id /*LEFT JOIN loans on applications.id=loans.application_id*/
                         where applications.status = "rejected" and applications.deleted=0 and visits.deleted=0 
                         and applications.group_id>0 /*and loans.application_id is null*/ ' . $application_cond . ' ) as rejectedvisitedCount,
                                
                 (select count(applications.id) from applications left JOIN visits on applications.id=visits.parent_id 
                         where applications.deleted=0 and applications.status="approved" and visits.parent_id is null 
                         and applications.group_id>0 /*and loans.application_id is null*/ ' . $application_cond . ') as pendingvisits,
                         
                 /*(SELECT count(applications.id) from applications where applications.status="approved" and applications.group_id = 0 and applications.deleted=0 ' . $application_cond . ') as groupsnotFormed,
                 (SELECT count(applications.id) from applications where applications.status="approved" and applications.group_id > 0 and applications.deleted=0 ' . $application_cond . ') as groupsformed,*/
                 (select count(applications.id) from applications inner JOIN members on members.id=applications.member_id 
                         inner JOIN members_account on members.id=members_account.member_id  where members_account.is_current=1 
                         and applications.deleted = 0 and applications.group_id>0 and applications.status ="approved" 
                         and members_account.status=1 ' . $application_cond . ' ) as verified,
                         
                 (select count(applications.id) from applications inner JOIN members on members.id=applications.member_id 
                        inner JOIN members_account on members.id=members_account.member_id
                        INNER JOIN loans on loans.application_id=applications.id 
                        where members_account.is_current=1 and applications.status ="approved"  
                        and members_account.is_current=1 and applications.deleted = 0 
                        and applications.group_id>0 and members_account.status=1 ' . $application_cond . ' ) as verifiedFACcompleted,
                         
                 (select count(applications.id) from applications inner JOIN members on members.id=applications.member_id 
                         inner JOIN members_account on members.id=members_account.member_id where members_account.is_current=1 
                         and applications.deleted = 0 and applications.group_id>0  and applications.status ="approved" and members_account.bank_name="HBL" 
                         and members_account.status=0 ' . $application_cond . ' ) as hblUnverified,
                         
                 (select count(applications.id) from applications inner JOIN members on members.id=applications.member_id 
                         inner JOIN members_account on members.id=members_account.member_id  where members_account.is_current=1 
                         and applications.deleted = 0 and applications.group_id>0  and applications.status ="approved"  and members_account.bank_name="ABL" 
                         and members_account.status=0 ' . $application_cond . ' ) as ablUnverified,
                         
                 (select count(applications.id) from applications inner JOIN members on members.id=applications.member_id 
                         inner JOIN members_account on members.id=members_account.member_id where members_account.is_current=1 
                         and members_account.status=0 and applications.group_id>0  and applications.status ="approved"  
                         and applications.deleted = 0 ' . $application_cond . ' )  as bankaccountUnverified ,
                         
                 (select count(applications.id) from applications inner JOIN members on members.id=applications.member_id 
                         inner JOIN members_account on members.id=members_account.member_id INNER JOIN loans on loans.application_id=applications.id 
                         where members_account.is_current=1 and applications.status ="approved"  and applications.group_id>0  and members_account.status=0 
                         and applications.deleted = 0 ' . $application_cond . ' )  as UnverifiedaccountsFACcompleted ,
                         
                 (SELECT count( applications.id) from applications LEFT JOIN loans on applications.id=loans.application_id 
                         where applications.id in (SELECT parent_id from visits where parent_id = applications.id and visits.deleted=0 ) 
                         and applications.status="approved" and applications.group_id>0 and loans.application_id is null ' . $application_cond . ') as FACPending,
                                 
                 (SELECT count(applications.id) from applications inner join loans on loans.application_id=applications.id 
                         where applications.status="approved" and applications.group_id>0 and loans.deleted=0 ' . $application_cond . ') as FACLoanDone
                         
                 from applications where applications.deleted=0 ' . $application_cond . '
        ';


            $query3 = 'select count(loans.id)  as counttranchtwo,

                  (select count(loans.id) from loans inner JOIN loan_tranches on loan_tranches.loan_id=loans.id 
                          where loan_tranches.tranch_no=2 and loan_tranches.status >=1 and loan_tranches.status!=6 ' . $loan_cond . ') as apptranchapproved,
                          
                  (select count(loans.id) from loans inner JOIN loan_tranches on loan_tranches.loan_id=loans.id 
                          where loan_tranches.tranch_no=2 and loan_tranches.status = 6 
                          and loan_tranches.date_disbursed>0 ' . $loan_cond . ') as apptranchDisbursed,
                          
                  (select count(loans.id) from loans inner JOIN loan_tranches on loan_tranches.loan_id=loans.id 
                          INNER JOIN loan_tranches_logs on loan_tranches_logs.id=loan_tranches.id 
                          where loan_tranches.tranch_no=2 and loan_tranches.status >=2 
                          and application_id in (SELECT parent_id from visits where visits.created_at>=loan_tranches_logs.stamp) 
                          and loan_tranches_logs.new_value=1 and loan_tranches_logs.field="status"  ' . $loan_cond . ') as tranchtwoVisited,
                  
                  (select count(loans.id) from loans inner JOIN loan_tranches on loan_tranches.loan_id=loans.id
                    INNER JOIN loan_tranches_logs on loan_tranches_logs.id=loan_tranches.id     
                    where loan_tranches.tranch_no=2 and loan_tranches.status in(1,2) and application_id not in (SELECT parent_id from visits where visits.created_at>=loan_tranches_logs.stamp) and loan_tranches_logs.new_value=1 and loan_tranches_logs.field="status" ' . $loan_cond . ') as pendingvisitsTranchtwo,

                   (SELECT count(loans.id) from loans  where loans.status="rejected" and loans.deleted=0 ' . $loan_cond . ') as FACrejected,
                   
                   (SELECT count(loans.id)  from loans inner join loan_tranches on loans.id=loan_tranches.loan_id  
                           where loan_tranches.status=9 and loan_tranches.tranch_no=2 and loans.deleted=0 ' . $loan_cond . ') as FACrejectedTranchtwo,
                           
                   (SELECT count(loans.id) from loans  Inner JOIN loan_tranches on loans.id=loan_tranches.loan_id 
                           where loan_tranches.tranch_no=2 and loan_tranches.status=2 and loans.deleted=0 ' . $loan_cond . ') as FACPendingtranchtwo,
                           
                   (SELECT count(loans.id) from loans Inner JOIN loan_tranches on loans.id=loan_tranches.loan_id where loan_tranches.tranch_no=1 
                           and loan_tranches.date_disbursed=0 and loans.deleted=0 ' . $loan_cond . ') as FACApprovedNotDisb,
                           
                   (SELECT sum(loan_tranches.tranch_amount) from loans Inner JOIN loan_tranches on loans.id=loan_tranches.loan_id 
                           where loan_tranches.tranch_no=1 and loan_tranches.date_disbursed=0 and loans.date_disbursed=0 
                           and loans.deleted=0 ' . $loan_cond . ') as FACApprovedNotDisbAmount,
                           
                   (SELECT sum(loan_tranches.tranch_amount) from loans Inner JOIN loan_tranches on loans.id=loan_tranches.loan_id
                           where loan_tranches.tranch_no=1 and loan_tranches.status=6 and loan_tranches.date_disbursed>0 
                           and loans.date_disbursed>0 and loans.deleted=0 ' . $loan_cond . ') as FACApprovedDisbAmount,
                           
                   (SELECT count(loans.id) from loans  Inner JOIN loan_tranches on loans.id=loan_tranches.loan_id 
                           where loan_tranches.date_disbursed>0 and loan_tranches.status>=3 and loan_tranches.status not in(6,9) 
                           and loan_tranches.tranch_no=2 and loans.deleted=0 ' . $loan_cond . ') as FACApprovedNotDisbTranchtwo,
                           
                   (SELECT sum(loan_tranches.tranch_amount) from loans  Inner JOIN loan_tranches on loans.id=loan_tranches.loan_id 
                           where loan_tranches.date_disbursed=0 and loan_tranches.status>=3 and loan_tranches.status not in(6,9) 
                           and loan_tranches.tranch_no=2 and loans.deleted=0 ' . $loan_cond . ') as FACApprovedNotDisbAmountTranchtwo,
                           
                   (SELECT sum(loan_tranches.tranch_amount) from loans  Inner JOIN loan_tranches on loans.id=loan_tranches.loan_id 
                           where loan_tranches.date_disbursed>0 and  loan_tranches.status=6 and loan_tranches.tranch_no=2 
                           and loans.deleted=0 ' . $loan_cond . ') as FACApprovedDisbAmountTranchtwo,
                
                  (select count(loan_tranches.id) from loan_tranches INNER JOIN loans on loans.id=loan_tranches.loan_id 
                          INNER JOIN fund_requests on loan_tranches.fund_request_id=fund_requests.id where loan_tranches.status=4 
                          and loan_tranches.fund_request_id>0 and loan_tranches.tranch_no=1 and loan_tranches.date_disbursed <=0 
                          and fund_requests.status="processed" ' . $loan_cond . ') as fundrequestInprocess,
                          
                  (select count(loan_tranches.id) from loan_tranches INNER JOIN loans on loans.id=loan_tranches.loan_id  
                          INNER JOIN fund_requests on loan_tranches.fund_request_id=fund_requests.id where loan_tranches.status=4 
                          and loan_tranches.tranch_no=2 and loan_tranches.fund_request_id>0 and loan_tranches.date_disbursed <=0 
                          and fund_requests.status="processed" ' . $loan_cond . ') as fundrequestInprocesstranchtwo,
                          
                  (select count(loan_tranches.id) from loan_tranches inner join loans on loans.id= loan_tranches.loan_id 
                          where loans.deleted = 0 and loans.status != "rejected" and loan_tranches.status=4 and loan_tranches.tranch_no=1 
                          and loan_tranches.fund_request_id=0 ' . $loan_cond . ') as fundrequestPending,
                          
                  (select count(loan_tranches.id) from loan_tranches inner join loans on loans.id= loan_tranches.loan_id 
                          where loans.deleted = 0 and loans.status != "rejected" and loan_tranches.status=4 and loan_tranches.tranch_no=2 
                          and loan_tranches.fund_request_id=0 ' . $loan_cond . ') as fundrequestPendingtranchtwo,
                          
                  (SELECT count(loans.id) from loans INNER JOIN loan_tranches on loans.id=loan_tranches.loan_id 
                  INNER JOIN disbursement_details on disbursement_details.tranche_id=loan_tranches.id where loans.status="collected" 
                  and loan_tranches.status=6 and loan_tranches.tranch_no=1 and loan_tranches.date_disbursed>0 
                  and disbursement_details.status=3  and loans.deleted=0  ' . $loan_cond . ') as Disb_disbursed,
                  
                  (SELECT count(loans.id) from loans INNER JOIN loan_tranches on loans.id=loan_tranches.loan_id 
                          INNER JOIN disbursement_details on disbursement_details.tranche_id=loan_tranches.id 
                          where loans.status="collected" and loan_tranches.status=6 and loan_tranches.tranch_no=2 
                          and loan_tranches.date_disbursed>0 and disbursement_details.status=3  and loans.deleted=0 ' . $loan_cond . ') as Disb_disbursedtranchtwo,
                          
                  (SELECT count(disbursement_details.tranche_id) from disbursement_details 
                  INNER JOIN loan_tranches on disbursement_details.tranche_id=loan_tranches.id 
                  INNER JOIN loans on loans.id=loan_tranches.loan_id  where disbursement_details.status=0 
                  and loan_tranches.tranch_no=2  and loans.deleted=0 and loans.status != "rejected" ' . $loan_cond . ') as disb_fundtransfertranchtwo,
                  
                  (SELECT count(disbursement_details.tranche_id) from disbursement_details 
                  INNER JOIN loan_tranches on disbursement_details.tranche_id=loan_tranches.id 
                  INNER JOIN loans on loans.id=loan_tranches.loan_id where disbursement_details.status not in (2,3) 
                  and loan_tranches.tranch_no=1  and loans.deleted=0 and loans.status != "rejected" ' . $loan_cond . ') as disb_fundtransfer,
                  
                  (SELECT count(loan_tranches.id) from loan_tranches  INNER JOIN loans on loans.id=loan_tranches.loan_id 
                          LEFT JOIN disbursement_details on loan_tranches.id=disbursement_details.tranche_id 
                          where loan_tranches.status=8 and loan_tranches.tranch_no=1 and disbursement_details.tranche_id is null 
                          and loans.deleted=0 and loans.status != "rejected" ' . $loan_cond . ' ) as disb_publishing,
                          
                  (SELECT count(loan_tranches.id) from loan_tranches  INNER JOIN loans on loans.id=loan_tranches.loan_id 
                          LEFT JOIN disbursement_details on loan_tranches.id=disbursement_details.tranche_id where loan_tranches.status=8 
                          and loan_tranches.tranch_no=2 and disbursement_details.tranche_id is null  and loans.deleted=0 
                          and loans.status != "rejected" ' . $loan_cond . ' ) as disb_publishingtranchtwo
                  /*(SELECT count(loan_tranches.id) from loan_tranches INNER JOIN fund_requests on loan_tranches.fund_request_id=fund_requests.id where loan_tranches.status=4 and loan_tranches.fund_request_id>0 and loan_tranches.tranch_no=1 and fund_requests.status="approved" ' . $loan_cond . ' ) as disb_disbursement,
                  (SELECT count(loan_tranches.id) from loan_tranches INNER JOIN fund_requests on loan_tranches.fund_request_id=fund_requests.id where loan_tranches.status=4 and loan_tranches.fund_request_id>0 and loan_tranches.tranch_no=2 and fund_requests.status="approved" ' . $loan_cond . ' ) as disb_disbursementtranchtwo
                 */ from loans inner JOIN loan_tranches on loan_tranches.loan_id=loans.id where loans.deleted=0 and loan_tranches.tranch_no=2 and loan_tranches.status>0 ' . $loan_cond . '
              ';

            $application = \Yii::$app->db->createCommand($query1)->queryAll();
            $loan = \Yii::$app->db->createCommand($query3)->queryAll();
            $result = array_merge($application, $loan);
            return $result;
        }
    }

}