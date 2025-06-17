<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 14/09/17
 * Time: 11:19 AM
 */

namespace common\components\Parsers\ReportsParser;

use common\components\Helpers\ImageHelper;
use common\components\Helpers\ProgressReportHelper;
use common\components\Helpers\StructureHelper;
use common\models\BranchProjectsMapping;
use common\models\ProgressReportDetails;
use common\models\ProgressReports;
use Yii;
use common\models\Areas;
use common\models\Branches;
use yii\helpers\Url;
use common\models\Users;
use common\components\Helpers\ReportsHelper\NumberHelper;

class ApiParser
{

    public static function parseUser($user)
    {
        if (!empty($user)) {
            $user_image = (!empty($user->image)) ? ($user->image) : 'noimage.png';
           // $pic_url = Url::to('@web/uploads/users/' . $user_image, true);

            $pic_url =ImageHelper::getAttachmentApiPath(). '?type=users&file_name=' . $user_image .'&download=true';
            $prompt = false;
            //if($user->password_hash == '$2y$13$r9ltzA8ACuDRF6M1p0dK3OS3cAzShkO8btm4/KG6gbeiGBeGTF.Gq'){
            if(empty($user->password_hash) || ($user->password_hash == NULL) || Yii::$app->security->validatePassword('abc@123', $user->password_hash)){
                $prompt = true;
            }
            $user_array = array(
                'user_id' => $user->id,
                'username' => $user->username,
                'full_name' => $user->fullname,
                'role' => $user->role->item_name,
                'email' => $user->email,
                'region_id' => isset($user->region) ?$user->region->obj_id : 0,
                'area_id' => isset($user->area) ? $user->area->obj_id : 0,
                'branch_id' => isset($user->branch) ? $user->branch->obj_id : 0,
                'password' => $user->password,
                'access_token' => $user->last_login_token,
                //'pic_url' => 'http://icons.iconarchive.com/icons/custom-icon-design/pretty-office-2/256/man-icon.png',
                'pic_url' => $pic_url,
                'prompt' => $prompt,
            );
            return $user_array;
        }

        return $user;
    }

    public static function parseRegion($region)
    {

        if (!empty($region)) {
            $user_array = array(
                'region_id' => $region->id,
                'region_name' => $region->name,
                'code' => $region->code,
                'division' => $region->cr_division_id,
                'tags' => $region->tags,
            );
            return $user_array;
        }

        return $region;
    }

    public static function parseArea($area)
    {

        if (!empty($area)) {
            $array = array(
                'area_id' => $area->id,
                'area_name' => $area->name,
                'code' => $area->code,
                //'region_id' => isset($area->region->name)?($area->region->name):"",
                'region_id' => $area->region_id,
                'tags' => $area->tags,
            );
            return $array;
        }

        return $area;
    }

    public static function parseBranch($branch)
    {
        $progress= ProgressReports::find()->where(['project_id'=>0,'status'=>1,'is_verified'=>1])->orderBy('id desc')->one();
        $detail=ProgressReportDetails::find()->where(['branch_id'=>$branch->id,'progress_report_id'=>$progress->id])->one();
        if (!empty($branch)/* && !(in_array($branch->code,StructureHelper::closedBranches()) && $detail->active_loans==0)*/) {

            $array = array(
                'branch_id' => $branch->id,
                //'area_id'       => isset($branch->area->name)? $branch->area->name : "",
                'area_id' => $branch->area_id,
                'region_id' => isset($branch->region_id) ? $branch->region_id : "",
                //'region_id'     => isset($branch->region->name)?($branch->region->name):"",
                'branch_name' => $branch->name,
                'short_name' => $branch->short_name,
                'code' => $branch->code,
                'district' => $branch->district_id,
                'country' => $branch->region_id,
                'province' => $branch->province_id,
                'tehsil' => $branch->region_id,
                'latitude' => isset($branch->latitude) ? $branch->latitude : '0.0',
                'longitude' => isset($branch->longitude) ? $branch->longitude : '0.0',
                //'tags' => 'Central,Lahore2,Pakistan,Punjab,Lahore',
                'tags' => self::getBranchTags($branch),
                //'status' =>($detail->active_loans==0)?0:1,
                'status' =>in_array($branch->code,StructureHelper::closedBranches())?0:1,
            );
            return $array;
        }

        return $branch;
    }

    public static function parseProgress($progress)
    {
        /*print_r($progress);
        die();*/
        if (!empty($progress)) {
            $no_of_loans = $progress['no_of_loans'];
            if ($no_of_loans == 0) {
                $family_loans = 0;
                $female_loans = 0;
            } else {
                $family_loans = $progress['family_loans'] / $progress['no_of_loans'] * 100;
                $female_loans = $progress['female_loans'] / $progress['no_of_loans'] * 100;
            }
            return [
                [
                    'key' => 'Total Loans',
                    'value' => NumberHelper::getFormattedNumber($progress['no_of_loans']),
                    'percentage' => [
                        ['title' => 'Male', 'value' => round($family_loans) . '%'],
                        ['title' => 'Female', 'value' => round($female_loans) . '%'],
                    ]
                ],
                ['key' => 'Total Beneficiaries', 'value' => NumberHelper::getFormattedNumber($progress['no_of_beneficiaries'])],
                ['key' => 'Total Disbursement', 'value' => 'Rs.' . NumberHelper::getFormattedNumberAmount($progress['cum_disb'])],
                //['key'=> 'Cum Due', 'value'=>NumberHelper::getFormattedNumber($progress['cum_due'],'m').' Millions'],
                //['key'=> 'Overdue', 'value'=>NumberHelper::getFormattedNumber($progress['overdue_borrowers'])],
                //['key'=> 'Overdue Amount', 'value'=>NumberHelper::getFormattedNumber($progress['overdue_amount'],'m').' Millions'],
                //['key'=> 'Overdue %', 'value'=>$progress['overdue_percentage']],
                //['key'=> 'PAR Amount', 'value'=>NumberHelper::getFormattedNumber($progress['par_amount']) ? $progress['par_amount'] : 0],
                //['key'=> 'PAR %', 'value'=>$progress['par_percentage']],
                //['key'=> 'Not yet Due', 'value'=>NumberHelper::getFormattedNumber($progress['not_yet_due'],'m').' Millions'],
                ['key' => 'Active Loans', 'value' => isset($progress['active_loans']) ? NumberHelper::getFormattedNumber($progress['active_loans']) : 0],
                ['key' => 'OLP', 'value' => 'Rs.' . NumberHelper::getFormattedNumberAmount($progress['olp_amount'])],
                /*[
                    'key' => 'Total Recovery',
                    'value' => 'Rs.' . NumberHelper::getFormattedNumber($progress['cum_recv'], 'm') . ' m',
                    'percentage' => [
                        ['title' => 'Recovery %', 'value' => round($progress['recovery_percentage'], 2) . '%'],
                    ]
                ],*/
                [
                    'key' => 'Recovery %',
                    'value' => round($progress['recovery_percentage'], 2) . '%',
                    /*'percentage' => [
                        ['title' => 'Recovery %', 'value' => round($progress['recovery_percentage'], 2) . '%'],
                    ]*/
                ],
                [
                    'key' => 'No of Branches',
                    'value' => $progress['branch_id'],
                ],
                //['key'=> 'Total Recovery', 'value'=>NumberHelper::getFormattedNumber($progress['cum_recv'],'m').' Millions'],
                //['key'=> 'Recovery %', 'value'=>round($progress['recovery_percentage'],2).'%'],
                //['key'=> 'Cash in Hand', 'value'=>isset($progress['cih']) ? NumberHelper::getFormattedNumber($progress['cih']) : 0],
                //['key'=> 'MDP', 'value'=>isset($progress['mdp']) ? NumberHelper::getFormattedNumber($progress['mdp']) : 0],
                ['key'=> '', 'value'=>''],
            ];
        }

        return $progress;
    }

    public static function parseProgressWithLastMonthRecovery($progress)
    {
        /*print_r($progress);
        die();*/
        if (!empty($progress)) {
            $no_of_loans = $progress['no_of_loans'];
            if ($no_of_loans == 0) {
                $family_loans = 0;
                $female_loans = 0;
            } else {
                $family_loans = $progress['family_loans'] / $progress['no_of_loans'] * 100;
                $female_loans = $progress['female_loans'] / $progress['no_of_loans'] * 100;
            }
            return [
                [
                    'key' => 'Total Loans',
                    'value' => NumberHelper::getFormattedNumber($progress['no_of_loans']),
                    'percentage' => [
                        ['title' => 'Male', 'value' => round($family_loans) . '%'],
                        ['title' => 'Female', 'value' => round($female_loans) . '%'],
                    ]
                ],
                ['key' => 'Total Beneficiaries', 'value' => NumberHelper::getFormattedNumber($progress['no_of_beneficiaries'])],
                ['key' => 'Total Disbursement', 'value' => 'Rs.' . NumberHelper::getFormattedNumberAmount($progress['cum_disb'])],
                //['key'=> 'Cum Due', 'value'=>NumberHelper::getFormattedNumber($progress['cum_due'],'m').' Millions'],
                //['key'=> 'Overdue', 'value'=>NumberHelper::getFormattedNumber($progress['overdue_borrowers'])],
                //['key'=> 'Overdue Amount', 'value'=>NumberHelper::getFormattedNumber($progress['overdue_amount'],'m').' Millions'],
                //['key'=> 'Overdue %', 'value'=>$progress['overdue_percentage']],
                //['key'=> 'PAR Amount', 'value'=>NumberHelper::getFormattedNumber($progress['par_amount']) ? $progress['par_amount'] : 0],
                //['key'=> 'PAR %', 'value'=>$progress['par_percentage']],
                //['key'=> 'Not yet Due', 'value'=>NumberHelper::getFormattedNumber($progress['not_yet_due'],'m').' Millions'],
                ['key' => 'Active Loans', 'value' => isset($progress['active_loans']) ? NumberHelper::getFormattedNumber($progress['active_loans']) : 0],
                ['key' => 'OLP', 'value' => 'Rs.' . NumberHelper::getFormattedNumberAmount($progress['olp_amount'])],
                /*[
                    'key' => 'Total Recovery',
                    'value' => 'Rs.' . NumberHelper::getFormattedNumber($progress['cum_recv'], 'm') . ' m',
                    'percentage' => [
                        ['title' => 'Recovery %', 'value' => round($progress['recovery_percentage'], 2) . '%'],
                    ]
                ],*/
                /*[
                    'key' => 'Recovery %',
                    'value' => round($progress['recovery_percentage'], 2) . '%',
                    /*'percentage' => [
                        ['title' => 'Recovery %', 'value' => round($progress['recovery_percentage'], 2) . '%'],
                    ]
                ],*/
                [
                    'key' => 'Last Month Recovery %',
                    'value' => round($progress['last_month_recovery_percentage'], 2) . '%',
                    /*'percentage' => [
                        ['title' => 'Recovery %', 'value' => round($progress['recovery_percentage'], 2) . '%'],
                    ]*/
                ],
                [
                    'key' => 'No of Branches',
                    'value' => $progress['branch_id'],
                ],
                [
                    'key' => 'No of Areas',
                    'value' => $progress['area_id'],
                ],
                [
                    'key' => 'No of Regions',
                    'value' => $progress['region_id'],
                ],
                [
                    'key' => 'No of Districts',
                    'value' => $progress['districts'],
                ],
                //['key'=> 'Total Recovery', 'value'=>NumberHelper::getFormattedNumber($progress['cum_recv'],'m').' Millions'],
                //['key'=> 'Recovery %', 'value'=>round($progress['recovery_percentage'],2).'%'],
                //['key'=> 'Cash in Hand', 'value'=>isset($progress['cih']) ? NumberHelper::getFormattedNumber($progress['cih']) : 0],
                //['key'=> 'MDP', 'value'=>isset($progress['mdp']) ? NumberHelper::getFormattedNumber($progress['mdp']) : 0],
                ['key'=> '', 'value'=>''],
            ];
        }

        return $progress;
    }

    public static function parseProjectCharges_($project_charges)
    {
        $total_disbursement = ProgressReportHelper::getTotalDisbursementLastMonth($project_charges->id);
        if($project_charges->sc_type == 'percent') {
            $due_amount = ($total_disbursement/100) * $project_charges->sc_due;
        } else {
            $due_amount = $project_charges->sc_due;
        }
        $service_charges = [
            [
                'key' => 'Project',
                'value' => $project_charges->name
            ],
            [
                'key' => 'Funds',
                'group' => [
                    [
                        'key' => 'Allocated Funds',
                        'value' => isset($project_charges->total_fund) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->total_fund) : '0'
                    ],
                    [
                        'key' => 'Received Funds',
                        'value' => isset($project_charges->fund_received) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->fund_received) : '0'
                    ],
                ]
            ],
            [
                'key' => 'Disbursement',
                'group' => [
                    [
                        'key' => 'Total Disbursement',
                        'value' => 'Rs.' .  NumberHelper::getFormattedNumberAmount($total_disbursement),
                    ]
                ]
            ],
            [
                'key' => 'Service Charges',
                'group' => [
                    [
                        'key' => ($project_charges->sc_type == 'percent')? 'Service Charges @ '.$project_charges->sc_due .'%': 'Service Charges Due',
                        'value' => 'Rs.' . NumberHelper::getFormattedNumberAmount($due_amount)
                    ],
                    [
                        'key' => 'Service Charges Recieved',
                        'value' => isset($project_charges->received_amount) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->received_amount) : '0'
                    ],
                    [
                        'key' => 'Adjustment Made',
                        'value' => isset($project_charges->adjustment_made) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->adjustment_made) : '0'
                    ],
                    [
                        'key' => 'Remaining Service Charges',
                        'value' => 'Rs.' . NumberHelper::getFormattedNumberAmount($due_amount - $project_charges->received_amount  - $project_charges->adjustment_made)
                    ],
                ]
            ],
            [
                'key' => 'Date',
                'group' => [
                    [
                        'key' => 'Service Charges Recieved Till Date',
                        'value' => ($project_charges->received_date != 0) ? date('d M Y',$project_charges->received_date) : ''
                    ],
                    [
                        'key' => 'Date',
                        'value' => date('d M Y',strtotime('last day of previous month'))
                    ],
                ]
            ]
        ];
        return $service_charges;
    }

    public static function parseProjectCharges($project_charges)
    {

        $progress_report = ProgressReportHelper::getProgressReportData($project_charges->id);

        $service_charges = [
            [
                'key' => 'Project',
                'value' => $project_charges->name
            ],
            [
                'key' => 'Funds',
                'group' => [
                    [
                        'key' => 'Total Funds Allocated',
                        'value' => isset($project_charges->total_fund) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->total_fund) : '0'
                    ],
                    [
                        'key' => 'Total Funds Received',
                        'value' => isset($project_charges->fund_received) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->fund_received) : '0'
                    ],
                    [
                        'key' => 'Started Date',
                        'value' => isset($project_charges->started_date)?date('d M, Y',$project_charges->started_date):'Not Set'
                    ],
                    [
                        'key' => 'Period ',
                        'value' =>  isset($project_charges->project_period)?date('d M, Y',$project_charges->project_period):'Not Set'
                    ],
                    [
                        'key' => 'Ending Date',
                        'value' =>  ($project_charges->ending_date > 0)?date('d M, Y',$project_charges->ending_date):'Not Set'
                    ],
                    [
                        'key' => 'Service Charges Rate',
                        'value' => $project_charges->sc_type
                    ],
                ]
            ],
            [
                'key' => 'Disbursement',
                'group' => [
                    [
                        'key' => 'Total Amount Disbursed',
                        'value' => 'Rs.' .  NumberHelper::getFormattedNumberAmount($progress_report['cum_disb']),
                    ],
                    [
                        'key' => 'Outstanding Loan Portfolio',
                        'value' => 'Rs.' .  NumberHelper::getFormattedNumberAmount($progress_report['olp_amount']),
                    ]
                ]
            ],
            [
                'key' => 'Service Charges',
                'group' => [
                    [
                        'key' => 'Total Receivable',
                        'value' => isset($project_charges->serviceCharges->received_amount) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->serviceCharges->received_amount) : '0'
                    ],
                    [
                        'key' => 'Total Received',
                        'value' => isset($project_charges->serviceCharges->remaining_amount) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->serviceCharges->remaining_amount) : '0'
                    ],
                    [
                        'key' => 'Total Pending',
                        'value' => isset($project_charges->serviceCharges->pending_amount) ? 'Rs.' . NumberHelper::getFormattedNumberAmount($project_charges->serviceCharges->pending_amount) : '0'
                    ]
                  ],
            ],
            [
                'key' => 'Date',
                'group' => [
                    [
                        'key' => 'Last Receiving Date',
                        'value' => ($project_charges->serviceCharges->received_date > 0) ? date('d M Y',$project_charges->serviceCharges->received_date) : ''
                    ],
                    [
                        'key' => 'Last Request Sent on',
                        'value' => ($project_charges->serviceCharges->request_date > 0) ? date('d M Y',$project_charges->serviceCharges->request_date) : ''
                    ],
                    [
                        'key' => 'Date',
                        'value' => date('d M Y',strtotime('last day of previous month'))
                    ],
                ]
            ]
        ];
        return $service_charges;
    }

    public static function parseStaff($user)
    {
        $staff = array();
        if (!empty($user)) {
            foreach ($user as $u) {
                $user_image = (!empty($u->image)) ? ($u->image) : 'noimage.png';
                $pic_url = ImageHelper::getAttachmentApiPath(). '?type=users&file_name=' . $user_image .'&download=true';
                $staff[] = [
                    'id' => isset($u->id) ? $u->id : '0',
                    'name' => isset($u->fullname) ? $u->fullname : '',
                    'designation' => isset($u->designation->name) ? $u->designation->name : '',
                    'joining_date' => isset($u->joining_date) ? $u->joining_date : '',
                    'phone' => isset($u->mobile) ? '0' . ltrim($u->mobile, '92') : '',
                    'email' => isset($u->email) ? $u->email : '',
                    'img_url' => $pic_url,
                ];
            }
        }
        return $staff;
    }
    public static function parseNetworkStaff($user)
    {
        $staff = array();
        $region = '';
        $area = '';
        if (!empty($user)) {
            foreach ($user as $u) {
                if(in_array($u->designation->code, array('CEO','COO','CFO','CCO','PM','DM','Founder'))){
                    $user_image = (!empty($u->image)) ? ($u->image) : 'noimage.png';
                    $pic_url = ImageHelper::getAttachmentApiPath(). '?type=users&file_name=' . $user_image .'&download=true';
                    $staff['hod_staff'][] = [
                        'id' => isset($u->id) ? $u->id : '0',
                        'name' => isset($u->fullname) ? $u->fullname : '',
                        'designation' => isset($u->designation->name) ? $u->designation->name : '',
                        'joining_date' => isset($u->joining_date) ? $u->joining_date : '',
                        'phone' => isset($u->mobile) ? '0' . ltrim($u->mobile, '92') : '',
                        'email' => isset($u->email) ? $u->email : '',
                        'img_url' => $pic_url,
                        'region' => '',
                        'area' => '',
                    ];
                }else if (in_array($u->designation->code, array('RM','AM'))){
                    $user_image = (!empty($u->image)) ? ($u->image) : 'noimage.png';
                    $pic_url = ImageHelper::getAttachmentApiPath(). '?type=users&file_name=' . $user_image .'&download=true';
                    if($u->designation->code == 'RM'){
                        $region = isset($u->region->userRegion->name) ? $u->region->userRegion->name : '';
                        $area = '';
                    }else if($u->designation->code == 'AM'){
                        $region = isset($u->region->userRegion->name) ? $u->region->userRegion->name : '';
                        $area = isset($u->area->userArea->name) ? $u->area->userArea->name : '';
                    }
                    $staff['field_staff'][] = [
                        'id' => isset($u->id) ? $u->id : '0',
                        'name' => isset($u->fullname) ? $u->fullname : '',
                        'designation' => isset($u->designation->name) ? $u->designation->name : '',
                        'joining_date' => isset($u->joining_date) ? $u->joining_date : '',
                        'phone' => isset($u->mobile) ? '0' . ltrim($u->mobile, '92') : '',
                        'email' => isset($u->email) ? $u->email : '',
                        'img_url' => $pic_url,
                        'region' => $region,
                        'area' => $area,
                    ];
                }

            }
        }
        return $staff;
    }
    public static function parseNetworkRegion($region)
    {

        if (!empty($region)) {
            $project = BranchProjectsMapping::find()->select('branch_projects_mapping.project_id,projects.short_name as name,projects.name as full_name,sector')
                ->innerJoin('projects','projects.id=branch_projects_mapping.project_id')
                ->innerJoin('branches','branches.id=branch_projects_mapping.branch_id')->where(['branches.region_id'=>$region->id])->distinct()->asArray()->all();
            $array = array(
                'type' => 'Region',
                'id' => isset($region->id) ? $region->id : '',
                'name' => isset($region->name) ? $region->name : '',
                'code' => '',
                'short_description' => isset($region->short_description) ? $region->short_description : '',
                'region_id' => isset($region->id) ? $region->id : '',
                'region_name' => isset($region->name) ? $region->name : '',
                'area_id' => '',
                'area_name' => '',
                'phone' => isset($region->mobile) ? $region->mobile : '',
                'opening_date' => isset($region->opening_date) ?(date('d M Y',$region->opening_date)) : '',
                'full_address' => isset($region->full_address) ? $region->full_address : '',
                'latitude' => isset($region->latitude) ? $region->latitude : '0.0',
                'longitude' => isset($region->longitude) ? $region->longitude : '0.0',
                //'projects' => isset($region->projects) ? $region->projects : '',
                'projects' => $project,
                'number_of_areas' => self::getAreasCount($region),
                'number_of_branches' => self::getBranchesCount($region),
                'label_wise_detail' => [
                    [
                        'label' => 'Type',
                        'value' => 'Region',
                    ],
                    [
                        'label' => 'ID',
                        'value' => isset($region->id) ? $region->id : '',
                    ],
                    [
                        'label' => 'Name',
                        'value' => isset($region->name) ? $region->name : '',
                    ],
                    [
                        'label' => 'Code',
                        'value' => '',
                    ],
                    [
                        'label' => 'Short Description',
                        'value' => isset($region->short_description) ? $region->short_description : '',
                    ],
                    [
                        'label' => 'Region ID',
                        'value' => isset($region->id) ? $region->id : '',
                    ],
                    [
                        'label' => 'Region Name',
                        'value' => isset($region->name) ? $region->name : '',
                    ],
                    [
                        'label' => 'Area ID',
                        'value' => '',
                    ],
                    [
                        'label' => 'Area Name',
                        'value' => '',
                    ],
                    [
                        'label' => 'Phone',
                        'value' => isset($region->mobile) ? $region->mobile : '',
                    ],
                    [
                        'label' => 'Opening Date',
                        'value' => isset($region->opening_date) ? (date('d M Y',$region->opening_date)) : '',
                    ],
                    [
                        'label' => 'Full Address',
                        'value' => isset($region->full_address) ? $region->full_address : '',
                    ],
                    [
                        'label' => 'Latitude',
                        'value' => isset($region->latitude) ? $region->latitude : '0.0',
                    ],
                    [
                        'label' => 'Longitude',
                        'value' => isset($region->longitude) ? $region->longitude : '0.0',
                    ],
                    [
                        'label' => 'Projects',
                        'value' => isset($region->projects) ? $region->projects : '',
                    ],
                    [
                        'label' => 'Number of Areas',
                        'value' => self::getAreasCount($region),
                    ],
                    [
                        'label' => 'Number of Branches',
                        'value' => self::getBranchesCount($region),
                    ]
                ]
            );
            return $array;
        }
        return $region;
    }

    public static function parseNetworkArea($area)
    {

        if (!empty($area)) {
            $project = BranchProjectsMapping::find()->select('branch_projects_mapping.project_id,projects.short_name as name,projects.name as full_name,sector')
                ->innerJoin('projects','projects.id=branch_projects_mapping.project_id')
                ->innerJoin('branches','branches.id=branch_projects_mapping.branch_id')->where(['branches.area_id'=>$area->id])->distinct()->asArray()->all();
            $array = array(
                'type' => 'Area',
                'id' => isset($area->id) ? $area->id : '',
                'name' => isset($area->name) ? $area->name : '',
                'code' => '',
                'short_description' => isset($area->short_description) ? $area->short_description : '',
                'region_id' => isset($area->region->id) ? $area->region->id : '',
                'region_name' => isset($area->region->name) ? $area->region->name : '',
                'area_id' => isset($area->id) ? $area->id : '',
                'area_name' => isset($area->name) ? $area->name : '',
                'phone' => isset($area->mobile) ? $area->mobile : '',
                'opening_date' => isset($area->opening_date) ? (date('d M Y',$area->opening_date)) : '',
                'full_address' => isset($area->full_address) ? $area->full_address : '',
                'latitude' => isset($area->latitude) ? $area->latitude : '0.0',
                'longitude' => isset($area->longitude) ? $area->longitude : '0.0',
                //'projects' => isset($area->projects) ? $area->projects : '',
                'projects' => $project,
                'number_of_branches' => self::getBranchesCountByAreas($area),
                'label_wise_detail' => [
                    [
                        'label' => 'Type',
                        'value' => 'Area',
                    ],
                    [
                        'label' => 'ID',
                        'value' => isset($area->id) ? $area->id : '',
                    ],
                    [
                        'label' => 'Name',
                        'value' => isset($area->name) ? $area->name : '',
                    ],
                    [
                        'label' => 'Code',
                        'value' => '',
                    ],
                    [
                        'label' => 'Short Description',
                        'value' => isset($area->short_description) ? $area->short_description : '',
                    ],
                    [
                        'label' => 'Region ID',
                        'value' => isset($area->region->id) ? $area->region->id : '',
                    ],
                    [
                        'label' => 'Region Name',
                        'value' => isset($area->region->name) ? $area->region->name : '',
                    ],
                    [
                        'label' => 'Area ID',
                        'value' => isset($area->id) ? $area->id : '',
                    ],
                    [
                        'label' => 'Area Name',
                        'value' => isset($area->name) ? $area->name : '',
                    ],
                    [
                        'label' => 'Phone',
                        'value' => isset($area->mobile) ? $area->mobile : '',
                    ],
                    [
                        'label' => 'Opening Date',
                        'value' => isset($area->opening_date) ? (date('d M Y',$area->opening_date)) : '',
                    ],
                    [
                        'label' => 'Full Address',
                        'value' => isset($area->full_address) ? $area->full_address : '',
                    ],
                    [
                        'label' => 'Latitude',
                        'value' => isset($area->latitude) ? $area->latitude : '0.0',
                    ],
                    [
                        'label' => 'Longitude',
                        'value' => isset($area->longitude) ? $area->longitude : '0.0',
                    ],
                    [
                        'label' => 'Projects',
                        'value' => isset($area->projects) ? $area->projects : '',
                    ],
                    [
                        'label' => 'Number of Branches',
                        'value' => self::getBranchesCountByAreas($area),
                    ]
                ]
            );
            return $array;
        }
        return $area;
    }

    public static function parseNetworkBranch($branch)
    {

        if (!empty($branch)) {
            $project = BranchProjectsMapping::find()->select('branch_projects_mapping.project_id,short_name as name,name as full_name,sector')->innerJoin('projects','projects.id=branch_projects_mapping.project_id')->where(['branch_id'=>$branch->id])->asArray()->all();
            $array = array(
                'type' => 'Branch',
                'id' => isset($branch->id) ? $branch->id : '',
                'name' => isset($branch->name) ? $branch->name : '',
                'code' => isset($branch->code) ? $branch->code : '',
                'short_description' => self::getBranchTags($branch, true),
                'region_id' => isset($branch->region_id) ? $branch->region_id : '',
                'region_name' => isset($branch->region->name) ? $branch->region->name : '',
                'area_id' => isset($branch->area_id) ? $branch->area_id : '',
                'area_name' => isset($branch->area->name) ? $branch->area->name : '',
                'phone' => isset($branch->mobile) ? $branch->mobile : '',
                'opening_date' => isset($branch->opening_date) ? (date('d M Y',$branch->opening_date)) : '',
                'full_address' => isset($branch->address) ? $branch->address : '',
                'latitude' => isset($branch->latitude) ? $branch->latitude : '0.0',
                'longitude' => isset($branch->longitude) ? $branch->longitude : '0.0',
                'projects' => $project,
                'status' =>in_array($branch->code,StructureHelper::closedBranches())?0:1,
                'label_wise_detail' => [
                    [
                        'label' => 'Type',
                        'value' => 'Branch',
                    ],
                    [
                        'label' => 'ID',
                        'value' => isset($branch->id) ? $branch->id : '',
                    ],
                    [
                        'label' => 'Name',
                        'value' => isset($branch->name) ? $branch->name : '',
                    ],
                    [
                        'label' => 'Code',
                        'value' => isset($branch->code) ? $branch->code : '',
                    ],
                    [
                        'label' => 'Short Description',
                        'value' => self::getBranchTags($branch, true),
                    ],
                    [
                        'label' => 'Region ID',
                        'value' => isset($branch->region_id) ? $branch->region_id : '',
                    ],
                    [
                        'label' => 'Region Name',
                        'value' => isset($branch->region->name) ? $branch->region->name : '',
                    ],
                    [
                        'label' => 'Area ID',
                        'value' => isset($branch->area_id) ? $branch->area_id : '',
                    ],
                    [
                        'label' => 'Area Name',
                        'value' => isset($branch->area->name) ? $branch->area->name : '',
                    ],
                    [
                        'label' => 'Phone',
                        'value' => isset($branch->mobile) ? $branch->mobile : '',
                    ],
                    [
                        'label' => 'Opening Date',
                        'value' => isset($branch->opening_date) ? (date('d M Y',$branch->opening_date)): '',
                    ],
                    [
                        'label' => 'Full Address',
                        'value' => isset($branch->address) ? $branch->address : '',
                    ],
                    [
                        'label' => 'Latitude',
                        'value' => isset($branch->latitude) ? $branch->latitude : '0.0',
                    ],
                    [
                        'label' => 'Longitude',
                        'value' => isset($branch->longitude) ? $branch->longitude : '0.0',
                    ],
                    [
                        'label' => 'Projects',
                        'value' => 'PSIC, TEVTA, DISABLE, KISAAN',
                    ]
                ]
            );
            return $array;
        }

        return $branch;
    }

    public static function parseBranches($branch)
    {
        if (!empty($branch)) {
            $array = array(
                'id' => $branch->id,
                //'area_id'       => isset($branch->area->name)? $branch->area->name : "",
                'area_id' => $branch->area_id,
                'region_id' => isset($branch->region_id) ? $branch->region_id : "",
                //'region_id'     => isset($branch->region->name)?($branch->region->name):"",
                'name' => $branch->name,
                'short_name' => $branch->short_name,
                'code' => $branch->code,
                'district' => $branch->district_id,
                'country' => $branch->region_id,
                'province' => $branch->region_id,
                'tehsil' => $branch->region_id,
                'latitude' => isset($branch->latitude) ? $branch->latitude : '0.0',
                'longitude' => isset($branch->longitude) ? $branch->longitude : '0.0',
                //'tags' => 'Central,Lahore2,Pakistan,Punjab,Lahore',
                'tags' => self::getBranchTags($branch),
            );
            return $array;
        }
    }

    public static function parseNews($latest_news)
    {

        $news = array();
        foreach ($latest_news as $n) {
            //print_r($n);
            //die();
            $news_image = (!empty($n->image_name)) ? ($n->image_name) : 'noimage.png';
            $pic_url = Url::to('@web/uploads/news/' . $news_image, true);
            $news [] = array(
                'id' => isset($n->id) ? $n->id : '',
                'heading' => isset($n->heading) ? htmlspecialchars($n->heading) : '',
                'short_description' => isset($n->short_description) ? htmlspecialchars($n->short_description) : '',
                'full_description' => isset($n->full_description) ? htmlspecialchars($n->full_description) : '',
                'news_date' => date('d-M-Y', $n->news_date),
                'news_time' => date('H:i A', $n->news_date),
                'img_url' => $pic_url
            );
        }
        /*$news = [
            [
                'id'=>12,
                'heading'=>'Dr. Amjad Saqib will reach Pakistan on 23rd September, 2017',
                'short_description'=>'Dr. Amjad Saqib will reach Pakistan on 23rd September, 2017',
                'full_description'=>'Dr. Amjad Saqib will reach Pakistan on 23rd September, 2017',
                'news_date'=>'2011-10-12',
                'news_time'=>'11:00 Am',
                'img_url'=>'https://media.licdn.com/mpr/mpr/shrinknp_400_400/AAEAAQAAAAAAAAU5AAAAJGU4MGZmZmZhLTRmOTUtNDE5ZC1iZTNlLWE0MGNjZTM2MTQ5OA.jpg',
            ],
            [
                'id'=>12,
                'heading'=>'Dr. Amjad Saqib will reach Pakistan on 23rd September, 2017',
                'short_description'=>'Dr. Amjad Saqib will reach Pakistan on 23rd September, 2017',
                'full_description'=>'Dr. Amjad Saqib will reach Pakistan on 23rd September, 2017',
                'news_date'=>'2011-10-12',
                'news_time'=>'11:00 Am',
                'img_url'=>'https://media.licdn.com/mpr/mpr/shrinknp_400_400/AAEAAQAAAAAAAAU5AAAAJGU4MGZmZmZhLTRmOTUtNDE5ZC1iZTNlLWE0MGNjZTM2MTQ5OA.jpg',
            ],
        ];*/
        //print_r($news);
        return $news;
    }

    public static function parseProjectDetail($project)
    {
        if (!empty($project)) {
            $project_image = (!empty($project->logo)) ? ($project->logo) : 'noimage.png';
            //$pic_url = Url::to('@web/uploads/projects/' . $project_image, true);
            $pic_url =ImageHelper::getAttachmentApiPath(). '?type=projects&file_name=' . $project_image .'&download=true';
            $array = array(
                'id' => isset($project->id) ? $project->id : '',
                'name' => isset($project->name) ? $project->name : '',
                'branches' => Branches::find()->join('inner join','branch_projects_mapping','branch_projects_mapping.branch_id=branches.id')->where(['branches.status' => 1,'branch_projects_mapping.project_id'=>$project->id])->count(),
                'started_date' => isset($project->started_date) ? date('d M Y', $project->started_date) : '',
                'logo' => $pic_url,
                'total_fund' => isset($project->total_fund) ? NumberHelper::getFormattedNumberAmount($project->total_fund) : '0',
                'description' => isset($project->description) ? $project->description : '',
                'info_array' => [
                    [
                        'key'=>'Branches',
                        'value'=>Branches::find()->join('inner join','branch_projects_mapping','branch_projects_mapping.branch_id=branches.id')->where(['branches.status' => 1,'branch_projects_mapping.project_id'=>$project->id])->count(),
                        'icon'=>'region_icon'
                    ],
                    [
                        'key'=>'Start Date',
                        'value'=>isset($project->started_date) ? date('d M Y', ($project->started_date)) : '',
                        'icon'=>'area_icon'
                    ],
                    [
                        'key'=>'Description',
                        'value'=>isset($project->description) ? $project->description : '',
                        'icon'=>'br_add_icon'
                    ]
                ]
            );
            return $array;
        }
        return $project;
    }

    public static function parseDistrictDetail($district)
    {

        if (!empty($district)) {
            $array = array(
                'id' => isset($district->id) ? $district->id : '',
                'name' => isset($district->name) ? $district->name : '',
                'description' => isset($district->description) ? $district->description : '',
                'branches' => Branches::find()->where(['district_id' => $district->id, 'status'=>1])->count(),
            );
            return $array;
        }
        return $district;
    }

    public static function parseActivityDetail($activity)
    {

        if (!empty($activity)) {
            $array = array(
                'id' => isset($activity->id) ? $activity->id : '',
                'name' => isset($activity->name) ? $activity->name : '',
                'description' => isset($activity->description) ? $activity->description : '',
                'branches' => '123',
            );
            return $array;
        }
        return $activity;
    }

    public static function parseProduct($product)
    {

        if (!empty($product)) {
            $array = array(
                'id' => isset($product['id']) ? $product['id'] : '',
                'name' => isset($product['name']) ? $product['name'] : '',
                'loans' => isset($product['loans_count']) ? $product['loans_count'] : '',
            );
            return $array;
        }
        return $product;
    }

    public static function parseActivity($activity)
    {

        if (!empty($activity)) {
            $array = array(
                'id' => isset($activity['id']) ? $activity['id'] : '',
                'name' => isset($activity['name']) ? $activity['name'] : '',
                'loans' => isset($activity['loans_count']) ? $activity['loans_count'] : '',
            );
            return $array;
        }
        return $activity;
    }

    public static function parseBorrower($borrowers)
    {
        $info = array();
        if (!empty($borrowers)) {
            foreach ($borrowers as $borrower) {
                $info[] = [
                    'id' => isset($borrower->id) ? $borrower->id : '',
                    'name' => isset($borrower->name) ? $borrower->name : '',
                    'parentage' => isset($borrower->parentage) ? $borrower->parentage : '',
                    'cnic' => isset($borrower->cnic) ? $borrower->cnic : '',
                    'groupno' => isset($borrower->group->grpno) ? $borrower->group->grpno : '',
                    'branch' => isset($borrower->branch->name) ? $borrower->branch->name : '',
                    'project' => isset($borrower->project) ? $borrower->project : '',
                    'loan' => self::getLoanInfo($borrower->loan),
                ];
            }
            return $info;
        }
        return $info;
    }

    public static function parseMember($members)
    {
        $info = array();
        if (!empty($members)) {
            foreach ($members as $member) {
                $info[] = [
                    'id' => isset($member->application->member->id) ? $member->application->member->id : '',
                    'name' => isset($member->application->member->full_name) ? $member->application->member->full_name : '',
                    'parentage' => isset($member->application->member->parentage) ? $member->application->member->parentage : '',
                    'cnic' => isset($member->application->member->cnic) ? $member->application->member->cnic : '',
                    'groupno' => isset($member->group->grp_no) ? $member->group->grp_no : '',
                    'branch' => isset($member->branch->name) ? $member->branch->name : '',
                    'project' => isset($member->project->name) ? $member->project->name : '',
                    'loan' => self::getLoanInfo($member->application->loan),
                ];
            }
            return $info;
        }
        return $info;
    }

    public static function parseMemberData($members)
    {
        $info = array();
        if (!empty($members)) {
            foreach ($members as $member) {
                $info[] = [
                    'id' => isset($member->id) ? $member->id : '',
                    'name' => isset($member->full_name) ? $member->full_name : '',
                    'cnic' => isset($member->cnic) ? $member->cnic : '',
                    'province' => isset($member->province->name) ? $member->province->name : '',
                    'city' => isset($member->city->name) ? $member->city->name : '',
                ];
            }
            return $info;
        }
        return $info;
    }

    public static function parseBorrowerLedger($borrower)
    {
        if (!empty($borrower)) {
            $array = array(
                'id' => isset($borrower->id) ? $borrower->id : 0,
                'name' => isset($borrower->name) ? $borrower->name : '',
                'parentage' => isset($borrower->parentage) ? $borrower->parentage : '',
                'cnic' => isset($borrower->cnic) ? $borrower->cnic : '',
                'groupno' => isset($borrower->group->grpno) ? $borrower->group->grpno : '',
                'branch' => isset($borrower->branch->name) ? $borrower->branch->name : '',
                'project' => isset($borrower->project) ? $borrower->project : '',
                'mobile' => isset($borrower->mobile) ? $borrower->mobile : '',
                'label_wise_borrower_detail' => [
                    [
                        'label' => 'Id',
                        'value' => isset($borrower->id) ? $borrower->id : 0,
                    ],
                    [
                        'label' => 'Name',
                        'value' => isset($borrower->name) ? $borrower->name : '',
                    ],
                    [
                        'label' => 'Parentage',
                        'value' => isset($borrower->parentage) ? $borrower->parentage : '',
                    ],
                    [
                        'label' => 'CNIC',
                        'value' => isset($borrower->cnic) ? $borrower->cnic : '',
                    ],
                    [
                        'label' => 'Type',
                        'value' => isset($borrower->group->grptype) ? ($borrower->group->grptype == 'GRP') ? 'Group' : 'Individual' : '',
                    ],
                    [
                        'label' => 'Group No',
                        'value' => isset($borrower->group->grpno) ? $borrower->group->grpno : '',
                    ],
                    [
                        'label' => 'Branch',
                        'value' => isset($borrower->branch->name) ? $borrower->branch->name : '',
                    ],
                    [
                        'label' => 'Project',
                        'value' => isset($borrower->project) ? $borrower->project : '',
                    ],
                    [
                        'label' => 'Mobile',
                        'value' => isset($borrower->mobile) ? $borrower->mobile : '',
                    ],
                    [
                        'label' => 'Loan Purpose',
                        'value' => isset($borrower->activity->name) ? $borrower->activity->name : '',
                    ]
                ]
            );
            return $array;
        }
        return $borrower;
    }

    public static function parseMemberLedger($application)
    {
        if (!empty($application)) {
            $array = array(
                'id' => isset($application->member->id) ? $application->member->id : 0,
                'name' => isset($application->member->full_name) ? $application->member->full_name : '',
                'parentage' => isset($application->member->parentage) ? $application->member->parentage : '',
                'cnic' => isset($application->member->cnic) ? $application->member->cnic : '',
                'groupno' => isset($application->group->grp_no) ? $application->group->grp_no : '',
                'branch' => isset($application->branch->name) ? $application->branch->name : '',
                'project' => isset($application->project->name) ? $application->project->name : '',
                'mobile' => isset($application->member->membersMobile->phone) ? $application->member->membersMobile->phone : '',
                'address' => isset($application->member->membersAddress->address) ? $application->member->membersAddress->address : '',
                'label_wise_borrower_detail' => [
                    [
                        'label' => 'Id',
                        'value' => isset($application->member->id) ? $application->member->id : 0,
                    ],
                    [
                        'label' => 'Name',
                        'value' => isset($application->member->full_name) ? $application->member->full_name : '',
                    ],
                    [
                        'label' => 'Parentage',
                        'value' => isset($application->member->parentage) ? $application->member->parentage : '',
                    ],
                    [
                        'label' => 'CNIC',
                        'value' => isset($application->member->cnic) ? $application->member->cnic : '',
                    ],
                    [
                        'label' => 'Type',
                        'value' => isset($application->group->grp_no) ? ($application->group->grp_type == 'GRP') ? 'Group' : 'Individual' : '',
                    ],
                    [
                        'label' => 'Group No',
                        'value' => isset($application->group->grp_no) ? $application->group->grp_no : '',
                    ],
                    [
                        'label' => 'Branch',
                        'value' => isset($application->branch->name) ? $application->branch->name : '',
                    ],
                    [
                        'label' => 'Project',
                        'value' => isset($application->project->name) ? $application->project->name : '',
                    ],
                    [
                        'label' => 'Mobile',
                        'value' => isset($application->member->membersMobile->phone) ? $application->member->membersMobile->phone : '',
                    ],
                    [
                        'label' => 'Loan Purpose',
                        'value' => isset($application->activity->name) ? $application->activity->name : '',
                    ]
                ]
            );
            return $array;
        }
        return $application;
    }

    public static function parseLoanLedger($loan)
    {
        /*print_r($loan);
        die();*/
        if (!empty($loan)) {
            $array = [
                'id' => isset($loan->id) ? $loan->id : '',
                'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : '',
                'amountapproved' => isset($loan->loan_amount) ? $loan->loan_amount : '',
                'datedisburse' => isset($loan->date_disbursed) && $loan['date_disbursed'] != 0  ? date('d M Y', $loan->date_disbursed) : '',
                'dsb_status' => isset($loan->status) ? (($loan['status']=='collected')?'Active':$loan['status']) : '',
                'label_wise_loan_detail' => [
                    [
                        'label' => 'Sanction No',
                        'value' => isset($loan->sanction_no) ? $loan->sanction_no : '',
                    ],
                    [
                        'label' => 'Disbursement Date',
                        'value' => isset($loan->date_disbursed) && $loan['date_disbursed'] != 0  ? date('d M Y', $loan->date_disbursed) : '',
                    ],
                    [
                        'label' => 'Loan Amount',
                        'value' => isset($loan->loan_amount) ? number_format($loan->loan_amount) : '',
                    ],
                    [
                        'label' => 'Loan Status',
                        'value' => isset($loan->status) ? (($loan['status']=='collected')?'Active':$loan['status']) : '',
                    ]
                ]
            ];
            return $array;
        }
        return $loan;
    }

    public static function parseMemberRecoveries($member,$balance)
    {
        if (!empty($member)) {
            $array =
                [
                    [
                        'label' => 'Name',
                        'value' => isset($member->full_name) ? $member->full_name : '',
                    ],
                    [
                        'label' => 'Parentage',
                        'value' => isset($member->parentage) ? $member->parentage : '',
                    ],
                    [
                        'label' => 'CNIC',
                        'value' => isset($member->cnic) ? $member->cnic : '',
                    ],
                    [
                        'label' => 'Balance',
                        'value' => isset($balance) ? $balance: '0',
                    ]
                ];

            return $array;
        }
        return $member;
    }

    public static function parseLoanRecoveries($loan,$user)
    {
        /*print_r($loan);
        die();*/
        $schedules = array_slice($loan->schedules, -2, 2, true);
        if (!empty($loan)) {
            $array = [
                'post_token' => $user->post_token,
                'loan_id' => $loan->id,
                'borrower_id' => isset($loan->application->member->id) ? $loan->application->member->id : '',
                'name' => isset($loan->application->member->full_name) ? $loan->application->member->full_name : '',
                'sanction_no' => isset($loan->sanction_no) ? $loan->sanction_no : '',
                'amountapproved' => isset($loan->loan_amount) ? $loan->loan_amount : '',
                'dsb_status' => isset($loan->status) ? $loan->status : '',
                'datedisburse' => isset($loan->date_disbursed) && $loan->date_disbursed !=0 ? date('d M Y',$loan->date_disbursed) : '',
                'balance' => isset($loan->balance) ? $loan->balance : '0',
                'due' => isset($loan->due) ? $loan->due : '0',
                'overdue' => isset($loan->overdue) ? $loan->overdue : '',
                'borrower' => ApiParser::parseMemberRecoveries($loan->application->member, $loan->balance),
                'paid-installments' => ApiParser::parseRecoveriesLedger($schedules,$loan->loan_amount),
                ];
            return $array;
        }
        return $loan;
    }

    public static function parseAnalysisInfo($analysis)
    {
        $info = array();
        if (!empty($analysis)) {
            foreach ($analysis as $a) {
                $info[] = [
                    'title' => isset($a['name']) ? $a['name'] : '',
                    'attribute_id' => isset($a['attribute_id']) ? $a['attribute_id'] : '',
                    'value' => isset($a['attribute']) ? NumberHelper::getFormattedNumberAmount($a['attribute']) : '',
                    //'value' => NumberHelper::getFormattedNumberAmount('100'),

                ];
            }
            return $info;
        }
        return $info;
    }

    public static function parseAnalysisInfoMdp($analysis)
    {
        $info = array();
        if (!empty($analysis)) {
            foreach ($analysis as $a) {
                $info[] = [
                    'title' => isset($a['name']) ? $a['name'] : '',
                    'attribute_id' => isset($a['attribute_id']) ? $a['attribute_id'] : '',
                    'value' => isset($a['attribute']) ? NumberHelper::getFormattedNumberAmount($a['attribute']) : '',
                    'mdp_borrower' => isset($a['mdp_borrower']) ? NumberHelper::getFormattedNumberAmount($a['mdp_borrower']) : '',
                    //'value' => NumberHelper::getFormattedNumberAmount('100'),

                ];
            }
            return $info;
        }
        return $info;
    }

    public static function parseAnalysisTrend($analysis,$attribute)
    {
        $response = array();
        $info = array();
        $max = 0;
        $min = 0;
        if (!empty($analysis)) {
            $min = $analysis[0]['attribute'];
            $i=0;
            foreach ($analysis as $key=>$a) {
                if($i<7) {
                    if(in_array($attribute,['MALELOANS','FEMALELOANS','CDISB'])){
                        if($i<6) {
                            $info[] = [
                                'x' => isset($a['month']) ? $a['month'] : '',
                                'y' => isset($a['attribute']) ? $a['attribute'] - $analysis[$key + 1]['attribute'] : '',
                            ];
                            if (($a['attribute'] - $analysis[$key + 1]['attribute']) > $max) {
                                $max = $a['attribute'] - $analysis[$key + 1]['attribute'];
                            }
                            if (($a['attribute'] - $analysis[$key + 1]['attribute']) < $min) {
                                $min = $a['attribute'] - $analysis[$key + 1]['attribute'];
                            }
                        }
                    }else{
                        $info[] = [
                            'x' => isset($a['month']) ? $a['month'] : '',
                            'y' => isset($a['attribute']) ? $a['attribute'] : '',
                        ];
                        if ($a['attribute'] > $max) {
                            $max = $a['attribute'];
                        }
                        if ($a['attribute'] < $min) {
                            $min = $a['attribute'];
                        }
                    }
                }
                $i++;
            }
            $response['range']   = ['max'=> $max, 'min' =>$min];
            $response['trend']   = $info;
            return $response;
        }
        return $response;
    }


    public static function parseAnalysisSummary($analysis,$analysis1)
    {
        $info = array();
        if (!empty($analysis)) {
            $a=NumberHelper::getFormattedNumberAmount(($analysis['mdp']-$analysis1['mdp']));
            $b=$analysis1['active_loans'];
            if((!empty($a) && $a!=0) && (!empty($b) && $b!=0)){
                $mdp_borrower = NumberHelper::getFormattedNumberAmount(($analysis['mdp']-$analysis1['mdp'])/$analysis1['active_loans']);
            }else{
                $mdp_borrower = 0;
            }

            /*if(!isset($analysis['mdp']) || empty($analysis['mdp']) || $analysis['mdp']==0){
                $prev_mdp=0;
            }else{
                $prev_mdp=$analysis1['mdp'];
            }*/
            $info = [
                ['title' => 'MDP', 'value' =>NumberHelper::getFormattedNumberAmount( $analysis['mdp']-$analysis1['mdp'])],
                ['title' => 'Cum Due', 'value' => NumberHelper::getFormattedNumberAmount($analysis['cum_due'])],
                ['title' => 'Cum Rec', 'value' => NumberHelper::getFormattedNumberAmount($analysis['cum_recv'])],
                ['title' => 'Overdue Borrowers', 'value' => isset($analysis['overdue_borrowers'])?$analysis['overdue_borrowers']:0],
                ['title' => 'Overdue amount', 'value' => NumberHelper::getFormattedNumberAmount($analysis['overdue_amount'])],
                ['title' => 'Overdue Percent', 'value' => isset($analysis['overdue_percentage'])?$analysis['overdue_percentage']:0],
                ['title' => 'PAR Amount', 'value' => NumberHelper::getFormattedNumberAmount($analysis['par_amount'])],
                ['title' => 'PAR Percent', 'value' => isset($analysis['par_percentage'])?$analysis['par_percentage']:0],
                ['title' => 'No of Loans', 'value' => isset($analysis['no_of_loans'])?$analysis['no_of_loans']:0],
                ['title' => 'Active Loans', 'value' => isset($analysis['active_loans'])?$analysis['active_loans']:0],
                ['title' => 'Male Loans', 'value' => isset($analysis['family_loans'])?$analysis['family_loans']:0],
                ['title' => 'Female Loans', 'value' => isset($analysis['female_loans'])?$analysis['female_loans']:0],
                ['title' => 'Cum Disbursement', 'value' => NumberHelper::getFormattedNumberAmount($analysis['cum_disb'])],
                ['title' => 'Recovery Percent', 'value' => isset($analysis['recovery_percentage'])?$analysis['recovery_percentage']:0],
                ['title' => 'Beneficiaries', 'value' => isset($analysis['members_count'])?$analysis['members_count']:0],
                ['title' => 'Olp Amount', 'value' => NumberHelper::getFormattedNumberAmount($analysis['olp_amount'])],
                //['title' => 'MDP/Borrower', 'value' => NumberHelper::getFormattedNumberAmount($analysis['mdp_per_borrower'])],
                ['title' => 'MDP/Borrower', 'value' => isset($mdp_borrower)?$mdp_borrower:0],
                ['title' => 'Current Month Disbursement', 'value' =>NumberHelper::getFormattedNumberAmount( $analysis['cum_disb']-$analysis1['cum_disb'])],
            ];

            return $info;
        }
        return $info;
    }

    public static function parseLoanTotal($loan, $recoveries)
    {
        $sum_credit = 0;
        if (!empty($recoveries)) {
            foreach ($recoveries as $r) {
                $sum_credit += $r->amount;
            }
        }
        if (!empty($loan)) {
            $array = [
                'amountapproved' => $loan->loan_amount,
                'balance' => $loan->balance,
                'recovery' => $sum_credit,
                'due' => $loan->due,
                'overdue' => $loan->overdue,
                'heading_wise_total' => [
                    [
                        'label' => 'Loan Amount',
                        'value' => isset($loan->loan_amount) ? number_format($loan->loan_amount) : 0
                    ],
                    [
                        'label' => 'Received Amount',
                        'value' => number_format($sum_credit)
                    ],
                    [
                        'label' => 'Outstanding Balance',
                        'value' => isset($loan->balance) ? number_format($loan->balance) : 0
                    ]
                ]
            ];
            return $array;
        }
        return $loan;
    }

    private static function getBorrowerInfo($borrower)
    {
        if (!empty($borrower)) {
            $array = array(
                'id' => isset($borrower->id) ? $borrower->id : 0,
                'name' => isset($borrower->name) ? $borrower->name : '',
                'parentage' => isset($borrower->parentage) ? $borrower->parentage : '',
                'cnic' => isset($borrower->cnic) ? $borrower->cnic : '',
                'groupno' => isset($borrower->group->grpno) ? $borrower->group->grpno : '',
                'branch' => isset($borrower->branch->name) ? $borrower->branch->name : '',
                'project' => isset($borrower->project) ? $borrower->project : '',
            );
            return $array;
        }
        return $borrower;
    }

    private static function getLoanInfo($loan)
    {
        if (!empty($loan)) {
            $array = array(
                'id' => isset($loan['id']) ? $loan['id'] : 0,
                'sanction_no' => isset($loan['sanction_no']) ? $loan['sanction_no'] : '',
                'amountapproved' => isset($loan['loan_amount']) ? $loan['loan_amount'] : '',
                'datedisburse' => isset($loan['date_disbursed']) && $loan['date_disbursed'] != 0 ? date('d M Y', $loan['date_disbursed']) : '',
                'dsb_status' => isset($loan['status']) ? (($loan['status']=='collected')?'Active':$loan['status']) : '',
            );
            return $array;
        } else {
            $array = array(
                'id' => 0
            );
            return $array;
        }
    }

    public static function parseLedger($schedules,$amountapproved)
    {
        if (!empty($schedules)) {

            $recv = array();
            $id = 1;
            foreach ($schedules as $s) {
                //print_r($s);
                //die();
                $balance = $amountapproved;
                if (!empty($s->recoveries)) {
                    foreach ($s->recoveries as $r) {
                        $balance = $balance - (isset($r->amount) ? $r->amount : 0);
                        $recv[] = array(
                            'id' => $id,
                            'due_date' => isset($s->due_date) ? date('d M Y',$s->due_date) : '',
                            'credit' => isset($r->amount) ? ($r->amount)+($r->charges_amount) : '0',
                            'recv_date' => isset($r->receive_date) ? date('d M Y', $r->receive_date) : '',
                            'receipt_no' => isset($r->receipt_no) ? $r->receipt_no : '',
                            'schdl_amnt' => isset($s->schdl_amnt) ? $s->schdl_amnt : '0',
                            'schdl_credit' => isset($s->amount) ? $s->amount : '0',
                            'advance' => isset($s->advance_log) ? $s->advance_log : '0',
                            'overdue' => isset($s->overdue_log) ? $s->overdue_log : '0',
                            'due' => isset($s->due_amnt) ? $s->due_amnt : '0',
                            'mdp' => isset($r->mdp) ? $r->mdp : '',
                            'is_late' => false,
                            'label_wise_ledger' =>
                                [
                                    [
                                        'label' => 'Installment No',
                                        'value' => $id
                                    ],
                                    [
                                        'label' => 'Due Date',
                                        'value' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                                    ],
                                    [
                                        'label' => 'Due Amount',
                                        'value' => isset($s->due_amnt) ? number_format($s->due_amnt) : '0',
                                    ],
                                    [
                                        'label' => 'Receipt No',
                                        'value' => isset($r->receipt_no) ? $r->receipt_no : '',
                                    ],
                                    [
                                        'label' => 'Received Date',
                                        'value' => isset($r->receive_date) ? date('d M Y', $r->receive_date) : '',
                                    ],
                                    [
                                        'label' => 'Received Amount',
                                        'value' => isset($r->amount) ? number_format($r->amount) : '0',
                                    ]
                                ]
                        );
                        $id++;
                    }
                } else {
                    $recv[] = array(
                        'id' => $id,
                        'due_date' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                        'credit' => '0',
                        'recv_date' => '',
                        'receipt_no' => '',
                        'schdl_credit' => isset($s->amount) ? $s->amount : '',
                        'schdl_amnt' => isset($s->schdl_amnt) ? $s->schdl_amnt : '',
                        'advance' => isset($s->advance_log) ? $s->advance_log : '',
                        'overdue' => isset($s->overdue_log) ? $s->overdue_log : '0',
                        'due' => isset($s->due_amnt) ? $s->due_amnt : '0',
                        'mdp' => '0',
                        'is_late' => false,
                        'label_wise_ledger' =>
                            [
                                [
                                    'label' => 'Installment No',
                                    'value' => $id
                                ],
                                [
                                    'label' => 'Due Date',
                                    'value' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                                ],
                                [
                                    'label' => 'Due Amount',
                                    'value' => isset($s->due_amnt) ? number_format($s->due_amnt) : '0',
                                ],
                                [
                                    'label' => 'Receipt No',
                                    'value' => '',
                                ],
                                [
                                    'label' => 'Received Date',
                                    'value' => '',
                                ],
                                [
                                    'label' => 'Received Amount',
                                    'value' => '0',
                                ]
                            ]
                    );
                    $id++;
                }
            }
            return $recv;
        }
        return $schedules;
    }

    public static function parseRecoveriesLedger($schedules,$amountapproved)
    {
        if (!empty($schedules)) {

            $recv = array();
            $id = 1;
            foreach ($schedules as $s) {
                //print_r($s);
                //die();
                $balance = $amountapproved;
                if (!empty($s->recoveries)) {
                    foreach ($s->recoveries as $r) {
                        $balance = $balance - (isset($r->amount) ? $r->amount : 0);
                        $recv[] =
                                [
                                    'installment_no' => $id,
                                    'due_date' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                                    'due_amount' => isset($s->due_amnt) ? number_format($s->due_amnt) : '0',
                                    'receipt_no' => isset($r->receipt_no) ? $r->receipt_no : '',
                                    'recv_date' => isset($r->receive_date) ? date('d M Y', $r->receive_date) : '',
                                    'recv_amount' => isset($r->amount) ? number_format($r->amount) : '0',
                                    /*[
                                        'label' => 'Installment No',
                                        'value' => $id
                                    ],
                                    [
                                        'label' => 'Due Date',
                                        'value' => isset($s->due_date) ? date('d M Y', strtotime($s->due_date)) : '',
                                    ],
                                    [
                                        'label' => 'Due Amount',
                                        'value' => isset($s->due_amnt) ? number_format($s->due_amnt) : '0',
                                    ],
                                    [
                                        'label' => 'Receipt No',
                                        'value' => isset($r->receipt_no) ? $r->receipt_no : '',
                                    ],
                                    [
                                        'label' => 'Received Date',
                                        'value' => isset($r->recv_date) ? date('d M Y', strtotime($r->recv_date)) : '',
                                    ],
                                    [
                                        'label' => 'Received Amount',
                                        'value' => isset($r->credit) ? number_format($r->credit) : '0',
                                    ]*/
                                ];

                        $id++;
                    }
                } else {
                    $recv[] = [

                                'installment_no' => $id,
                                'due_date' => isset($s->due_date) ? date('d M Y', $s->due_date) : '',
                                'due_amount' => isset($s->due_amnt) ? number_format($s->due_amnt) : '0',
                                'receipt_no' => '',
                                'recv_date' => '',
                                'recv_amount' => '0',
                                /*[
                                    'label' => 'Installment No',
                                    'value' => $id
                                ],
                                [
                                    'label' => 'Due Date',
                                    'value' => isset($s->due_date) ? date('d M Y', strtotime($s->due_date)) : '',
                                ],
                                [
                                    'label' => 'Due Amount',
                                    'value' => isset($s->due_amnt) ? number_format($s->due_amnt) : '0',
                                ],
                                [
                                    'label' => 'Receipt No',
                                    'value' => '',
                                ],
                                [
                                    'label' => 'Received Date',
                                    'value' => '',
                                ],
                                [
                                    'label' => 'Received Amount',
                                    'value' => '0',
                                ]*/
                            ];

                    $id++;
                }
            }
            return $recv;
        }
        return $schedules;
    }

    private static function getBranchTags($branch, $short=false){
        $tags = '';

        if(isset($branch->province->name)){
            $tags .= ', '.$branch->province->name;
        }
        if(isset($branch->region->name) && $short==false){
            $tags .= ', '.$branch->region->name;
        }
        if(isset($branch->area->name) && $short==false){
            $tags .= ', '.$branch->area->name;
        }
        /*if(isset($branch->city->name)){
            $tags .= ', '.$branch->city->name;
        }*/
        return trim($tags,',');
    }

    private static function getAreasCount($region){
        $areas = Areas::find()->select(['count(id) as areas_count'])->where(['region_id'=>$region->id])->asArray()->all();
        return $areas[0]['areas_count'];
    }

    private static function getBranchesCount($region){
        $areas = Branches::find()->select(['count(id) as branches_count'])->where(['region_id'=>$region->id])->asArray()->all();
        return $areas[0]['branches_count'];
    }

    private static function getBranchesCountByAreas($area){
        $areas = Branches::find()->select(['count(id) as branches_count'])->where(['area_id'=>$area->id])->asArray()->all();
        return $areas[0]['branches_count'];
    }

    public static function parseModules($user)
    {
        $info = array();
        if (!empty($user)) {
            return [
                'network' => ($user->designation->network == 0)?false:true,
                'progress_report' => ($user->designation->progress_report == 0)?false:true,
                'projects' => ($user->designation->projects == 0)?false:true,
                'districts' => ($user->designation->districts == 0)?false:true,
                'products' => ($user->designation->products == 0)?false:true,
                'analysis' => ($user->designation->analysis == 0)?false:true,
                'search_loan' => ($user->designation->search_loan == 0)?false:true,
                'news' => ($user->designation->news == 0)?false:true,
                'maps' => ($user->designation->maps == 0)?false:true,
                'staff' => ($user->designation->staff == 0)?false:true,
                'links' => ($user->designation->links == 0)?false:true,
                'filters' => ($user->designation->filters == 0)?false:true,
                'charges' => ($user->designation->charges == 0)?false:true,
                'housing' => ($user->designation->housing == 0)?false:true,
                'audit' => ($user->designation->audit == 0)?false:true,
            ];
            //return $info;
        }
        return $info;
    }

    public static function parseReceipt($model)
    {
        $info = array();
        if (!empty($model)) {
            return [
                [
                    'label' => 'Receipt No',
                    'value' => isset($model->receipt_no) ? $model->receipt_no : ''
                ],
                [
                    'label' => 'Sanction No',
                    'value' => isset($model->sanction_no) ? $model->sanction_no : '',
                ],
                [
                    'label' => 'Borrower Name',
                    'value' => isset($model->borrower->name) ? $model->borrower->name : '',
                ],
                [
                    'label' => 'CNIC',
                    'value' => isset($model->borrower->cnic) ? $model->borrower->cnic : '',
                ],
                [
                    'label' => 'Received Date',
                    'value' => isset($model->recv_date) ? date('d-M-Y', $model->recv_date) : '',
                ],
                [
                    'label' => 'Recovery Amount',
                    'value' => isset($model->credit) ? 'Rs.'.number_format($model->credit).'/-' : '',
                ],
                [
                    'label' => 'MDP',
                    'value' => isset($model->mdp) ? 'Rs.'.number_format($model->mdp).'/-' : '',
                ],
                [
                    'label' => 'Loan Amount',
                    'value' => isset($model->loan->amountapproved) ? 'Rs.'.number_format($model->loan->amountapproved).'/-' : '',
                ],
                [
                    'label' => 'Outstanding Balance',
                    'value' => isset($model->loan->balance) ? 'Rs.'.number_format($model->loan->balance).'/-' : '',
                ],
                /*'receipt_no' => isset($model->receipt_no) ? $model->receipt_no : '',
                'sanction_no' => isset($model->sanction_no) ? $model->sanction_no : '',
                'name' => isset($model->borrower->name) ? $model->borrower->name : '',
                'cnic' => isset($model->borrower->cnic) ? $model->borrower->cnic : '',
                'balance' => isset($model->loan->balance) ? $model->loan->balance : '',
                'recv_date' => isset($model->recv_date) ? $model->recv_date : '',
                'recovery_amount' => isset($model->credit) ? $model->credit : '',
                'mdp' => isset($model->mdp) ? $model->mdp : '',*/
            ];
            //return $info;
        }
        return $info;
    }

    public static function parseRecoveriescih($recoveries)
    {
        $info = array();
        if (!empty($recoveries)) {
            foreach ($recoveries as $r) {
                $info[] = [
                    'id'=>$r->id,
                    'loan_id'=>$r->loan_id,
                    'sanction_no'=>$r->loan->sanction_no,
                    'name'=>$r->application->member->full_name,
                    'cnic'=>$r->application->member->cnic,
                    'recv_date'=>isset($r->receive_date) && $r->receive_date !=0 ? date('d M Y', $r->receive_date) : '',
                    'amount'=>$r->amount,
                    'receipt_no'=>$r->receipt_no,
                ];
            }
        }
        return $info;
    }

    public static function parseDevice($device)
    {
        $info = array();
        if (!empty($device)) {
            $info = [
                'id'=>$device->id,
                'uu_id'=>$device->uu_id,
                'imei_no'=>$device->imei_no,
                'os_version'=>$device->os_version,
                'device_model'=>$device->device_model,
            ];
        }
        return $info;
    }

}