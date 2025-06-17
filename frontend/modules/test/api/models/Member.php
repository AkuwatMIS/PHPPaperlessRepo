<?php

namespace frontend\modules\test\api\models;

use Yii;

/**
 * This is the model class for table "employee".
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $created_at
 * @property string $updated_at
 */
class Member extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'members';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['full_name', 'sur_name', 'parentage_type', 'cnic', 'gender', 'dob', 'education', 'marital_status', 'family_member_name', 'family_member_cnic', 'religion', 'profile_pic', 'status', 'assigned_to', 'created_by', 'created_at'], 'required'],
            [['dob', 'created_at', 'updated_at'], 'safe'],
            [['assigned_to', 'created_by'], 'integer'],
            [['full_name', 'sur_name', 'parentage_type', 'family_no'], 'string', 'max' => 50],
            [['cnic', 'education'], 'string', 'max' => 20],
            [['gender'], 'string', 'max' => 6],
            [['marital_status'], 'string', 'max' => 10],
            [['family_head', 'family_member_name', 'religion'], 'string', 'max' => 25],
            [['family_member_cnic'], 'string', 'max' => 15],
            [['profile_pic'], 'string', 'max' => 100],
            [['status'], 'string', 'max' => 3],
            [['deleted'], 'string', 'max' => 1],
            [['cnic'], 'unique'],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'full_name' => 'Full Name',
            'sur_name' => 'Sur Name',
            'parentage_type' => 'Parentage Type',
            'cnic' => 'Cnic',
            'gender' => 'Gender',
            'dob' => 'Dob',
            'education' => 'Education',
            'marital_status' => 'Marital Status',
            'family_no' => 'Family No',
            'family_head' => 'Family Head',
            'family_member_name' => 'Family Member Name',
            'family_member_cnic' => 'Family Member Cnic',
            'religion' => 'Religion',
            'profile_pic' => 'Profile Pic',
            'status' => 'Status',
            'deleted' => 'Deleted',
            'assigned_to' => 'Assigned To',
            'created_by' => 'Created By',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    static public function search($params)
    {

        $page = Yii::$app->getRequest()->getQueryParam('page');
        $limit = Yii::$app->getRequest()->getQueryParam('limit');
        $order = Yii::$app->getRequest()->getQueryParam('order');

        $search = Yii::$app->getRequest()->getQueryParam('search');

        if(isset($search)){
            $params=$search;
        }



        $limit = isset($limit) ? $limit : 10;
        $page = isset($page) ? $page : 1;


        $offset = ($page - 1) * $limit;

        $query = Member::find()
            ->select('*')
            ->asArray(true)
            ->limit($limit)
            ->offset($offset);

        if(isset($params['id'])) {
            $query->andFilterWhere(['id' => $params['id']]);
        }

        if(isset($params['created_at'])) {
            $query->andFilterWhere(['created_at' => $params['created_at']]);
        }
        if(isset($params['updated_at'])) {
            $query->andFilterWhere(['updated_at' => $params['updated_at']]);
        }
        if(isset($params['full_name'])) {
            $query->andFilterWhere(['like', 'full_name', $params['full_name']]);
        }
        if(isset($params['cnic'])){
            $query->andFilterWhere(['like', 'cnic', $params['cnic']]);
        }


        if(isset($order)){
            $query->orderBy($order);
        }


        $additional_info = [
            'page' => $page,
            'size' => $limit,
            'totalCount' => (int)$query->count()
        ];

        return [
            'data' => $query->all(),
            'info' => $additional_info
        ];
    }

    public function beforeValidate()
    {
        if (parent::beforeValidate()) {

            if ($this->isNewRecord) {
                $this->assigned_to = 1;
                $this->created_by = 1;
                $this->created_at = date("Y-m-d H:i:s", time());
                $this->updated_at = date("Y-m-d H:i:s", time());
            } else {
                $this->updated_at = date("Y-m-d H:i:s", time());
            }
            return true;
        } else {
            return false;
        }


    }
}
