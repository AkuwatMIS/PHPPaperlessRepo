<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;


use common\components\DBHelper;
use common\components\Helpers\MemberHelper;
use common\components\Helpers\StructureHelper;
use common\components\Parsers\ApiParser;
use common\components\ViewFormHelper;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\AppraisalsBusiness;
use common\models\ConfigRules;
use common\models\Documents;
use common\models\Guarantors;
use common\models\Images;
use common\models\Lists;
use common\models\Loans;
use common\models\MemberInfo;
use common\models\Members;
use common\models\Referrals;
use common\models\SocialAppraisal;
use common\models\Verification;
use common\models\Projects;
use common\models\Visits;
use yii\helpers\ArrayHelper;
use Yii;

class ApplicationHelper
{
    public static function getReferredByList()
    {
        //$referred_by = ['','Dr Amjad Saqib', 'NJV', 'PHA', 'Digi Skills'];
        $referred_by = ArrayHelper::map(Referrals::find()->where(['id'=>0])->all(),'id','name');
        return $referred_by;
    }

    public static function getWhoWillWork()
    {
        $work = ArrayHelper::map(Lists::find()->where(['list_name'=>'who_will_work'])->all(),'value','label');
        return $work;
    }

    public static function getPropertyRecordType()
    {
        $properType = ArrayHelper::map(Lists::find()->where(['list_name'=>'property_record_type'])->all(),'value','label');
        return $properType;
    }

    public static function getInstallmentsTypes()
    {
        $work = ArrayHelper::map(Lists::find()->where(['list_name'=>'installments_types'])->all(),'value','label');
        return $work;
    }


    public static function getIsUrban()
    {
        $is_urban = ArrayHelper::map(Lists::find()->where(['list_name'=>'is_urban'])->all(),'value','label');
        return $is_urban;
    }

    public static function getVerification()
    {
        $verification = ArrayHelper::map(Lists::find()->where(['list_name'=>'verification'])->all(),'value','label');
        return $verification;
    }
    public static function getAppStatus()
    {
        $application_status = ArrayHelper::map(Lists::find()->where(['list_name'=>'application_status'])->all(),'value','label');
        return $application_status;
    }

    public static function getInfoByApplicationNew($id)
    {
        $data = [];
        $application = Applications::find()->where(['id' => $id,'deleted' => 0])->one();
        $data = [
            'full_name'=>isset($application->member->full_name)?$application->member->full_name:'',
            'profile_pic' => ApiParser::parseImage(Images::findOne(['parent_id' => $application->member->id, 'parent_type' => 'members', 'image_type' => 'profile_pic']), $application->member->id),
            'parentage'=>isset($application->member->parentage)?$application->member->parentage:'',
            'parentage_type'=>isset($application->member->parentage_type)?$application->member->parentage_type:'',
            'gender'=>isset($application->member->gender)?$application->member->gender:'',
            'application_no'=>isset($application->application_no)?$application->application_no:'',
            /*'social_appraisal_latitude'=>isset($application->socialAppraisal->latitude)?$application->socialAppraisal->latitude:'0.0',
            'social_appraisal_longitude'=>isset($application->socialAppraisal->longitude)?$application->socialAppraisal->longitude:'0.0',
            'social_appraisal_address'=>isset($application->socialAppraisal->social_appraisal_address)?$application->socialAppraisal->social_appraisal_address:'0.0',
            'business_appraisal_latitude'=>isset($application->businessAppraisal->latitude)?$application->businessAppraisal->latitude:'0.0',
            'business_appraisal_longitude'=>isset($application->businessAppraisal->longitude)?$application->businessAppraisal->longitude:'0.0',
            'business_appraisal_address'=>isset($application->businessAppraisal->business_appraisal_address)?$application->businessAppraisal->business_appraisal_address:'0.0',
            */'verification_details'=>[
                [
                    'name' => 'Member',
                    'details' => ApiParser::parseMemberByKeyValue($application->member)
                ],
                [
                    'name' => 'Application',
                    'details' => ApiParser::parseApplicationByKeyValue($application)
                ],
                /*[
                    'name' => 'Social Appraisal',
                    'details' => ApiParser::parseSocialAppraisalByKeyValue($application->socialAppraisal)
                ],
                [
                    'name' => 'Business Appraisal',
                    'details' => ApiParser::parseBusinessAppraisalByKeyValue($application->businessAppraisal)
                ],*/
                /*[
                    'name' => 'Documents',
                    'details' => self::getdocuments($application)
                ],*/
            ]
        ];
        $data = array_merge($data,self::getAppraisalLocations($application));
        $data['verification_details'] = array_merge($data['verification_details'],self::getAppraisalDetail($application));
        $data['verification_details'][] = [
            'name' => 'Documents',
            'details' => self::getdocuments($application)
        ];
        return $data;
    }

    public static function getAppraisalLocations($application)
    {
        $data = [];
        if(isset($application->socialAppraisal))
        {
            $data['social_appraisal_latitude'] = isset($application->socialAppraisal->latitude)?$application->socialAppraisal->latitude:'0.0';
            $data['social_appraisal_longitude'] = isset($application->socialAppraisal->longitude)?$application->socialAppraisal->longitude:'0.0';
            $data['social_appraisal_address'] = isset($application->socialAppraisal->social_appraisal_address)?$application->socialAppraisal->social_appraisal_address:'0.0';

        }
        if(isset($application->businessAppraisal))
        {
            $data['business_appraisal_latitude']=isset($application->businessAppraisal->latitude)?$application->businessAppraisal->latitude:'0.0';
            $data['business_appraisal_longitude']=isset($application->businessAppraisal->longitude)?$application->businessAppraisal->longitude:'0.0';
            $data['business_appraisal_address']=isset($application->businessAppraisal->business_appraisal_address)?$application->businessAppraisal->business_appraisal_address:'0.0';

        }
        if(isset($application->housingAppraisal))
        {
            $data['housing_appraisal_latitude']=isset($application->housingAppraisal->latitude)?$application->housingAppraisal->latitude:'0.0';
            $data['housing_appraisal_longitude']=isset($application->housingAppraisal->longitude)?$application->housingAppraisal->longitude:'0.0';
            $data['housing_appraisal_address']=isset($application->housingAppraisal->housing_appraisal_address)?$application->housingAppraisal->housing_appraisal_address:'0.0';

        }
        if(isset($application->agricultureAppraisal))
        {
            $data['agriculture_appraisal_latitude']=isset($application->agricultureAppraisal->latitude)?$application->agricultureAppraisal->latitude:'0.0';
            $data['agriculture_appraisal_longitude']=isset($application->agricultureAppraisal->longitude)?$application->agricultureAppraisal->longitude:'0.0';
            $data['agriculture_appraisal_address']=isset($application->agricultureAppraisal->agriculture_appraisal_address)?$application->agricultureAppraisal->agriculture_appraisal_address:'0.0';

        }
        if(isset($application->emergencyAppraisal))
        {
            $data['emergency_appraisal_latitude']=isset($application->emergencyAppraisal->latitude)?$application->emergencyAppraisal->latitude:'0.0';
            $data['emergency_appraisal_longitude']=isset($application->emergencyAppraisal->longitude)?$application->emergencyAppraisal->longitude:'0.0';
            $data['emergency_appraisal_address']=isset($application->emergencyAppraisal->emergency_appraisal_address)?$application->emergencyAppraisal->emergency_appraisal_address:'0.0';

        }
        return $data;
    }

    public static function getHousingAppraisalLocations($application)
    {
        $data = [];

        if(isset($application->housingAppraisal))
        {
            $data['housing_appraisal_latitude']=isset($application->housingAppraisal->latitude)?$application->housingAppraisal->latitude:'0.0';
            $data['housing_appraisal_longitude']=isset($application->housingAppraisal->longitude)?$application->housingAppraisal->longitude:'0.0';
            $data['housing_appraisal_address']=isset($application->housingAppraisal->housing_appraisal_address)?$application->housingAppraisal->housing_appraisal_address:'0.0';

        }
        return $data;
    }

    public static function getVisitsByRole($id,$download)
    {
        $visits = Visits::find()->where(['parent_type' => 'application','parent_id' => $id, 'deleted' => 0])->all();
        foreach ($visits as $visit)
        {
            $visit_list[] = ApiParser::parseVisit($visit,$download,$id);
        }
        return $visit_list;
    }

    public static function getShiftedVisitsId($id,$download)
    {
        $visits = Visits::find()->where(['id' => $id])->all();
        foreach ($visits as $visit)
        {
            $visit_list[] = ApiParser::parseVisit($visit,$download,$id);
        }
        return $visit_list;
    }

    public static function getInfoByApplication($id)
    {
        $data = [];
        $application = Applications::find()->where(['id' => $id,'deleted' => 0])->one();
        $data = [
            'full_name'=>isset($application->member->full_name)?$application->member->full_name:'',
            'profile_pic' => ApiParser::parseImage(Images::findOne(['parent_id' => $application->member->id, 'parent_type' => 'members', 'image_type' => 'profile_pic']), $application->member->id),
            'parentage'=>isset($application->member->parentage)?$application->member->parentage:'',
            'parentage_type'=>isset($application->member->parentage_type)?$application->member->parentage_type:'',
            'gender'=>isset($application->member->gender)?$application->member->gender:'',
            'application_no'=>isset($application->application_no)?$application->application_no:'',
            'is_auto_month'=>in_array($application->project_id,StructureHelper::autoSelectMonthProjects())?0:1,
            /*'social_appraisal_latitude'=>isset($application->socialAppraisal->latitude)?$application->socialAppraisal->latitude:'0.0',
            'social_appraisal_longitude'=>isset($application->socialAppraisal->longitude)?$application->socialAppraisal->longitude:'0.0',
            'social_appraisal_address'=>isset($application->socialAppraisal->social_appraisal_address)?$application->socialAppraisal->social_appraisal_address:'0.0',
            'business_appraisal_latitude'=>isset($application->businessAppraisal->latitude)?$application->businessAppraisal->latitude:'0.0',
            'business_appraisal_longitude'=>isset($application->businessAppraisal->longitude)?$application->businessAppraisal->longitude:'0.0',
            'business_appraisal_address'=>isset($application->businessAppraisal->business_appraisal_address)?$application->businessAppraisal->business_appraisal_address:'0.0',
            */'verification_details'=>[
                [
                    'name' => 'Member',
                    'details' => ApiParser::parseMemberByKeyValue($application->member)
                ],
                [
                    'name' => 'Application',
                    'details' => ApiParser::parseApplicationByKeyValue($application)
                ],
                /*[
                    'name' => 'Social Appraisal',
                    'details' => ApiParser::parseSocialAppraisalByKeyValue($application->socialAppraisal)
                ],
                [
                    'name' => 'Business Appraisal',
                    'details' => ApiParser::parseBusinessAppraisalByKeyValue($application->businessAppraisal)
                ],*/
                /*[
                    'name' => 'Documents',
                    'details' => self::getdocuments($application)
                ],*/
            ]
        ];
        $data = array_merge($data,self::getAppraisalLocations($application));
        $data['verification_details'] = array_merge($data['verification_details'],self::getAppraisalDetail($application));
        $data['verification_details'][] = [
            'name' => 'Documents',
            'details' => self::getdocuments($application)
        ];
        return $data;
    }

    public static function getInfoByApplicationForSE($application)
    {
        $data = [];
        $data = [
            /*'full_name'=>isset($application->member->full_name)?$application->member->full_name:'',
            'profile_pic' => ApiParser::parseImage(Images::findOne(['parent_id' => $application->member->id, 'parent_type' => 'members', 'image_type' => 'profile_pic']), $application->member->id),
            'parentage'=>isset($application->member->parentage)?$application->member->parentage:'',
            'parentage_type'=>isset($application->member->parentage_type)?$application->member->parentage_type:'',
            'gender'=>isset($application->member->gender)?$application->member->gender:'',
            'application_no'=>isset($application->application_no)?$application->application_no:'',
            'client_contribution'=>isset($application->client_contribution)?$application->client_contribution:'',*/
            'verification_details'=>[
                [
                    'name' => 'Member',
                    'details' => ApiParser::parseMemberByKeyValueSE($application)
                ]
            ]
        ];
        $data = array_merge($data,self::getHousingAppraisalLocations($application));
        $data['verification_details'] = array_merge($data['verification_details'],self::getAppraisalDetailHousing($application));
        /*$data['verification_details'][] = [
            'name' => 'Documents',
            'details' => self::getdocuments($application)
        ];*/
        return $data;
    }

    public static function getAppraisalDetail($application)
    {
        $appraisals = [];
        foreach ($application->project->appraisals as $appr)
        {
            if($appr->appraisal->name == 'social_appraisal')
            {
                $appraisals[] = [
                    'name' => 'Social Appraisal',
                    'details' => ApiParser::parseSocialAppraisalByKeyValue($application->socialAppraisal)
                ];
            } else if($appr->appraisal->name == 'business_appraisal')
            {
                $appraisals[] = [
                    'name' => 'Business Appraisal',
                    'details' => ApiParser::parseBusinessAppraisalByKeyValue($application->businessAppraisal)
                ];
            } else if($appr->appraisal->name == 'housing_appraisal')
            {
                $appraisals[] = [
                    'name' => 'Housing Appraisal',
                    'details' => ApiParser::parseHousingAppraisalByKeyValue($application->housingAppraisal)
                ];
            }  else if(isset($application->agricultureAppraisal))
            {
                $appraisals[] = [
                    'name' => 'Agriculture Appraisal',
                    'details' => ApiParser::parseAgricultureAppraisalByKeyValue($application->agricultureAppraisal)
                ];
            }
        }
        return $appraisals;
    }

    public static function getAppraisalDetailHousing($application)
    {
        $appraisals = [];
        foreach ($application->project->appraisals as $appr)
        {
            if($appr->appraisal->name == 'housing_appraisal')
            {
                $appraisals[] = [
                    'name' => 'Housing Appraisal',
                    'details' => ApiParser::parseHousingAppraisalByKeyValueSE($application->housingAppraisal)
                ];
            }
        }
        return $appraisals;
    }

    public static function getdocuments($application)
    {
        $member_id = $application->member_id;
        $application_id = $application->id;
        $data = [];
        // $data .= ApiParser::parseImage(Images::findOne(['parent_id' => $member_id, 'parent_type' => 'members', 'image_type' => 'profile_pic']), $member_id);
        $data[]= ['key' => 'Cnic Front', 'value' => ApiParser::parseImage(Images::findOne(['parent_id' => $member_id, 'parent_type' => 'members', 'image_type' => 'cnic_front']), $member_id)];
        $data[]= ['key' => 'Cnic Back', 'value' => ApiParser::parseImage(Images::findOne(['parent_id' => $member_id, 'parent_type' => 'members', 'image_type' => 'cnic_back']), $member_id)];
        $data[]= ['key' => 'Utility Bill', 'value' => ApiParser::parseImage(Images::findOne(['parent_id' => $application_id, 'parent_type' => 'applications', 'image_type' => 'utility_bill']), $application_id)];
        $data[]= ['key' => 'Marraige Certificate', 'value' => ApiParser::parseImage(Images::findOne(['parent_id' => $application_id, 'parent_type' => 'applications', 'image_type' => 'marriage_certificate']), $application_id)];
        $docs = Documents::find()->where(['module_type' => 'projects'])->andWhere(['module_id' => $application->project_id])->all();
        if (isset($docs)) {
            foreach ($docs as $doc) {
                $data[]= ['key' => $doc->name, 'value' => ApiParser::parseImage(Images::findOne(['parent_id' => $application_id, 'parent_type' => 'applications', 'image_type' => $doc->name]), $application_id)];
            }
        }
        $data = [
            array(
                'key'=>'documents',
                //'value'=>$data,
                'document_details' => $data
            ),
        ];
        //$data = array_merge(array(array('key'=>'documents')),['value'=>$data]);
        $check_array = array(
            array(
                'key' => 'check',
                'value' => 'Check Documents Details',
            )
        );
        $data = array_merge($data,$check_array);
        return $data;
    }

    public static function getSocialAppraisal($application_id)
    {
        $social_appraisal = SocialAppraisal::findOne(['application_id' => $application_id]);
        $social_appraisal = ApiParser::parseSocialAppraisal($social_appraisal);
        return $social_appraisal;
    }

    public static function getBusinessAppraisal($application_id)
    {
        $business_appraisal = AppraisalsBusiness::findOne(['application_id' => $application_id]);
        $business_appraisal = ApiParser::parseBusinessAppraisal($business_appraisal);
        return $business_appraisal;
    }

    public static function getInfoByMember($id,$status)
    {
        $data = [];
        $application = Applications::find()->where(['applications.id' => $id,'applications.deleted' => 0])->one();
        /*print_r($application);
        print_r($application->member);
        die();*/
        $data = ApiParser::parseVerificationMember($application,$status);
        return $data;
    }
    public static function getApplicationsForBMVerification()
    {
        ini_set('memory_limit', '1024M');
        //$user = Users::findIdentityByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'));
        $applications = Applications::find()
            ->join('inner join','application_actions','application_actions.parent_id=applications.id')
            ->where(['applications.deleted'=>0,'applications.status'=>'pending','application_actions.action'=>'approved/rejected'])
            //->andWhere(['application_actions.user_id'=>$user->id])
            ->orderBy('created_at desc')->all();
        $data = [];
        foreach ($applications as $application)
        {
            $member_info = ApiParser::parseVerificationMember($application);
            $member_info = array_merge($member_info,MemberHelper::getMemberProfileImage($member_info['id']));
            $data['members'][] = $member_info;
        }
        return $data;
    }
    public static function getApplicationsForVerification()
    {
        ini_set('memory_limit', '1024M');
        $applications = Applications::find()->where(['deleted'=>0])->all();
        $counter = 1;
        $data = [];
        $skip_ids = [];
        $verifications = Verification::find()->select(['application_id'])->where(['status'=> "skip"])->all();
        if(isset($verifications)) {
            foreach ($verifications as $verification) {
                $skip_ids[] = $verification['application_id'];
            }
        }
        foreach ($applications as $application)
        {
            $flag = true;
            $verification = Verification::find()->where(['application_id'=> $application['id']])->andFilterWhere(['!=','status','skip'])->one();
            $loan = Loans::findOne(['application_id' => $application['id']]);
            if(isset($verification))
            {
                if(!isset($loan)) {
                    $member_info = self::getInfoByMember($application['id'],$verification['status']);
                    //$member_info['verification_status'] = $verification['status'];
                    //$member_info['assigned_to'] = $verification['assigned_to'];
                    $member_info = array_merge($member_info,MemberHelper::getMemberProfileImage($member_info['id']));
                    $data['members'][] = $member_info;
                    $counter++;
                    $flag = false;
                }
            }
            if($counter <= 5) {
                if (!isset($loan) && !in_array($application['id'],$skip_ids) && ($flag)) {
                    $member_info = self::getInfoByMember($application['id'],$verification['status']);

                    $verification_model = new Verification();
                    $verification_model->assigned_to = Yii::$app->user->getId();
                    $verification_model->application_id = $application['id'];
                    $verification_model->save();

                    $member_info = array_merge($member_info,MemberHelper::getMemberProfileImage($member_info['id']));
                    $data['members'][] = $member_info;
                    $counter++;
                }
            }
        }

        return $data;
    }
    public static function getUtilityBill($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'applications', 'image_type' => 'utility_bill']);
        return $image;
    }
    public static function getDocument($id)
    {
        $image = Images::find()->where(['parent_id' => $id, 'parent_type' => 'applications'])->all();
        return $image;
    }
    public static function getDocumentsTitle()
    {
        $doc_title =[];
        $documents = Documents::find()->all();
        foreach ($documents as $document)
        {
            $doc_title[$document->name] = $document->title;
        }
        return $doc_title;
    }

    public static function getMarraigeCertificate($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'applications', 'image_type' => 'marriage_certificate']);
        return $image;
    }

    public static function getTevtaCertificate($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'applications', 'image_type' => 'tevta_certificate']);
        return $image;
    }

    public static function getProjectDetail($id, $project_table)
    {
        $data = [];
        $keys = ArrayHelper::map(DBHelper::getTableColumns($project_table),'id','id');
        $excluded_columns = ['id','application_id','loan_id','assigned_to','created_at','updated_at','created_by','updated_by','is_lock','deleted'];
        $keys = array_diff_key($keys,array_flip($excluded_columns));
        //$keys = DBHelper::excludeColumns($keys);
        $project_table_name = str_replace('_', ' ',$project_table);
        $project_table_name = ucwords($project_table_name);
        $project_table_name = str_replace(' ', '',$project_table_name);

        $class = 'common\models\\' . $project_table_name;
        $project_model = $class::find()->where(['application_id' => $id, 'deleted' => 0])->one();
        if(isset($project_model)) {
            $data['data'] = ApiParser::parseProjectDetail($keys, $project_model);
            if($project_table == 'projects_tevta') {
                $data['data']['tevta_certificate'] = ApiParser::parseImage(self::getTevtaCertificate($id), $id);
            }
            $data['keys'] = $keys;
        }

        return $data;
    }
//    public static function getIsUrban()
//    {
//        $religions = array(0=>'Rural',1=>'Urban');
//        return $religions;
//    }
//    public static function getAppStatus()
//    {
//        $status = array('pending'=>'Pending','approved'=>'Approved','rejected'=>'Rejected');
//        return $status;
//    }

    public static function getApplicationdetailsTest($application)
    {

        $data = ApiParser::parseApplication($application) ;

        if(isset($application->project_table) && !empty($application->project_table))
        {
            $project_details = ApplicationHelper::getProjectDetail($application->id,$application->project_table);
            $data = !empty($project_details['data'])?array_merge($data,$project_details['data']):$data;
        }
        $response['application'] = $data;
        $documents_list = ViewFormHelper::getDocumentsById($application->project_id);
        $doc = [];
        if(isset($documents_list)) {
            foreach ($documents_list as $document) {
                $doc[] = ['file_type' => $document, 'file_address' => ApiParser::parseImage(Images::findOne(['parent_id' => $application->id, 'parent_type' => 'applications', 'image_type' => $document]), $application->id)];
            }
        }
        /*$doc['utility_bill'] = ApiParser::parseImage(ApplicationHelper::getUtilityBill($application->id), $application->id);
        $doc['marriage_certificate'] = ApiParser::parseImage(ApplicationHelper::getMarraigeCertificate($application->id), $application->id);*/
        $response['documents'] = $doc;
        $response['member'] =  ApiParser::parseMemberAll($application->member);
        $response['member_documents'] =  MemberHelper::getMemberDocuments($application->member->id);

        return $response;
    }

    public static function getApplicationdetails($application)
    {

        $data = ApiParser::parseApplication($application) ;

        if(isset($application->project_table) && !empty($application->project_table))
        {
            $project_details = ApplicationHelper::getProjectDetail($application->id,$application->project_table);
            $data = !empty($project_details['data'])?array_merge($data,$project_details['data']):$data;
        }
        $response['application'] = $data;
        $documents_list = ViewFormHelper::getDocumentsById($application->project_id);
        $doc = [];
        if(isset($documents_list)) {
            foreach ($documents_list as $document) {
                $doc[] = ['file_type' => $document, 'file_address' => ApiParser::parseImage(Images::findOne(['parent_id' => $application->id, 'parent_type' => 'applications', 'image_type' => $document]), $application->id)];
            }
        }
        /*$doc['utility_bill'] = ApiParser::parseImage(ApplicationHelper::getUtilityBill($application->id), $application->id);
        $doc['marriage_certificate'] = ApiParser::parseImage(ApplicationHelper::getMarraigeCertificate($application->id), $application->id);*/
        $response['documents'] = $doc;
        $response['member'] =  ApiParser::parseMemberAll($application->member);
        $response['member_documents'] =  MemberHelper::getMemberDocuments($application->member->id);

        return $response;
    }
    public static function validateReqAmount($id)
    {
        $project_limit=Projects::find()->select('loan_amount_limit')->where(['id'=>$id])->one();
        return $project_limit;
    }
    public static function preConditionsApplication($model){
        $member= Members::findOne(['id'=>$model->member_id]);

        /*$info=MemberInfo::find()->where(['member_id'=>$member->id])->one();
        if(empty($info)){
            $model->addError('member_id', 'Member inforamtion not completed.Update member before proceed furthure. '.$member->cnic);
        }*/
        //if(in_array($model->project_id,StructureHelper::trancheProjects()) || in_array($model->project_id,[60])){
            if(empty($member->membersAccount)){
                $model->addError('member_id', 'Member do not have any active account no.Update member before proceed furthure. '.$member->cnic);
            }
        //}
        $member_image_front = Images::find()->where(['parent_id' => $member->id,'image_type'=>'cnic_front'])->one();
        $member_image_back = Images::find()->where(['parent_id' => $member->id,'image_type'=>'cnic_back'])->one();
        $profile_image = Images::find()->where(['parent_id' => $member->id,'image_type'=>'profile_pic'])->one();
        if ((empty($profile_image) || empty($member_image_front) || empty($member_image_back))){
            $model->addError('member_id', 'Memebrs Documents Missing.Update member before proceed furthure. '.$member->cnic);
        }

        if(in_array($model->project_id,StructureHelper::trancheProjects()) && $member->platform == 1){
            if(empty($member->family_member_name) || empty($member->family_member_cnic)){
                $model->addError('member_id', 'Family Member inforamtion not completed.Update member before proceed furthure. '.$member->cnic);
            }
        }
        foreach($member->applications as $app){
            if(($app->status=='pending' || $app->status=='approved') && empty($app->loan) && $app->deleted=='0'){
                $model->addError('member_id', 'Application against this member is already in-process against Application Number:' . $app->application_no);
            }
            /*else if(!empty($app->loan) && !in_array($model->project_id,StructureHelper::withoutCheckProjects()) && $app->loan->status!='loan completed' && $app->loan->status!='rejected' && $app->loan->status!='not collected' && !in_array($app->loan->project_id,StructureHelper::withoutCheckProjects())){

                $model->addError('member_id', 'Already have active loan against this member with Sanction Number: '.$app->loan->sanction_no);
            }*/
        }
        if(isset($model->other_cnic) && $model->other_cnic==$member->cnic){
            $model->addError('member_id', 'Member and other cnic are same');
        }
        return $model;
    }

    public static function preConditionsApplicationWithRejectedStatus($model){
        $member= Members::findOne(['id'=>$model->member_id]);
        if(!empty($member->applications)) {
            foreach ($member->applications as $app) {
                /*if (($app->status == 'pending' || $app->status == 'approved') && empty($app->loan) && $app->deleted == '0') {
                    //$model->addError('member_id', 'Application against this member is already in-process against Application Number:' . $app->application_no);
                    $model->status = 'rejected';
                    $model->reject_reason = 'Application against this member is already in-process against Application Number:' . $app->application_no;
                } else*/ if (!empty($app->loan) && $app->loan->status != 'loan completed' && $app->loan->status !='rejected' && $model->project_id!=98) {
                    //$model->addError('member_id', 'Already have active loan against this member with Sanction Number: '.$app->loan->sanction_no);
                    $model->status = 'rejected';
                    $model->reject_reason = 'Already have active loan against this member with Sanction Number: ' . $app->loan->sanction_no;

                }
            }
/*        }

        $guarantors= Guarantors::find()->where(['cnic'=>$member->cnic])->one();
        if(!empty($guarantors) && $guarantors!=null){
            $activeLoanGuarantor = Loans::find()
                ->innerJoin('applications', 'applications.id=loans.application_id')
                ->innerJoin('guarantors', 'guarantors.group_id=applications.group_id')
                ->where(['guarantors.cnic' => $guarantors->cnic])
                ->andWhere(['in', 'loans.status', ['collected', 'not collected', 'pending']])
                ->exists();

            if ($activeLoanGuarantor) {
                $activeLoanGuarantorData = Loans::find()
                    ->innerJoin('applications', 'applications.id=loans.application_id')
                    ->innerJoin('guarantors', 'guarantors.group_id=applications.group_id')
                    ->where(['guarantors.cnic' => $guarantors->cnic])
                    ->andWhere(['in', 'loans.status', ['collected', 'not collected', 'pending']])
                    ->one();
                $model->status = 'rejected';
                $model->reject_reason = 'Member is already guarantor of another cnic: '.$activeLoanGuarantorData->application->member->cnic;
            }

            $guarantorLoanAppIds = Loans::find()
                ->select('applications.id')
                ->innerJoin('applications', 'applications.id = loans.application_id')
                ->innerJoin('guarantors', 'guarantors.group_id = applications.group_id')
                ->where(['guarantors.cnic' => $guarantors->cnic])
                ->column();

            $guarantorLoanAppIds = array_map('intval', $guarantorLoanAppIds);

            $queryApplication = Applications::find()
                ->select(['applications.id'])
                ->innerJoin('guarantors', 'guarantors.group_id = applications.group_id')
                ->where(['guarantors.cnic' => $guarantors->cnic])
                ->andWhere(['in', 'applications.status', ['approved']]);

            if (!empty($guarantorLoanAppIds)) {
                $queryApplication->andWhere(['not in', 'applications.id', $guarantorLoanAppIds]);
            }

            $activeAppGuarantor = $queryApplication->one();

            if (!empty($activeAppGuarantor) && $activeAppGuarantor!=null && $activeAppGuarantor->id!=$model->id) {
                $model->status = 'rejected';
                $model->reject_reason = 'Member is already guarantor of another cnic: ' . $activeAppGuarantor->member->cnic;
            }*/
        }

        if ($member->info->cnic_expiry_date <= $model->application_date) {
            if(!is_null($member->info->cnic_expiry_date)) {
                $model->status = 'rejected';
                $model->reject_reason = 'Member CNIC Expired as : ' . $app->memberInfo->cnic_expiry_date;
            }
        }

        if(!empty($member->family_member_cnic)){
            $family_member_cnic_check=MemberHelper::checkActiveLoan($member->family_member_cnic);
            if((!empty($family_member_cnic_check)) && !in_array($model->project_id,[59,60,98])){
                $model->status='rejected';
                $model->reject_reason="Already have active loan against family member cnic against sanction no: ".$family_member_cnic_check->sanction_no;
                //$model->addError('family_member_cnic',"Already have active loan against family member cnic");
            }
            $family_member_cnic=MemberHelper::checkActiveLoanFamilyMember($member->family_member_cnic);
            if((!empty($family_member_cnic)) && !in_array($model->project_id,[59,60,98])){
                $model->status='rejected';
                $model->reject_reason="Family member cnic already exists against active loan against sanction no: ".$family_member_cnic->sanction_no;
                //$model->addError('family_member_cnic',"Family member cnic already exists against active loan");
            }

        $family_member_cnic=MemberHelper::checkProcessAppFamilyMember($member->family_member_cnic);
        if((!empty($family_member_cnic)) && !in_array($model->project_id,[59,60,98])){
            $model->status='rejected';
            $model->reject_reason="Family member cnic already exists against Inprocess Application against member cnic: ".$family_member_cnic->member->cnic;
            //$model->addError('family_member_cnic',"Family member cnic already exists against active loan");
        }

            $blacklist_member = BlacklistHelper::checkBlacklist($member->family_member_cnic);
            if(!empty($blacklist_member)){
                $model->status='rejected';
                $model->reject_reason="family member's cnic is in black list.(".$blacklist_member->reason.")";
            }
        }else{
            $blacklist_family_member_journal = BlacklistHelper::checksJournalBlacklist($member,'family',$model->branch_id);
            if(!empty($blacklist_family_member_journal)){
                $model->status='rejected';
                $model->reject_reason="family member's name is in black list.()";
            }
        }
        $parentage = trim($model->parentage);
        if(!empty($parentage)){
            $blacklist_family_member_journal = BlacklistHelper::checksJournalBlacklist($member,'parentage',$model->branch_id);
            if(!empty($blacklist_family_member_journal)){
                $model->status='rejected';
                $model->reject_reason="member's parentage name is in black list.()";
            }
        }

        $family_member_cnic_=MemberHelper::checkActiveLoanFamilyMember($member->cnic);
        if(!empty($family_member_cnic_) && !in_array($model->project_id,[59,60,98])){
            $model->status='rejected';
            $model->reject_reason="This cnic already exists against a family member cnic of active loan: ".$family_member_cnic_->sanction_no;
            //$model->addError('cnic',"This cnic already exists against a family member cnic of active loan");
        }
        $family_member_cnic_=MemberHelper::checkProcessAppFamilyMember($member->cnic);
        if(!empty($family_member_cnic_) && !in_array($model->project_id,[59,60,98])){
        $model->status='rejected';
        $model->reject_reason="This cnic already exists against a family member cnic of Inprocess Application: ".$family_member_cnic_->member->cnic;
    //$model->addError('cnic',"This cnic already exists against a family member cnic of active loan");
}

        $min = strtotime('+18 years', $member->dob);
        if(in_array($model->project_id,[1,2,10])){
            $max = strtotime('+62 years', $member->dob);
        }else{
            $max = strtotime('+60 years', $member->dob);
        }

        if($model->project_id == 5){
            $today = new \DateTime();
            $birthDate = new \DateTime(date('Y-m-d',$member->dob));
            $age = $today->diff($birthDate)->y;

            if ($age < 17) {
                $model->status='rejected';
                $model->reject_reason="member age is less then minimum limit of 18 years.";
            }

            if ($age > 39) {
                $model->status='rejected';
                $model->reject_reason="member age is greater then maximum limit of 40 years.";
            }

        }

        if(in_array($model->project_id,[132])){
            $today = new \DateTime();
            $birthDate = new \DateTime(date('Y-m-d',$member->dob));
            $age = $today->diff($birthDate)->y;

            if($age < 17)  {
                $model->status='rejected';
                $model->reject_reason="member age is less then minimum limit of 18 years.";
            }

            if($age > 74){
                $model->status='rejected';
                $model->reject_reason="member age is greater then maximum limit of 75 years.";
            }
        }else{
            if(time() < $min)  {
                $model->status='rejected';
                $model->reject_reason="member age is less then minimum limit of 18 years.";
                //$member->addError('dob','Your age is less then minimum limit of 18 years.');
            }
            else if(!empty($max) && time() > $max){
                $model->status='rejected';
                $model->reject_reason="member age is greater then maximam limit of 62 years.";
                //$member->addError('dob','Your age is greater then maximam limit of 62 years.');
            }
        }

        $blacklist_member = BlacklistHelper::checkBlacklist($member->cnic);
        if(!empty($blacklist_member)){
            $model->status='rejected';
            $model->reject_reason="member cnic is in black list.(".$blacklist_member->reason.')';
        }
//        else{
//            $blacklist_member_journal = BlacklistHelper::checksJournalBlacklist($member,'member',$model->branch_id);
//            if(!empty($blacklist_member_journal)){
//                $model->status='rejected';
//                $model->reject_reason="member's name is in black list.(".$blacklist_member->reason.")";
//            }
//        }
        if(!empty($model->who_will_work) && $model->who_will_work!='' && $model->who_will_work!='self'){
            $blacklist_member = BlacklistHelper::checkBlacklist($model->other_cnic);
            if(!empty($blacklist_member)){
                $model->status='rejected';
                $model->reject_reason="other's cnic is in black list.(".$blacklist_member->reason.')';
            }
        }else{
            $blacklist_other_member_journal = BlacklistHelper::checksJournalBlacklist($model,'other',$model->branch_id);
            if(!empty($blacklist_other_member_journal)){
                $model->status='rejected';
                $model->reject_reason="member's Coworker name is in black list.(".$blacklist_member->reason.")";
            }

        }

        $config_rule=ConfigRules::find()->where(['key'=>'no_of_times','group'=>'applications','parent_type'=>'project','parent_id'=>$model->project_id])->one();
        if(!empty($config_rule)){
            $loan_count=Loans::find()
                ->innerJoin('applications','loans.application_id=applications.id')
                ->innerJoin('members','members.id=applications.member_id')
                ->andWhere(['loans.deleted'=>0])
                ->andWhere(['loans.project_id'=>$model->project_id])
                ->andWhere(['members.cnic'=>$member->cnic])
                ->andWhere(['in','loans.status',['collected','loan completed','rejected']])
                ->count();
            $loan_count=isset($loan_count)?$loan_count:0;
            if($loan_count>=$config_rule->value){
                $model->status='rejected';
                $model->reject_reason="Member has already availed loans against this project .(".$loan_count.' times)';
            }
        }
        if($model->project_id == 1) {
            $psic_application = Applications::find()->where(['member_id' => $model->member_id, 'project_id' => 1, 'status' => 'approved'])->orderBy('id desc')->one();
            if (isset($psic_application) && !empty($psic_application)) {


                $first = new \DateTime(date('Y-m-d',($psic_application->loan->date_disbursed)));
                $secnd = new \DateTime(date('Y-m-d',strtotime($model->application_date)));
                $diff = $first->diff($secnd);


                /*if ($psic_application->loan->date_disbursed > strtotime("-1 year")) {*/
                if($diff->days < 365){
                    $model->status = 'rejected';
                    $model->reject_reason = "Member has already availed loans against this project in last year.";
                }
            }
        }
        if($model->project_id == 71) {
            $sibd_application = Applications::find()->where(['member_id' => $model->member_id,/* 'project_id' => 71,*/ 'status' => 'approved'])->andWhere(['in' ,'project_id', [18,71]])->orderBy('id desc')->one();
            if (isset($sibd_application) && !empty($sibd_application)) {

                $first = new \DateTime(date('Y-m-d',($sibd_application->loan->date_disbursed)));
                $secnd = new \DateTime(date('Y-m-d',strtotime($model->application_date)));
                $diff = $first->diff($secnd);

                if($diff->days < 365){
               /* if ($sibd_application->loan->date_disbursed > strtotime("-1 year")) {*/
                    $model->status = 'rejected';
                    $model->reject_reason = "Member has already availed loans against this project in last year.";
                }
            }
        }

        $dateOfBirth = date('Y-m-d',$model->member->dob);
        $now = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($now));

//      echo "your current age is ".$diff->format('%y')." Years ".$diff->format('%m')." months ".$diff->format('%d')." days";

        if(in_array($model->project_id,[105,106,132,134])) {
            if($model->project_id == 132)
            {
                if ($diff->format('%y') > 74 || $diff->format('%y') < 18) {
                    $model->status = 'rejected';
                    $model->reject_reason = 'Member Age should be in reference  between 18 and 75!';
                }
            }elseif($model->project_id == 134){
                if ($diff->format('%y') > 34 || $diff->format('%y') < 18) {
                    $model->status = 'rejected';
                    $model->reject_reason = 'Member Age should be in reference  between 18 and 35!';
                }
            }else{
                if ($diff->format('%y') > 44 || $diff->format('%y') < 21) {
                    $model->status = 'rejected';
                    $model->reject_reason = 'Member Age should be in reference  between 21 and 45!';
                }
            }

        }

        return $model;
    }

    public static function preConditionsApplicationUpdateRejectedStatus($model){
        $member= Members::findOne(['id'=>$model->member_id]);
        if(!empty($member->applications)) {
            foreach ($member->applications as $app) {
               if ($model->id!=$app->id && !empty($app->loan) && $app->loan->status != 'loan completed' && $app->loan->status != 'not collected' && $app->loan->status !='rejected' && $model->project_id!=98) {
                    $model->status = 'rejected';
                    $model->reject_reason = 'Already have active loan against this member with Sanction Number: ' . $app->loan->sanction_no;

                }
            }
        }

        if ($member->info->cnic_expiry_date <= $model->application_date) {
            if(!is_null($member->info->cnic_expiry_date)) {
                $model->status = 'rejected';
                $model->reject_reason = 'Member CNIC Expired as : ' . $app->memberInfo->cnic_expiry_date;
            }
        }

        if(!empty($member->family_member_cnic)){
            $family_member_cnic_check=MemberHelper::checkActiveLoan($member->family_member_cnic);
            if((!empty($family_member_cnic_check)) && !in_array($model->project_id,[59,60,98])){
                $model->status='rejected';
                $model->reject_reason="Already have active loan against family member cnic against sanction no: ".$family_member_cnic_check->sanction_no;
                //$model->addError('family_member_cnic',"Already have active loan against family member cnic");
            }
            $family_member_cnic=MemberHelper::checkActiveLoanFamilyMember($member->family_member_cnic);
            if((!empty($family_member_cnic)) && !in_array($model->project_id,[59,60,98])){
                $model->status='rejected';
                $model->reject_reason="Family member cnic already exists against active loan against sanction no: ".$family_member_cnic->sanction_no;
                //$model->addError('family_member_cnic',"Family member cnic already exists against active loan");
            }

            $family_member_cnic=MemberHelper::checkProcessAppFamilyMember($member->family_member_cnic);
            if($model->id!=$family_member_cnic->id && (!empty($family_member_cnic)) && !in_array($model->project_id,[59,60,98])){
                $model->status='rejected';
                $model->reject_reason="Family member cnic already exists against Inprocess Application against member cnic: ".$family_member_cnic->member->cnic;
                //$model->addError('family_member_cnic',"Family member cnic already exists against active loan");
            }

            $blacklist_member = BlacklistHelper::checkBlacklist($member->family_member_cnic);
            if(!empty($blacklist_member)){
                $model->status='rejected';
                $model->reject_reason="family member's cnic is in black list.(".$blacklist_member->reason.")";
            }
        }else{
            $blacklist_family_member_journal = BlacklistHelper::checksJournalBlacklist($member,'family',$model->branch_id);
            if(!empty($blacklist_family_member_journal)){
                $model->status='rejected';
                $model->reject_reason="family member's name is in black list.()";
            }
        }
        $parentage = trim($model->parentage);
        if(!empty($parentage)){
            $blacklist_family_member_journal = BlacklistHelper::checksJournalBlacklist($member,'parentage',$model->branch_id);
            if(!empty($blacklist_family_member_journal)){
                $model->status='rejected';
                $model->reject_reason="member's parentage name is in black list.()";
            }
        }

        $family_member_cnic_=MemberHelper::checkActiveLoanFamilyMember($member->cnic);
        if(!empty($family_member_cnic_) && !in_array($model->project_id,[59,60,98])){
            $model->status='rejected';
            $model->reject_reason="This cnic already exists against a family member cnic of active loan: ".$family_member_cnic_->sanction_no;
            //$model->addError('cnic',"This cnic already exists against a family member cnic of active loan");
        }
        $family_member_cnic_=MemberHelper::checkProcessAppFamilyMember($member->cnic);
        if(!empty($family_member_cnic_) && !in_array($model->project_id,[59,60,98])){
            $model->status='rejected';
            $model->reject_reason="This cnic already exists against a family member cnic of Inprocess Application: ".$family_member_cnic_->member->cnic;
            //$model->addError('cnic',"This cnic already exists against a family member cnic of active loan");
        }

        $min = strtotime('+18 years', $member->dob);
        if(in_array($model->project_id,[1,2,10])){
            $max = strtotime('+62 years', $member->dob);
        }else{
            $max = strtotime('+60 years', $member->dob);
        }

        if($model->project_id == 5){
            $today = new \DateTime();
            $birthDate = new \DateTime(date('Y-m-d',$member->dob));
            $age = $today->diff($birthDate)->y;

            if ($age < 17) {
                $model->status='rejected';
                $model->reject_reason="member age is less then minimum limit of 18 years.";
            }

            if ($age > 39) {
                $model->status='rejected';
                $model->reject_reason="member age is greater then maximum limit of 40 years.";
            }

        }

        if(in_array($model->project_id,[132])){
            $today = new \DateTime();
            $birthDate = new \DateTime(date('Y-m-d',$member->dob));
            $age = $today->diff($birthDate)->y;

            if($age < 17)  {
                $model->status='rejected';
                $model->reject_reason="member age is less then minimum limit of 18 years.";
            }

            if($age > 74){
                $model->status='rejected';
                $model->reject_reason="member age is greater then maximum limit of 75 years.";
            }
        }else{
            if(time() < $min)  {
                $model->status='rejected';
                $model->reject_reason="member age is less then minimum limit of 18 years.";
                //$member->addError('dob','Your age is less then minimum limit of 18 years.');
            }
            else if(!empty($max) && time() > $max){
                $model->status='rejected';
                $model->reject_reason="member age is greater then maximam limit of 62 years.";
                //$member->addError('dob','Your age is greater then maximam limit of 62 years.');
            }
        }

        $blacklist_member = BlacklistHelper::checkBlacklist($member->cnic);
        if(!empty($blacklist_member)){
            $model->status='rejected';
            $model->reject_reason="member cnic is in black list.(".$blacklist_member->reason.')';
        }

        if(!empty($model->who_will_work) && $model->who_will_work!='' && $model->who_will_work!='self'){
            $blacklist_member = BlacklistHelper::checkBlacklist($model->other_cnic);
            if(!empty($blacklist_member)){
                $model->status='rejected';
                $model->reject_reason="other's cnic is in black list.(".$blacklist_member->reason.')';
            }
        }else{
            $blacklist_other_member_journal = BlacklistHelper::checksJournalBlacklist($model,'other',$model->branch_id);
            if(!empty($blacklist_other_member_journal)){
                $model->status='rejected';
                $model->reject_reason="member's Coworker name is in black list.(".$blacklist_member->reason.")";
            }

        }

        $config_rule=ConfigRules::find()->where(['key'=>'no_of_times','group'=>'applications','parent_type'=>'project','parent_id'=>$model->project_id])->one();
        if(!empty($config_rule)){
            $loan_count=Loans::find()
                ->innerJoin('applications','loans.application_id=applications.id')
                ->innerJoin('members','members.id=applications.member_id')
                ->andWhere(['loans.deleted'=>0])
                ->andWhere(['loans.project_id'=>$model->project_id])
                ->andWhere(['members.cnic'=>$member->cnic])
                ->andWhere(['in','loans.status',['collected','loan completed','rejected']])
                ->count();
            $loan_count=isset($loan_count)?$loan_count:0;
            if($loan_count>=$config_rule->value){
                $model->status='rejected';
                $model->reject_reason="Member has already availed loans against this project .(".$loan_count.' times)';
            }
        }
        if($model->project_id == 1) {
            $psic_application = Applications::find()->where(['member_id' => $model->member_id, 'project_id' => 1, 'status' => 'approved'])->orderBy('id desc')->one();
            if (isset($psic_application) && !empty($psic_application)) {


                $first = new \DateTime(date('Y-m-d',($psic_application->loan->date_disbursed)));
                $secnd = new \DateTime(date('Y-m-d',strtotime($model->application_date)));
                $diff = $first->diff($secnd);


                /*if ($psic_application->loan->date_disbursed > strtotime("-1 year")) {*/
                if($diff->days < 365){
                    $model->status = 'rejected';
                    $model->reject_reason = "Member has already availed loans against this project in last year.";
                }
            }
        }
        if($model->project_id == 71) {
            $sibd_application = Applications::find()->where(['member_id' => $model->member_id,/* 'project_id' => 71,*/ 'status' => 'approved'])->andWhere(['in' ,'project_id', [18,71]])->orderBy('id desc')->one();
            if (isset($sibd_application) && !empty($sibd_application)) {

                $first = new \DateTime(date('Y-m-d',($sibd_application->loan->date_disbursed)));
                $secnd = new \DateTime(date('Y-m-d',strtotime($model->application_date)));
                $diff = $first->diff($secnd);

                if($diff->days < 365){
                    /* if ($sibd_application->loan->date_disbursed > strtotime("-1 year")) {*/
                    $model->status = 'rejected';
                    $model->reject_reason = "Member has already availed loans against this project in last year.";
                }
            }
        }

        $dateOfBirth = date('Y-m-d',$model->member->dob);
        $now = date("Y-m-d");
        $diff = date_diff(date_create($dateOfBirth), date_create($now));

        if(in_array($model->project_id,[105,106,132,134])) {
            if($model->project_id == 132)
            {
                if ($diff->format('%y') > 74 || $diff->format('%y') < 18) {
                    $model->status = 'rejected';
                    $model->reject_reason = 'Member Age should be in reference  between 18 and 75!';
                }
            }elseif($model->project_id == 134){
                if ($diff->format('%y') > 34 || $diff->format('%y') < 18) {
                    $model->status = 'rejected';
                    $model->reject_reason = 'Member Age should be in reference  between 18 and 35!';
                }
            }else{
                if ($diff->format('%y') > 44 || $diff->format('%y') < 21) {
                    $model->status = 'rejected';
                    $model->reject_reason = 'Member Age should be in reference  between 21 and 45!';
                }
            }

        }

        return $model;
    }
}