<?php

namespace console\controllers;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\StructureHelper;
use common\models\ApplicationDetails;
use common\models\Areas;
use common\models\Branches;
use common\models\Loans;
use common\models\Regions;
use common\models\StructureTransfer;
use yii\console\Controller;

class HousingDashboardController extends Controller
{


// * * * * * php /var/www/paperless_web/yii  housing-dashboard/housing-project-details
// * * * * * php /var/www/paperless_web/yii  housing-dashboard/housing-project-loans-push

    public function actionHousingProjectDetails()
    {
        $data_array = [
            'total_loans' => 0,
            'amount_disbursed' => 0,
            'no_of_applications' => 0,
            'no_of_applications_pending' => 0,
            'amount_approved' => 0,
            'province_loans' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0
            ],
            'prurpose_loans' => [
                42 => 0,
                43 => 0,
                44 => 0,
            ],
            'completion_percent' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
            ]
        ];


        // project_id=52 and
        $loan_query = 'select coalesce(sum(loans.disbursed_amount),0) as disbursed_amount,coalesce(sum(loans.loan_amount),0) as amount_approved ,count(loans.id) as loan_count,province_id from loans  inner join branches
              on branches.id=loans.branch_id where loans.project_id=52 and loans.status in("loan completed","collected") group by branches.province_id';
        $loan_data = \Yii::$app->db->createCommand($loan_query)->queryAll();
        foreach ($loan_data as $l_data) {
            $data_array['province_loans'][$l_data['province_id']] = $l_data['loan_count'];
            $data_array['total_loans'] += $l_data['loan_count'];
            $data_array['amount_disbursed'] += $l_data['disbursed_amount'];
            $data_array['amount_approved'] += $l_data['amount_approved'];
        }

        /////purpose-wise applications
        $loan_query_purpose = 'select count(loans.id) as loan_count,applications.activity_id as activity_id from loans  inner join applications 
              on applications.id=loans.application_id where loans.project_id=52 and loans.status in("loan completed","collected") group by applications.activity_id';
        $loan_data_purpose = \Yii::$app->db->createCommand($loan_query_purpose)->queryAll();
        foreach ($loan_data_purpose as $l_purpose_data) {
            $data_array['prurpose_loans'][$l_purpose_data['activity_id']] = $l_purpose_data['loan_count'];
        }

        ////pending/approved applications
        $app_query = 'select count(applications.id) as no_of_applications from applications  
              where applications.project_id=52 and applications.status in("pending","approved")';
        $app_data = \Yii::$app->db->createCommand($app_query)->queryAll();
        foreach ($app_data as $dt) {
            $data_array['no_of_applications'] += $dt['no_of_applications'];
            /*if($dt['status']=='pending'){
                $data_array['no_of_applications_pending']+=$dt['no_of_applications'];
            }*/
        }
        $data_array['no_of_applications'] = $data_array['no_of_applications'] - $data_array['total_loans'];

        /*$total_applications='select count(applications.id) as no_of_applications from applications
                 left join loans on loans.application_id=applications.id
                     where loans.application_id is null and applications.project_id=52
                     and applications.status in("pending","approved")';
        $total_applications = \Yii::$app->db->createCommand($total_applications)->queryAll();
        $data_array['no_of_applications_pending']=$total_applications[0]['no_of_applications'];*/
        $total_applications = 'select count(applications.id) as no_of_applications from applications
                     where applications.project_id=52
                     and applications.status in("pending") and applications.deleted=0';
        $total_applications = \Yii::$app->db->createCommand($total_applications)->queryAll();
        $data_array['no_of_applications_pending'] = $data_array['no_of_applications'] - $total_applications[0]['no_of_applications'];
        ////////////////////

        $completion_wise_query = 'select loans.id,(select percent from visits where parent_id=applications.id and parent_type="application" and visits.deleted=0 order by created_at desc limit 1) as percent from loans  
                 inner join applications on applications.id=loans.application_id 
              where loans.project_id=52 and loans.status in("loan completed","collected")';

        $completion_wise_query = \Yii::$app->db->createCommand($completion_wise_query)->queryAll();

        foreach ($completion_wise_query as $l) {
            if ($l['percent'] >= 0 && $l['percent'] <= 25) {
                $data_array['completion_percent'][1] = $data_array['completion_percent'][1] + 1;
            } elseif ($l['percent'] > 25 && $l['percent'] <= 50) {
                $data_array['completion_percent'][2] = $data_array['completion_percent'][2] + 1;
            } elseif ($l['percent'] > 50 && $l['percent'] <= 75) {
                $data_array['completion_percent'][3] = $data_array['completion_percent'][3] + 1;
            } elseif ($l['percent'] > 75 && $l['percent'] <= 100) {
                $data_array['completion_percent'][4] = $data_array['completion_percent'][4] + 1;
            } else {
                $data_array['completion_percent'][5] = $data_array['completion_percent'][5] + 1;
            }
        }

        ////////////////////
        ////completion-wise loans
        /*$completion_wise_query_1='select coalesce(count(loans.id),0) as no_of_loans from loans
                 inner join applications on applications.id=loans.application_id 
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=52 and loans.status in("loan completed","collected") and  visits.percent between 0 and 25';
        $completion_wise_query_1 = \Yii::$app->db->createCommand($completion_wise_query_1)->queryAll();


        $completion_wise_query_2='select coalesce(count(loans.id),0) as no_of_loans from loans  
                 inner join applications on applications.id=loans.application_id 
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=52 and loans.status in("loan completed","collected") and  visits.percent between 26 and 50';
        $completion_wise_query_2 = \Yii::$app->db->createCommand($completion_wise_query_2)->queryAll();


        $completion_wise_query_3='select coalesce(count(loans.id),0) as no_of_loans from loans  
                 inner join applications on applications.id=loans.application_id 
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=52 and loans.status in("loan completed","collected") and  visits.percent between 51 and 75';
        $completion_wise_query_3 = \Yii::$app->db->createCommand($completion_wise_query_3)->queryAll();

        $completion_wise_query_4='select coalesce(count(loans.id),0) as no_of_loans from loans  
                 inner join applications on applications.id=loans.application_id 
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=52 and loans.status in("loan completed","collected") and  visits.percent between 76 and 100';
        $completion_wise_query_4 = \Yii::$app->db->createCommand($completion_wise_query_4)->queryAll();


        $data_array['completion_percent'][1]= $completion_wise_query_1[0]['no_of_loans'];
        $data_array['completion_percent'][2]= $completion_wise_query_2[0]['no_of_loans'];
        $data_array['completion_percent'][3]= $completion_wise_query_3[0]['no_of_loans'];
        $data_array['completion_percent'][4]= $completion_wise_query_4[0]['no_of_loans'];*/

        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );

        print_r($data_array);
        $ch = curl_init('http://52.136.198.67/html/akhuwat_gis/dashboard/post_data_project_summary.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }

    public function actionHousingProjectLoansPush()
    {
        $loans_data = [];
        $applications = 'select m.id as id,
                        applications.id as application_id,
                        applications.req_amount as req_amount,
                        m.full_name as full_name,
                        m.parentage as parentage,
                        m.cnic as cnic,
                        m.parentage_type as parentage_type,
                        m.gender as gender,
                        m.dob as dob,
                        m.education as education,
                        m.education as education,
                        m.marital_status as marital_status,
                        m.family_member_name as family_member_name,
                        m.family_member_cnic as family_member_cnic,
                        m.religion as religion,
                        p.name as product_name,
                        a.name as activity_name,
                        applications.sub_activity as sub_activity,
                        applications.region_id as region_id,
                        applications.area_id as area_id,
                        applications.branch_id as branch_id,
                        applications.application_date as application_date,
                        applications.application_no as application_no,
                        b.city_id as city_id,
                        b.district_id as district_id,
                        b.province_id as province_id,
                        applications.id as application_id,
                (select coalesce(id,0) from visits where parent_id=applications.id and parent_type="application" 
                       and visits.deleted=0 order by created_at desc limit 1) as loan_visit_id,
                (select percent from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0 order by created_at desc limit 1) as completion_percent,
                (select coalesce(count(id),0) from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0) as visits_count,
                (select created_at from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0  order by created_at desc limit 1) as last_visit_date,
                (select address from members_address ma where ma.member_id=applications.member_id 
                               and address_type="business" and is_current=1 limit 1) as address ,
                (select phone from members_phone mp where mp.member_id=applications.member_id 
                               and phone_type="mobile" and is_current=1 limit 1) as phone,
                (select image_name from images i where i.parent_id=applications.member_id 
                               and parent_type="members" and image_type="profile_pic" limit 1) as profile_pic,
                (select longitude from visits v where v.parent_id=applications.id 
                               and parent_type="application" and v.longitude > 0 order by created_at desc limit 1) as longitude,
                (select v1.latitude from visits v1 where v1.parent_id=applications.id 
                               and v1.parent_type="application" and v1.latitude > 0 order by v1.created_at desc limit 1) as latitude 
                 from applications 
                 inner join loans l on l.application_id=applications.id 
                 inner join members m on m.id=applications.member_id
                 inner join branches b on b.id=applications.branch_id
                 inner join products p on p.id=applications.product_id
                 inner join activities a on a.id=applications.activity_id
                 where applications.status="approved" and l.status in ("collected","loan completed","rejected","not collected") 
              and applications.deleted=0 and l.project_id=52';
//        AND applications.id=4205458
        $applications = \Yii::$app->db->createCommand($applications)->queryAll();
        $i = 0;
        foreach ($applications as $app) {

            $shifted = ApplicationDetails::getShifted($app['application_id']);

            $loans_data[$i]['name'] = $app['full_name'];
            $loans_data[$i]['parentage'] = $app['parentage'];
            $loans_data[$i]['cnic'] = $app['cnic'];
            $loans_data[$i]['parentage_type'] = $app['parentage_type'];
            $loans_data[$i]['gender'] = $app['gender'];
            $loans_data[$i]['dob'] = date('Y-m-d', $app['dob']);
            $loans_data[$i]['education'] = $app['education'];
            $loans_data[$i]['marital_status'] = $app['marital_status'];
            $loans_data[$i]['family_member_name'] = $app['family_member_name'];
            $loans_data[$i]['family_member_cnic'] = $app['family_member_cnic'];
            $loans_data[$i]['religion'] = $app['religion'];
            $loans_data[$i]['project'] = 'Low Cost Housing Scheme';
            $loans_data[$i]['application_no'] = $app['application_no'];
            $loans_data[$i]['product'] = $app['product_name'];
            $loans_data[$i]['purpose'] = $app['activity_name'];
            $loans_data[$i]['sub_purpose'] = $app['sub_activity'];
            $loans_data[$i]['application_date'] = date('Y-m-d', $app['application_date']);
            $loans_data[$i]['region'] = $app['region_id'];
            $loans_data[$i]['area'] = $app['area_id'];
            $loans_data[$i]['branch'] = $app['branch_id'];
            $loans_data[$i]['city'] = $app['city_id'];
            $loans_data[$i]['district'] = $app['district_id'];
            $loans_data[$i]['address'] = $app['address'];
            $loans_data[$i]['mobile_no'] = $app['phone'];
            $loans_data[$i]['member_id'] = $app['id'];
            $loans_data[$i]['application_id'] = $app['application_id'];
            $loans_data[$i]['image_path'] = $app['profile_pic'];
            $loans_data[$i]['longitude'] = isset($app['longitude']) && !empty($app['longitude']) && $app['longitude'] != null ? $app['longitude'] : 0;
            $loans_data[$i]['latitude'] = isset($app['latitude']) && !empty($app['latitude']) && $app['latitude'] != null ? $app['latitude'] : 0;
            $loans_data[$i]['province'] = !empty($app['province_id']) ? $app['province_id'] : 0;
            $loans_data[$i]['completion_percent'] = !empty($app['completion_percent']) ? $app['completion_percent'] : 0;
            $loan = Loans::find()->where(['application_id' => $app['application_id'], 'deleted' => 0])->one();
            if (!empty($loan)) {
                $loans_data[$i]['loan_amount'] = $loan->loan_amount;
                $loans_data[$i]['disbursed_amount'] = $loan->disbursed_amount;
                $loans_data[$i]['sanction_no'] = $loan->sanction_no;
                $loans_data[$i]['status'] = $loan->status;
            } else {
                $loans_data[$i]['loan_amount'] = !empty($app['req_amount']) ? $app['req_amount'] : 0;
                $loans_data[$i]['disbursed_amount'] = 0;
                $loans_data[$i]['sanction_no'] = '';
                $loans_data[$i]['status'] = 'application';
            }
            $loans_data[$i]['visits_count'] = !empty($app['visits_count']) ? $app['visits_count'] : 0;
            $loans_data[$i]['last_visit_date'] = !empty($app['last_visit_date']) ? date('Y-m-d', $app['last_visit_date']) : '';
            if ($app['loan_visit_id'] != 0) {
                $loans_data[$i]['visit_images'] = ImageHelper::getVisitImages($app['loan_visit_id'], 1);
            } else {
                $loans_data[$i]['visit_images'] = [];
            }
            $loans_data[$i]['is_shifted'] = $shifted;
            $i++;
        }

        echo 'transport started';

        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );


        $ch = curl_init('http://52.136.198.67/html/akhuwat_gis/dashboard/post_data_project_details.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loans_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }
// //////////////////////////////////////////////////// New Housing Project Apni Chat apna ghar/////////////////////
// * * * * * php /var/www/paperless_web/yii  housing-dashboard/housing-project-details-apni-chhat-apna-ghar
// * * * * * php /var/www/paperless_web/yii  housing-dashboard/housing-project-loans-push-apni-chhat-apna-ghar
//

    // php yii housing-dashboard/housing-project-details-apni-chhat-apna-ghar

    public function actionHousingProjectDetailsApniChhatApnaGhar()
    {
        $data_array = [
            'total_loans' => 0,
            'amount_disbursed' => 0,
            'no_of_applications' => 0,
            'no_of_applications_pending' => 0,
            'amount_approved' => 0,
            'province_loans' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0
            ],
            'prurpose_loans' => [
                42 => 0,
                43 => 0,
                44 => 0,
            ],
            'completion_percent' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
            ]
        ];


        // project_id=132 and
        $loan_query = 'select coalesce(sum(loans.disbursed_amount),0) as disbursed_amount,coalesce(sum(loans.loan_amount),0) as amount_approved ,count(loans.id) as loan_count,province_id from loans  inner join branches
              on branches.id=loans.branch_id where loans.project_id=132 and loans.status in("loan completed","collected") group by branches.province_id';
        $loan_data = \Yii::$app->db->createCommand($loan_query)->queryAll();
        foreach ($loan_data as $l_data) {
            $data_array['province_loans'][$l_data['province_id']] = $l_data['loan_count'];
            $data_array['total_loans'] += $l_data['loan_count'];
            $data_array['amount_disbursed'] += $l_data['disbursed_amount'];
            $data_array['amount_approved'] += $l_data['amount_approved'];
        }

        /////purpose-wise applications
        $loan_query_purpose = 'select count(loans.id) as loan_count,applications.activity_id as activity_id from loans  inner join applications 
              on applications.id=loans.application_id where loans.project_id=132 and loans.status in("loan completed","collected") group by applications.activity_id';
        $loan_data_purpose = \Yii::$app->db->createCommand($loan_query_purpose)->queryAll();
        foreach ($loan_data_purpose as $l_purpose_data) {
            $data_array['prurpose_loans'][$l_purpose_data['activity_id']] = $l_purpose_data['loan_count'];
        }

        ////pending/approved applications
        $app_query = 'select count(applications.id) as no_of_applications from applications  
              where applications.project_id=132 and applications.status in("pending","approved")';
        $app_data = \Yii::$app->db->createCommand($app_query)->queryAll();
        foreach ($app_data as $dt) {
            $data_array['no_of_applications'] += $dt['no_of_applications'];
            /*if($dt['status']=='pending'){
                $data_array['no_of_applications_pending']+=$dt['no_of_applications'];
            }*/
        }
        $data_array['no_of_applications'] = $data_array['no_of_applications'] - $data_array['total_loans'];

        /*$total_applications='select count(applications.id) as no_of_applications from applications
                 left join loans on loans.application_id=applications.id
                     where loans.application_id is null and applications.project_id=132
                     and applications.status in("pending","approved")';
        $total_applications = \Yii::$app->db->createCommand($total_applications)->queryAll();
        $data_array['no_of_applications_pending']=$total_applications[0]['no_of_applications'];*/
        $total_applications = 'select count(applications.id) as no_of_applications from applications
                     where applications.project_id=132
                     and applications.status in("pending") and applications.deleted=0';
        $total_applications = \Yii::$app->db->createCommand($total_applications)->queryAll();
        $data_array['no_of_applications_pending'] = $data_array['no_of_applications'] - $total_applications[0]['no_of_applications'];
        ////////////////////

        $completion_wise_query = 'select loans.id,(select percent from visits where parent_id=applications.id and parent_type="application" and visits.deleted=0 order by created_at desc limit 1) as percent from loans  
                 inner join applications on applications.id=loans.application_id 
              where loans.project_id=132 and loans.status in("loan completed","collected")';

        $completion_wise_query = \Yii::$app->db->createCommand($completion_wise_query)->queryAll();

        foreach ($completion_wise_query as $l) {
            if ($l['percent'] >= 0 && $l['percent'] <= 25) {
                $data_array['completion_percent'][1] = $data_array['completion_percent'][1] + 1;
            } elseif ($l['percent'] > 25 && $l['percent'] <= 50) {
                $data_array['completion_percent'][2] = $data_array['completion_percent'][2] + 1;
            } elseif ($l['percent'] > 50 && $l['percent'] <= 75) {
                $data_array['completion_percent'][3] = $data_array['completion_percent'][3] + 1;
            } elseif ($l['percent'] > 75 && $l['percent'] <= 100) {
                $data_array['completion_percent'][4] = $data_array['completion_percent'][4] + 1;
            } else {
                $data_array['completion_percent'][5] = $data_array['completion_percent'][5] + 1;
            }
        }

        ////////////////////
        ////completion-wise loans
        /*$completion_wise_query_1='select coalesce(count(loans.id),0) as no_of_loans from loans
                 inner join applications on applications.id=loans.application_id 
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=132 and loans.status in("loan completed","collected") and  visits.percent between 0 and 25';
        $completion_wise_query_1 = \Yii::$app->db->createCommand($completion_wise_query_1)->queryAll();


        $completion_wise_query_2='select coalesce(count(loans.id),0) as no_of_loans from loans  
                 inner join applications on applications.id=loans.application_id 
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=132 and loans.status in("loan completed","collected") and  visits.percent between 26 and 50';
        $completion_wise_query_2 = \Yii::$app->db->createCommand($completion_wise_query_2)->queryAll();


        $completion_wise_query_3='select coalesce(count(loans.id),0) as no_of_loans from loans  
                 inner join applications on applications.id=loans.application_id 
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=132 and loans.status in("loan completed","collected") and  visits.percent between 51 and 75';
        $completion_wise_query_3 = \Yii::$app->db->createCommand($completion_wise_query_3)->queryAll();

        $completion_wise_query_4='select coalesce(count(loans.id),0) as no_of_loans from loans  
                 inner join applications on applications.id=loans.application_id 
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=132 and loans.status in("loan completed","collected") and  visits.percent between 76 and 100';
        $completion_wise_query_4 = \Yii::$app->db->createCommand($completion_wise_query_4)->queryAll();


        $data_array['completion_percent'][1]= $completion_wise_query_1[0]['no_of_loans'];
        $data_array['completion_percent'][2]= $completion_wise_query_2[0]['no_of_loans'];
        $data_array['completion_percent'][3]= $completion_wise_query_3[0]['no_of_loans'];
        $data_array['completion_percent'][4]= $completion_wise_query_4[0]['no_of_loans'];*/

        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );

        print_r($data_array);
        $ch = curl_init('http://20.174.13.174/post_data_project_summary.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }

    // php yii housing-dashboard/housing-project-loans-push-apni-chhat-apna-ghar

    public function actionHousingProjectLoansPushApniChhatApnaGhar()
    {
        $loans_data = [];

        $branches = Branches::find()->where(['status'=>1])->andWhere(['province_id'=>1])->select(['id'])->all();
        foreach ($branches as $branch){
            $branchId = $branch->id;

            $applications = "select m.id as id,
                        applications.id as application_id,
                        applications.req_amount as req_amount,
                        m.full_name as full_name,
                        m.parentage as parentage,
                        m.cnic as cnic,
                        m.parentage_type as parentage_type,
                        m.gender as gender,
                        m.dob as dob,
                        m.education as education,
                        m.education as education,
                        m.marital_status as marital_status,
                        m.family_member_name as family_member_name,
                        m.family_member_cnic as family_member_cnic,
                        m.religion as religion,
                        p.name as product_name,
                        a.name as activity_name,
                        applications.sub_activity as sub_activity,
                        applications.region_id as region_id,
                        applications.area_id as area_id,
                        applications.branch_id as branch_id,
                        applications.application_date as application_date,
                        applications.application_no as application_no,
                        b.city_id as city_id,
                        b.district_id as district_id,
                        b.province_id as province_id,
                        applications.id as application_id,
                        l.loan_amount,
                        l.disbursed_amount,
                        l.sanction_no,
                        l.status,
                        ad.is_shifted,
                (select coalesce(id,0) from visits where parent_id=applications.id and parent_type='application' 
                       and visits.deleted=0 order by created_at desc limit 1) as loan_visit_id,
                (select percent from visits where parent_id=applications.id and parent_type='application' 
                               and visits.deleted=0 order by created_at desc limit 1) as completion_percent,
                (select coalesce(count(id),0) from visits where parent_id=applications.id and parent_type='application' 
                               and visits.deleted=0) as visits_count,
                (select created_at from visits where parent_id=applications.id and parent_type='application'
                               and visits.deleted=0  order by created_at desc limit 1) as last_visit_date,
                (select address from members_address ma where ma.member_id=applications.member_id 
                               and address_type='business' and is_current=1 limit 1) as address ,
                (select phone from members_phone mp where mp.member_id=applications.member_id 
                               and phone_type='mobile' and is_current=1 limit 1) as phone,
                (select image_name from images i where i.parent_id=applications.member_id 
                               and parent_type='members' and image_type='profile_pic' limit 1) as profile_pic,
                (select longitude from visits v where v.parent_id=applications.id 
                               and parent_type='application' and v.longitude > 0 order by created_at desc limit 1) as longitude,
                (select v1.latitude from visits v1 where v1.parent_id=applications.id 
                               and v1.parent_type='application' and v1.latitude > 0 order by v1.created_at desc limit 1) as latitude 
                 from applications 
                 inner join loans l on l.application_id=applications.id 
                 inner join members m on m.id=applications.member_id
                 inner join branches b on b.id=applications.branch_id
                 inner join products p on p.id=applications.product_id
                 inner join activities a on a.id=applications.activity_id
                 left join application_details ad on ad.parent_id=applications.id and ad.parent_type='application'
                 where l.status in ('collected','loan completed')
              and applications.deleted=0 and applications.project_id=132 and applications.branch_id=$branchId";
//        applications.status in ("approved","pending")
//        and l.status in ("collected","loan completed","rejected","not collected")
//        AND applications.id=4205458
            $applications = \Yii::$app->db->createCommand($applications)->queryAll();
            $i = 0;
            foreach ($applications as $app) {

//            $shifted = ApplicationDetails::getShifted($app['application_id']);

                $loans_data[$i]['name'] = $app['full_name'];
                $loans_data[$i]['parentage'] = $app['parentage'];
                $loans_data[$i]['cnic'] = $app['cnic'];
                $loans_data[$i]['parentage_type'] = $app['parentage_type'];
                $loans_data[$i]['gender'] = $app['gender'];
                $loans_data[$i]['dob'] = date('Y-m-d', $app['dob']);
                $loans_data[$i]['education'] = $app['education'];
                $loans_data[$i]['marital_status'] = $app['marital_status'];
                $loans_data[$i]['family_member_name'] = $app['family_member_name'];
                $loans_data[$i]['family_member_cnic'] = $app['family_member_cnic'];
                $loans_data[$i]['religion'] = $app['religion'];
                $loans_data[$i]['project'] = 'Low Cost Housing Scheme';
                $loans_data[$i]['application_no'] = $app['application_no'];
                $loans_data[$i]['product'] = $app['product_name'];
                $loans_data[$i]['purpose'] = $app['activity_name'];
                $loans_data[$i]['sub_purpose'] = $app['sub_activity'];
                $loans_data[$i]['application_date'] = date('Y-m-d', $app['application_date']);
                $loans_data[$i]['region'] = $app['region_id'];
                $loans_data[$i]['area'] = $app['area_id'];
                $loans_data[$i]['branch'] = $app['branch_id'];
                $loans_data[$i]['city'] = $app['city_id'];
                $loans_data[$i]['district'] = $app['district_id'];
                $loans_data[$i]['address'] = $app['address'];
                $loans_data[$i]['mobile_no'] = $app['phone'];
                $loans_data[$i]['member_id'] = $app['id'];
                $loans_data[$i]['application_id'] = $app['application_id'];
                $loans_data[$i]['image_path'] = $app['profile_pic'];
                $loans_data[$i]['longitude'] = isset($app['longitude']) && !empty($app['longitude']) && $app['longitude'] != null ? $app['longitude'] : 0;
                $loans_data[$i]['latitude'] = isset($app['latitude']) && !empty($app['latitude']) && $app['latitude'] != null ? $app['latitude'] : 0;
                $loans_data[$i]['province'] = !empty($app['province_id']) ? $app['province_id'] : 0;
                $loans_data[$i]['completion_percent'] = !empty($app['completion_percent']) ? $app['completion_percent'] : 0;
                $loans_data[$i]['loan_amount'] = $app['loan_amount'];
                $loans_data[$i]['disbursed_amount'] = $app['disbursed_amount'];
                $loans_data[$i]['sanction_no'] = $app['sanction_no'];
                $loans_data[$i]['status'] = $app['status'];
                $loans_data[$i]['visits_count'] = !empty($app['visits_count']) ? $app['visits_count'] : 0;
                $loans_data[$i]['last_visit_date'] = !empty($app['last_visit_date']) ? date('Y-m-d', $app['last_visit_date']) : '';
//            if ($app['loan_visit_id'] != 0) {
//                $loans_data[$i]['visit_images'] = ImageHelper::getVisitImages($app['loan_visit_id'], 1);
//            } else {
                $loans_data[$i]['visit_images'] = [];
//            }
                $loans_data[$i]['is_shifted'] =   (!empty($loans_data[$i]['is_shifted']) && $loans_data[$i]['is_shifted'] != null) ? $loans_data[$i]['is_shifted'] : 0;
                $i++;

                print_r($loans_data);
                die();
            }
        }


        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );


        $ch = curl_init('http://20.174.13.174/post_data_project_details.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loans_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }





// * * * * * php /var/www/paperless_web/yii  housing-dashboard/flood-housing-project-details
// * * * * * php /var/www/paperless_web/yii  housing-dashboard/flood-housing-project-loans-push

    public function actionFloodHousingProjectDetails()
    {
        $data_array = [
            'total_loans' => 0,
            'amount_disbursed' => 0,
            'no_of_applications' => 0,
            'no_of_applications_pending' => 0,
            'amount_approved' => 0,
            'province_loans' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0
            ],
            'prurpose_loans' => [
                42 => 0,
                43 => 0,
                44 => 0,
            ],
            'completion_percent' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
            ]
        ];


        // project_id in (98,108) and
        $loan_query = 'select coalesce(sum(loans.disbursed_amount),0) as disbursed_amount,coalesce(sum(loans.loan_amount),0) as amount_approved ,count(loans.id) as loan_count,province_id from loans  inner join branches
              on branches.id=loans.branch_id where loans.project_id in (98,108) and loans.status in("loan completed","collected","grant") group by branches.province_id';
        $loan_data = \Yii::$app->db->createCommand($loan_query)->queryAll();
        foreach ($loan_data as $l_data) {
            $data_array['province_loans'][$l_data['province_id']] = $l_data['loan_count'];
            $data_array['total_loans'] += $l_data['loan_count'];
            $data_array['amount_disbursed'] += $l_data['disbursed_amount'];
            $data_array['amount_approved'] += $l_data['amount_approved'];
        }

        /////purpose-wise applications
        $loan_query_purpose = 'select count(loans.id) as loan_count,applications.activity_id as activity_id from loans  inner join applications 
              on applications.id=loans.application_id where loans.project_id in (98,108) and loans.status in("loan completed","collected","grant") group by applications.activity_id';
        $loan_data_purpose = \Yii::$app->db->createCommand($loan_query_purpose)->queryAll();
        foreach ($loan_data_purpose as $l_purpose_data) {
            $data_array['prurpose_loans'][$l_purpose_data['activity_id']] = $l_purpose_data['loan_count'];
        }

        ////pending/approved applications
        $app_query = 'select count(applications.id) as no_of_applications from applications  
              where applications.project_id in (98,108) and applications.status in("pending","approved","grant")';
        $app_data = \Yii::$app->db->createCommand($app_query)->queryAll();
        foreach ($app_data as $dt) {
            $data_array['no_of_applications'] += $dt['no_of_applications'];
        }
        $data_array['no_of_applications'] = $data_array['no_of_applications'] - $data_array['total_loans'];

        $total_applications = 'select count(applications.id) as no_of_applications from applications
                     where applications.project_id in (98,108)
                     and applications.status in("pending") and applications.deleted=0';
        $total_applications = \Yii::$app->db->createCommand($total_applications)->queryAll();
        $data_array['no_of_applications_pending'] = $data_array['no_of_applications'] - $total_applications[0]['no_of_applications'];

        $completion_wise_query = 'select loans.id,(select percent from visits where parent_id=applications.id and parent_type="application" and visits.deleted=0 order by created_at desc limit 1) as percent from loans  
                 inner join applications on applications.id=loans.application_id 
              where loans.project_id in (98,108) and loans.status in("loan completed","collected","grant")';

        $completion_wise_query = \Yii::$app->db->createCommand($completion_wise_query)->queryAll();

        foreach ($completion_wise_query as $l) {
            if ($l['percent'] >= 0 && $l['percent'] <= 25) {
                $data_array['completion_percent'][1] = $data_array['completion_percent'][1] + 1;
            } elseif ($l['percent'] > 25 && $l['percent'] <= 50) {
                $data_array['completion_percent'][2] = $data_array['completion_percent'][2] + 1;
            } elseif ($l['percent'] > 50 && $l['percent'] <= 75) {
                $data_array['completion_percent'][3] = $data_array['completion_percent'][3] + 1;
            } elseif ($l['percent'] > 75 && $l['percent'] <= 100) {
                $data_array['completion_percent'][4] = $data_array['completion_percent'][4] + 1;
            } else {
                $data_array['completion_percent'][5] = $data_array['completion_percent'][5] + 1;
            }
        }


        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );

        print_r($data_array);
        $ch = curl_init('http://40.115.27.118/post_data_project_summary.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }

    public function actionFloodHousingProjectLoansPush()
    {
        $loans_data = [];
        $applications = 'select m.id as id,
                        applications.id as application_id,
                        applications.referral_id,
                        applications.req_amount as req_amount,
                        m.full_name as full_name,
                        m.parentage as parentage,
                        m.cnic as cnic,
                        m.parentage_type as parentage_type,
                        m.gender as gender,
                        m.dob as dob,
                        m.education as education,
                        m.dob as dob,
                        m.education as education,
                        m.marital_status as marital_status,
                        m.family_member_name as family_member_name,
                        m.family_member_cnic as family_member_cnic,
                        m.religion as religion,
                        p.name as product_name,
                        a.name as activity_name,
                        applications.sub_activity as sub_activity,
                        applications.region_id as region_id,
                        applications.area_id as area_id,
                        applications.branch_id as branch_id,
                        applications.application_date as application_date,
                        applications.application_no as application_no,
                        b.city_id as city_id,
                        b.district_id as district_id,
                        b.province_id as province_id,
                        applications.id as application_id,
                (select coalesce(id,0) from visits where parent_id=applications.id and parent_type="application" 
                       and visits.deleted=0 order by created_at desc limit 1) as loan_visit_id,
                (select percent from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0 order by created_at desc limit 1) as completion_percent,
                (select coalesce(count(id),0) from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0) as visits_count,
                (select created_at from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0  order by created_at desc limit 1) as last_visit_date,
                (select address from members_address ma where ma.member_id=applications.member_id 
                               and address_type="business" and is_current=1 limit 1) as address ,
                (select phone from members_phone mp where mp.member_id=applications.member_id 
                               and phone_type="mobile" and is_current=1 limit 1) as phone,
                (select image_name from images i where i.parent_id=applications.member_id 
                               and parent_type="members" and image_type="profile_pic" limit 1) as profile_pic,
                (select longitude from visits v where v.parent_id=applications.id 
                               and parent_type="application" and v.longitude > 0 order by created_at desc limit 1) as longitude,
                (select v1.latitude from visits v1 where v1.parent_id=applications.id 
                               and v1.parent_type="application" and v1.latitude > 0 order by v1.created_at desc limit 1) as latitude              
                 from applications 
                 inner join loans l on l.application_id=applications.id 
                 inner join members m on m.id=applications.member_id
                 inner join branches b on b.id=applications.branch_id
                 inner join products p on p.id=applications.product_id
                 inner join activities a on a.id=applications.activity_id
              where applications.status="approved" and l.status in ("collected","loan completed","grant") and applications.deleted=0 and l.project_id in (98,108)';
        $applications = \Yii::$app->db->createCommand($applications)->queryAll();
        $i = 0;
        foreach ($applications as $app) {

            $shifted = ApplicationDetails::getShifted($app['application_id']);

            $loans_data[$i]['name'] = $app['full_name'];
            $loans_data[$i]['parentage'] = $app['parentage'];
            $loans_data[$i]['cnic'] = $app['cnic'];
            $loans_data[$i]['parentage_type'] = $app['parentage_type'];
            $loans_data[$i]['gender'] = $app['gender'];
            $loans_data[$i]['dob'] = date('Y-m-d', $app['dob']);
            $loans_data[$i]['education'] = $app['education'];
            $loans_data[$i]['marital_status'] = $app['marital_status'];
            $loans_data[$i]['family_member_name'] = $app['family_member_name'];
            $loans_data[$i]['family_member_cnic'] = $app['family_member_cnic'];
            $loans_data[$i]['religion'] = $app['religion'];
            $loans_data[$i]['project'] = 'Akhuwat Housing Loan for Flood 2022';
            $loans_data[$i]['application_no'] = $app['application_no'];
            $loans_data[$i]['product'] = $app['product_name'];
            $loans_data[$i]['purpose'] = $app['activity_name'];
            $loans_data[$i]['sub_purpose'] = $app['sub_activity'];
            $loans_data[$i]['application_date'] = date('Y-m-d', $app['application_date']);
            $loans_data[$i]['region'] = $app['region_id'];
            $loans_data[$i]['area'] = $app['area_id'];
            $loans_data[$i]['branch'] = $app['branch_id'];
            $loans_data[$i]['city'] = $app['city_id'];
            $loans_data[$i]['district'] = $app['district_id'];
            $loans_data[$i]['address'] = $app['address'];
            $loans_data[$i]['mobile_no'] = $app['phone'];
            $loans_data[$i]['member_id'] = $app['id'];
            $loans_data[$i]['application_id'] = $app['application_id'];
            $loans_data[$i]['image_path'] = $app['profile_pic'];
            $loans_data[$i]['longitude'] = !empty($app['longitude']) ? $app['longitude'] : 0;
            $loans_data[$i]['latitude'] = !empty($app['latitude']) ? $app['latitude'] : 0;
            $loans_data[$i]['province'] = !empty($app['province_id']) ? $app['province_id'] : 0;
            $loans_data[$i]['completion_percent'] = !empty($app['completion_percent']) ? $app['completion_percent'] : 0;
            $loan = Loans::find()->where(['application_id' => $app['application_id'], 'deleted' => 0])->one();
            if (!empty($loan)) {
                $loans_data[$i]['loan_amount'] = $loan->loan_amount;
                $loans_data[$i]['disbursed_amount'] = $loan->disbursed_amount;
                $loans_data[$i]['sanction_no'] = $loan->sanction_no;
                $loans_data[$i]['status'] = $loan->status;
            } else {
                $loans_data[$i]['loan_amount'] = !empty($app['req_amount']) ? $app['req_amount'] : 0;
                $loans_data[$i]['disbursed_amount'] = 0;
                $loans_data[$i]['sanction_no'] = '';
                $loans_data[$i]['status'] = 'application';
            }
            $loans_data[$i]['visits_count'] = !empty($app['visits_count']) ? $app['visits_count'] : 0;
            $loans_data[$i]['last_visit_date'] = !empty($app['last_visit_date']) ? date('Y-m-d', $app['last_visit_date']) : '';
            if ($app['loan_visit_id'] != 0) {
                $loans_data[$i]['visit_images'] = ImageHelper::getVisitImages($app['loan_visit_id'], 1);
            } else {
                $loans_data[$i]['visit_images'] = [];
            }
            $loans_data[$i]['referral_id'] = (empty($app['referral_id']) && $app['referral_id'] == null) ? 0 : (int)$app['referral_id'];
            $loans_data[$i]['is_shifted'] = $shifted;

            $i++;
        }

        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );


        $ch = curl_init('http://40.115.27.118/post_data_project_details.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loans_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }


// * * * * * php /var/www/paperless_web/yii  housing-dashboard/emergency-project-details
// * * * * * php /var/www/paperless_web/yii  housing-dashboard/emergency-project-loans-push

    public function actionEmergencyProjectDetails()
    {
        $data_array = [
            'total_loans' => 0,
            'amount_disbursed' => 0,
            'no_of_applications' => 0,
            'no_of_applications_pending' => 0,
            'province_loans' => [
                1 => 0,
                2 => 0,
                3 => 0,
                4 => 0,
                5 => 0,
                6 => 0,
                7 => 0,
                8 => 0
            ],
            /* 'prurpose_loans' => [
                 42 => 0,
                 43 => 0,
                 44 => 0,
             ],*/
            /* 'completion_percent' => [
                 1 => 0,
                 2 => 0,
                 3 => 0,
                 4 => 0,
             ]*/
        ];

        // project_id=62 and
        $loan_query = 'select coalesce(sum(loans.disbursed_amount),0) as disbursed_amount ,count(loans.id) as loan_count,province_id from loans  inner join branches
              on branches.id=loans.branch_id where loans.project_id=60 and loans.status in("loan completed","collected") group by branches.province_id';
        $loan_data = \Yii::$app->db->createCommand($loan_query)->queryAll();
        foreach ($loan_data as $l_data) {
            $data_array['province_loans'][$l_data['province_id']] = $l_data['loan_count'];
            $data_array['total_loans'] += $l_data['loan_count'];
            $data_array['amount_disbursed'] += $l_data['disbursed_amount'];
        }

        /////purpose-wise applications
        $loan_query_purpose = 'select count(loans.id) as loan_count,applications.activity_id as activity_id from loans  inner join applications 
              on applications.id=loans.application_id where loans.project_id=60 and loans.status in("loan completed","collected") group by applications.activity_id';
        $loan_data_purpose = \Yii::$app->db->createCommand($loan_query_purpose)->queryAll();
        foreach ($loan_data_purpose as $l_purpose_data) {
            $data_array['prurpose_loans'][$l_purpose_data['activity_id']] = $l_purpose_data['loan_count'];
        }
        ////pending/approved applications
        $app_query = 'select count(applications.id) as no_of_applications from applications  
              where applications.project_id=60 and applications.status in("pending","approved")';
        $app_data = \Yii::$app->db->createCommand($app_query)->queryAll();
        foreach ($app_data as $dt) {
            $data_array['no_of_applications'] += $dt['no_of_applications'];
            /*if($dt['status']=='pending'){
                $data_array['no_of_applications_pending']+=$dt['no_of_applications'];
            }*/
        }
        $data_array['no_of_applications'] = $data_array['no_of_applications'] - $data_array['total_loans'];

        /*$total_applications='select count(applications.id) as no_of_applications from applications
                 left join loans on loans.application_id=applications.id
                     where loans.application_id is null and applications.project_id=52
                     and applications.status in("pending","approved")';
        $total_applications = \Yii::$app->db->createCommand($total_applications)->queryAll();
        $data_array['no_of_applications_pending']=$total_applications[0]['no_of_applications'];*/
        $total_applications = 'select count(applications.id) as no_of_applications from applications
                     where applications.project_id=60
                     and applications.status in("pending") and applications.deleted=0';
        $total_applications = \Yii::$app->db->createCommand($total_applications)->queryAll();
        $data_array['no_of_applications_pending'] = $data_array['no_of_applications'] - $total_applications[0]['no_of_applications'];
        ////////////////////
        /* $completion_wise_query='select loans.id,(select percent from visits where parent_id=applications.id and parent_type="application" and visits.deleted=0 order by created_at desc limit 1) as percent from loans
                  inner join applications on applications.id=loans.application_id
               where loans.project_id=52 and loans.status in("loan completed","collected")';*/
        //  $completion_wise_query = \Yii::$app->db->createCommand($completion_wise_query)->queryAll();

        /* foreach ($completion_wise_query as $l){
             if($l['percent']>=0 && $l['percent']<=25){
                 $data_array['completion_percent'][1]= $data_array['completion_percent'][1]+1;
             }elseif ($l['percent']>25 && $l['percent']<=50){
                 $data_array['completion_percent'][2]= $data_array['completion_percent'][2]+1;
             }elseif ($l['percent']>50 && $l['percent']<=75){
                 $data_array['completion_percent'][3]= $data_array['completion_percent'][3]+1;
             }elseif ($l['percent']>75 && $l['percent']<=100){
                 $data_array['completion_percent'][4]= $data_array['completion_percent'][4]+1;
             }
         }*/
        ////////////////////
        ////completion-wise loans
        /*$completion_wise_query_1='select coalesce(count(loans.id),0) as no_of_loans from loans
                 inner join applications on applications.id=loans.application_id
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=52 and loans.status in("loan completed","collected") and  visits.percent between 0 and 25';
        $completion_wise_query_1 = \Yii::$app->db->createCommand($completion_wise_query_1)->queryAll();


        $completion_wise_query_2='select coalesce(count(loans.id),0) as no_of_loans from loans
                 inner join applications on applications.id=loans.application_id
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=52 and loans.status in("loan completed","collected") and  visits.percent between 26 and 50';
        $completion_wise_query_2 = \Yii::$app->db->createCommand($completion_wise_query_2)->queryAll();


        $completion_wise_query_3='select coalesce(count(loans.id),0) as no_of_loans from loans
                 inner join applications on applications.id=loans.application_id
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=52 and loans.status in("loan completed","collected") and  visits.percent between 51 and 75';
        $completion_wise_query_3 = \Yii::$app->db->createCommand($completion_wise_query_3)->queryAll();

        $completion_wise_query_4='select coalesce(count(loans.id),0) as no_of_loans from loans
                 inner join applications on applications.id=loans.application_id
                 inner join visits on visits.parent_id=applications.id
              where loans.project_id=52 and loans.status in("loan completed","collected") and  visits.percent between 76 and 100';
        $completion_wise_query_4 = \Yii::$app->db->createCommand($completion_wise_query_4)->queryAll();


        $data_array['completion_percent'][1]= $completion_wise_query_1[0]['no_of_loans'];
        $data_array['completion_percent'][2]= $completion_wise_query_2[0]['no_of_loans'];
        $data_array['completion_percent'][3]= $completion_wise_query_3[0]['no_of_loans'];
        $data_array['completion_percent'][4]= $completion_wise_query_4[0]['no_of_loans'];*/

        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );


        $ch = curl_init('http://40.118.255.234/gis/lowcost/post_data_emergency_summary.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data_array));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }

    public function actionEmergencyProjectLoansPush()
    {
        $loans_data = [];
        $applications = 'select m.id as id,
                        applications.id as application_id,
                        applications.req_amount as req_amount,
                        m.full_name as full_name,
                        m.parentage as parentage,
                        m.cnic as cnic,
                        m.parentage_type as parentage_type,
                        m.gender as gender,
                        m.dob as dob,
                        m.education as education,
                        m.dob as dob,
                        m.education as education,
                        m.marital_status as marital_status,
                        m.family_member_name as family_member_name,
                        m.family_member_cnic as family_member_cnic,
                        m.religion as religion,
                        p.name as product_name,
                        a.name as activity_name,
                        applications.sub_activity as sub_activity,
                        applications.region_id as region_id,
                        applications.area_id as area_id,
                        applications.branch_id as branch_id,
                        applications.application_date as application_date,
                        applications.application_no as application_no,
                        b.city_id as city_id,
                        b.district_id as district_id,
                        b.province_id as province_id,
                        applications.id as application_id,
               /* (select percent from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0 order by created_at desc limit 1) as completion_percent,
                (select coalesce(count(id),0) from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0) as visits_count,
                (select created_at from visits where parent_id=applications.id and parent_type="application" 
                               and visits.deleted=0  order by created_at desc limit 1) as last_visit_date,*/
                (select address from members_address ma where ma.member_id=applications.member_id 
                               and address_type="business" and is_current=1 limit 1) as address ,
                (select phone from members_phone mp where mp.member_id=applications.member_id 
                               and phone_type="mobile" and is_current=1 limit 1) as phone,
                (select image_name from images i where i.parent_id=applications.member_id 
                               and parent_type="members" and image_type="profile_pic" limit 1) as profile_pic,
                (select longitude from visits v where v.parent_id=applications.id 
                               and parent_type="application" order by created_at desc limit 1) as longitude ,
                (select v1.latitude from visits v1 where v1.parent_id=applications.id 
                               and v1.parent_type="application" order by v1.created_at desc limit 1) as latitude 
                 from applications 
                 inner join loans l on l.application_id=applications.id 
                 inner join members m on m.id=applications.member_id
                 inner join branches b on b.id=applications.branch_id
                 inner join products p on p.id=applications.product_id
                 inner join activities a on a.id=applications.activity_id
              where applications.status="approved" and l.status in ("collected","loan completed","rejected","not collected") and applications.deleted=0 and l.project_id=60';
        $applications = \Yii::$app->db->createCommand($applications)->queryAll();
        $i = 0;
        foreach ($applications as $app) {
            $loans_data[$i]['name'] = $app['full_name'];
            $loans_data[$i]['parentage'] = $app['parentage'];
            $loans_data[$i]['cnic'] = $app['cnic'];
            $loans_data[$i]['parentage_type'] = $app['parentage_type'];
            $loans_data[$i]['gender'] = $app['gender'];
            $loans_data[$i]['dob'] = date('Y-m-d', $app['dob']);
            $loans_data[$i]['education'] = $app['education'];
            $loans_data[$i]['marital_status'] = $app['marital_status'];
            $loans_data[$i]['family_member_name'] = $app['family_member_name'];
            $loans_data[$i]['family_member_cnic'] = $app['family_member_cnic'];
            $loans_data[$i]['religion'] = $app['religion'];
            $loans_data[$i]['project'] = 'Emergency Loan';
            $loans_data[$i]['application_no'] = $app['application_no'];
            $loans_data[$i]['product'] = $app['product_name'];
            $loans_data[$i]['purpose'] = $app['activity_name'];
            $loans_data[$i]['sub_purpose'] = $app['sub_activity'];
            $loans_data[$i]['application_date'] = date('Y-m-d', $app['application_date']);
            $loans_data[$i]['region'] = $app['region_id'];
            $loans_data[$i]['area'] = $app['area_id'];
            $loans_data[$i]['branch'] = $app['branch_id'];
            $loans_data[$i]['city'] = $app['city_id'];
            $loans_data[$i]['district'] = $app['district_id'];
            $loans_data[$i]['address'] = $app['address'];
            $loans_data[$i]['mobile_no'] = $app['phone'];
            $loans_data[$i]['member_id'] = $app['id'];
            $loans_data[$i]['application_id'] = $app['application_id'];
            $loans_data[$i]['image_path'] = $app['profile_pic'];
            $loans_data[$i]['longitude'] = !empty($app['longitude']) ? $app['longitude'] : 0;
            $loans_data[$i]['latitude'] = !empty($app['latitude']) ? $app['latitude'] : 0;
            $loans_data[$i]['province'] = !empty($app['province_id']) ? $app['province_id'] : 0;
            // $loans_data[$i]['completion_percent']=!empty($app['completion_percent'])?$app['completion_percent']:0;
            $loan = Loans::find()->where(['application_id' => $app['application_id'], 'deleted' => 0])->one();
            if (!empty($loan)) {
                $loans_data[$i]['loan_amount'] = $loan->loan_amount;
                $loans_data[$i]['disbursed_amount'] = $loan->disbursed_amount;
                $loans_data[$i]['sanction_no'] = $loan->sanction_no;
                $loans_data[$i]['status'] = $loan->status;
            } else {
                $loans_data[$i]['loan_amount'] = !empty($app['req_amount']) ? $app['req_amount'] : 0;
                $loans_data[$i]['disbursed_amount'] = 0;
                $loans_data[$i]['sanction_no'] = '';
                $loans_data[$i]['status'] = 'application';
            }
            // $loans_data[$i]['visits_count']=!empty($app['visits_count'])?$app['visits_count']:0;
            // $loans_data[$i]['last_visit_date']=!empty($app['last_visit_date'])?date('Y-m-d',$app['last_visit_date']):'';

            $i++;
        }

        $headers = array
        (
            'X-Access-Token: 453fc1e7e030326df71ab9278283fb8a',
            'Content-Type: application/json',
            'x-api-key: sdf3rfew3ferf$dfvfrrg#dgsrr2342gdas',
            'version_code: 19',
        );


        $ch = curl_init('http://40.118.255.234/gis/lowcost/post_data_emergency_details.php');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loans_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($ch);
        print_r($result);
        curl_close($ch);
    }
}