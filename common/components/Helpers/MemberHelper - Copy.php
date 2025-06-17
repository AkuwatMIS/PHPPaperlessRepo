<?php

/**
 * Created by PhpStorm.
 * User: Khubaib_ur_Rehman
 * Date: 9/9/2017
 * Time: 7:40 PM
 */
namespace common\components\Helpers;

use common\components\Parsers\ApiParser;
use common\models\Applications;
use common\models\Lists;
use common\models\Loans;
use common\models\Members;
use common\models\Images;
use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use yii\helpers\ArrayHelper;

class MemberHelper
{
    public static function getParentageType()
    {
        $parentage_type = ArrayHelper::map(Lists::find()->where(['list_name'=>'parentage_types'])->all(),'value','label');
        return $parentage_type;
    }
    public static function getGender()
    {
        $gender = ArrayHelper::map(Lists::find()->where(['list_name'=>'gender'])->all(),'value','label');
        return $gender;
    }
    public static function getEducation()
    {
        $education = ArrayHelper::map(Lists::find()->where(['list_name'=>'education'])->all(),'value','label');
        return $education;
    }
    public static function getMaritalStatus()
    {
        $marital_status = ArrayHelper::map(Lists::find()->where(['list_name'=>'marital_status'])->all(),'value','label');
        return $marital_status;
    }
    public static function getReligions()
    {
        $religions = ArrayHelper::map(Lists::find()->where(['list_name'=>'religions'])->all(),'value','label');
        return $religions;
    }

    public static function getProfileImage($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'profile_pic']);
        return $image;
    }

    public static function getFamilyMemberCNICFrontImage($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'family_member_cnic_front']);
        return $image;
    }

    public static function getFamilyMemberCNICBackImage($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'family_member_cnic_back']);
        return $image;
    }

    public static function getFCnic($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'cnic_front']);
        return $image;
    }

    public static function getBCnic($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'cnic_back']);
        return $image;
    }

    public static function getLeftIndex($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'left_index']);
        return $image;
    }

    public static function getRightIndex($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'right_index']);
        return $image;
    }

    public static function getLeftThumb($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'left_thumb']);
        return $image;
    }

    public static function getRightThumb($id)
    {
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => 'members', 'image_type' => 'right_thumb']);
        return $image;
    }

    public static function getIsDisable()
    {
        $disable = ArrayHelper::map(Lists::find()->where(['list_name'=>'is_disable'])->all(),'value','label');

        return $disable;
    }

    public static function getDisability()
    {
        $disable = ArrayHelper::map(Lists::find()->where(['list_name'=>'disabilities'])->all(),'value','label');
        return $disable;
    }

    public static function getDisabilityNature()
    {
        $disable = ArrayHelper::map(Lists::find()->where(['list_name'=>'disability_nature'])->all(),'value','label');
        return $disable;
    }

    public static function getDisabilityType()
    {
        $disable = ArrayHelper::map(Lists::find()->where(['list_name'=>'disability_types'])->all(),'value','label');
        return $disable;
    }


    public static function getMemberImages($id)
    {
        $data = [];
        $data['profile_pic'] = ApiParser::parseImage(self::getProfileImage($id), $id);
        $data['cnic_front'] = ApiParser::parseImage(self::getFCNIC($id), $id);
        $data['cnic_back'] = ApiParser::parseImage(self::getBCNIC($id), $id);
        $data['left_index'] = ApiParser::parseImage(self::getLeftThumb($id), $id);
        $data['right_index'] = ApiParser::parseImage(self::getRightIndex($id), $id);
        $data['left_thumb'] = ApiParser::parseImage(self::getLeftThumb($id), $id);
        $data['right_thumb'] = ApiParser::parseImage(self::getRightThumb($id), $id);

        return $data;
    }

    public static function getMemberDocuments($id)
    {
        $data = [];
        $data[] = ['file_type' => 'cnic_front', 'file_address' => ApiParser::parseImage(self::getFCNIC($id), $id)];
        $data[] = ['file_type' => 'cnic_back', 'file_address' => ApiParser::parseImage(self::getBCNIC($id), $id)];

        return $data;
    }

    public static function getMemberProfileImage($id)
    {
        $data = [];
        $data['profile_pic'] = ApiParser::parseImage(self::getProfileImage($id), $id);
        return $data;
    }

    public static function getThumbImpressions($id)
    {
        $data = [];
        $model = Members::findOne(['id' => $id, 'deleted' => 0]);
        $data['left_index'] = isset($model->left_index) ? ImageHelper::getBase64Image($model->left_index) : "noimage.png";
        $data['right_index'] = isset($model->right_index) ? ImageHelper::getBase64Image($model->right_index) : "noimage.png";
        $data['left_thumb'] = isset($model->left_thumb) ? ImageHelper::getBase64Image($model->left_thumb) : "noimage.png";
        $data['right_thumb'] = isset($model->right_thumb) ? ImageHelper::getBase64Image($model->right_thumb) : "noimage.png";
        return $data;
    }
    public static function getActiveLoanFromCNIC($cnic)
    {
        $loan = Loans::find()->joinWith('application.member')
            ->where(['members.cnic' =>$cnic,'loans.status' => "collected"])->one();
        return $loan;
    }
    public static function getExistingApplicationFromCNIC($cnic)
    {
            $application_data = Applications::find()
                    ->joinWith('member')
                ->leftJoin('loans','loans.application_id = applications.id')
                ->where(['members.cnic'=> $cnic])
                ->andWhere(['is', 'loans.application_id', null])
                ->andWhere(['in','applications.status',['pending','approved']])
                //->andWhere(['applications.status'=>'approved'])
                ->one();

       return $application_data;
   }
    public static function getUserThumbImpression($id)
    {
        $model = Members::findOne(['id' => $id, 'deleted' => 0]);
        $data = isset($model->thumb_impression) ? ImageHelper::getBase64Image($model->thumb_impression) : "noimage.png";
        return $data;
    }
    public static function getMemberStatus()
    {
        $status = array('approved'=>'Approved','rejected'=>'Rejected');
        return $status;
    }
    public static function checkActiveLoan($cnic)
    {
        $active_loan_check = Loans::find()
            ->join('inner join','applications','applications.id=loans.application_id')
            ->join('inner join','members','members.id=applications.member_id')
            ->filterWhere(['like', 'members.cnic', $cnic])
            ->andFilterWhere(['not in','loans.status',['loan completed','not collected']])
            ->andFilterWhere(['loans.deleted'=>0])
            ->one();
        return $active_loan_check;
    }
    public static function checkActiveLoanFamilyMember($cnic)
    {
        $active_loan_check = Loans::find()
            ->join('inner join','applications','applications.id=loans.application_id')
            ->join('inner join','members','members.id=applications.member_id')
            ->filterWhere(['like', 'members.family_member_cnic', $cnic])
            ->andFilterWhere(['not in','loans.status',['loan completed','not collected']])
            ->andFilterWhere(['loans.deleted'=>0])
            ->one();
        return $active_loan_check;
    }
    public static function saveMemberAddress($model)
    {
        if (empty(MembersAddress::find()->where(['member_id' => $model->member_id, 'address_type' => $model->address_type, 'address' => $model->address,'is_current'=>1,'deleted'=>0])->one())) {
            $member_addresses = MembersAddress::find()->where(['member_id' => $model->member_id, 'address_type' => $model->address_type,'is_current'=>1,'deleted'=>0])->all();
            if (!empty($model->address)) {
                foreach ($member_addresses as $member_address) {
                    $member_address->is_current = 0;
                    $member_address->save();
                }
                if (!($flag = $model->save())) {
                    return $model->getErrors();
                } else {
                    return true;
                }
            }
            else {
                return true;
            }
        } else {
            return true;
        }
    }
    public static function saveMemberPhone($model)
    {
        if (empty(MembersPhone::find()->where(['member_id' => $model->member_id, 'phone_type' => $model->phone_type, 'phone' => $model->phone,'is_current'=>1,'deleted'=>0])->one())) {
            $member_phones = MembersPhone::find()->where(['member_id' => $model->member_id, 'phone_type' => $model->phone_type,'is_current'=>1,'deleted'=>0])->all();
            if (!empty($model->phone)) {
                foreach ($member_phones as $member_phone) {
                    $member_phone->is_current = 0;
                    $member_phone->save();
                }
                if (!($flag = $model->save())) {
                    return $model->getErrors();
                } else {
                    return true;
                }
            }
            else{
                return true;
            }
        } else {
            return true;
        }
    }

    public static function saveMemberEmail($model)
    {
        if (empty(MembersEmail::find()->where(['member_id' => $model->member_id, 'email' => $model->email,'is_current'=>1,'deleted'=>0])->one())) {
            $member_emails = MembersEmail::find()->where(['member_id' => $model->member_id,'is_current'=>1,'deleted'=>0])->all();
            if (!empty($model->email)) {
                foreach ($member_emails as $member_email) {
                    $member_email->is_current = 0;
                    $member_email->save();
                }
                if (!($flag = $model->save())) {
                    return $model->getErrors();
                } else {
                    return true;
                }
            }
            else{
                return true;
            }
        } else {
            return true;
        }
    }

}