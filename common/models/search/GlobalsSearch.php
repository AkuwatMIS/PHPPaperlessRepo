<?php

namespace common\models\search;

use common\models\Groups;
use common\models\Members;
use common\models\reports\Globals;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\SqlDataProvider;

/**
 * LoansSearch represents the model behind the search form about `common\models\Loans`.
 */
class GlobalsSearch extends Globals
{
    public $type;
    public $cnic;
    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            //[['borrower_cnic','grpno','sanction_no'], 'required'],
            [['borrower_cnic','grpno','sanction_no'], 'safe'],
            [['type', 'cnic'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {

        //$query = Globals::find();

        /*$query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        $query->joinWith('branch');
        $query->joinWith('project');
        $query->andFilterWhere(['=', 'sanction_no', $this->sanction_no])
                    ->andFilterWhere(['like', 'members.cnic', $this->borrower_cnic])
                    ->andFilterWhere(['=', 'groups.grp_no', $this->grpno]);*/
        $this->load($params);
        $connection = Yii::$app->db;
        if(!empty($this->sanction_no)){
//            $query="select loans.id,loans.inst_amnt,loans.loan_amount,loans.application_id,loans.project_id,loans.inst_type,
//                        loans.group_id,groups.grp_no as grp_no,projects.name as project_name,
//                        loans.inst_months,loans.date_disbursed,loans.sanction_no,applications.application_no,members.id as member_id,
//                        members.cnic as member_cnic,members.full_name as member_name from loans
//                         INNER JOIN applications on applications.id=loans.application_id
//                         INNER JOIN members on members.id=applications.member_id
//                         INNER JOIN groups on groups.id=loans.group_id
//                         INNER JOIN projects on projects.id=loans.project_id where loans.deleted=0 and sanction_no = '".$this->sanction_no."'";

            $query = "SELECT loans.id, loans.inst_amnt, loans.loan_amount, loans.application_id, loans.project_id, loans.inst_type,
                 loans.group_id, `groups`.grp_no as grp_no, projects.name as project_name,
                 loans.inst_months, loans.date_disbursed, loans.sanction_no, applications.application_no, members.id as member_id,
                 members.cnic as member_cnic, members.full_name as member_name 
                  FROM loans
                  INNER JOIN applications ON applications.id = loans.application_id
                  INNER JOIN members ON members.id = applications.member_id
                  INNER JOIN `groups` ON `groups`.id = loans.group_id
                  INNER JOIN projects ON projects.id = loans.project_id 
                  WHERE loans.deleted = 0 AND sanction_no = '" . $this->sanction_no . "'";
           //$query=Loans::find()->where(['sanction_no'=>$this->sanction_no]);
        }
        else if($this->borrower_cnic){
            $member=Members::find()->select('id')->where(['cnic'=>$this->borrower_cnic])->one();
            if(!empty($member)) {
//                $query="select loans.id,loans.inst_amnt,loans.loan_amount,loans.application_id,loans.project_id,loans.inst_type,
//                            loans.group_id,groups.grp_no as grp_no,projects.name as project_name,
//                            loans.inst_months,loans.date_disbursed,loans.sanction_no,applications.application_no,members.id as member_id,
//                            members.cnic as member_cnic,members.full_name as member_name from loans
//                             INNER JOIN applications on applications.id=loans.application_id
//                             INNER JOIN members on members.id=applications.member_id
//                             INNER JOIN groups on groups.id=loans.group_id
//                             INNER JOIN projects on projects.id=loans.project_id where loans.deleted=0 and loans.application_id in (select id from applications where(applications.member_id='".$member->id."'))";

                $query = "SELECT loans.id, loans.inst_amnt, loans.loan_amount, loans.application_id, loans.project_id, loans.inst_type,
                 loans.group_id, `groups`.grp_no as grp_no, projects.name as project_name,
                 loans.inst_months, loans.date_disbursed, loans.sanction_no, applications.application_no, members.id as member_id,
                 members.cnic as member_cnic, members.full_name as member_name 
                      FROM loans
                      INNER JOIN applications ON applications.id = loans.application_id
                      INNER JOIN members ON members.id = applications.member_id
                      INNER JOIN `groups` ON `groups`.id = loans.group_id
                      INNER JOIN projects ON projects.id = loans.project_id 
                      WHERE loans.deleted = 0 
                        AND loans.application_id IN (
                            SELECT id FROM applications WHERE applications.member_id = '" . $member->id . "')";
                //$query = Loans::find()->where(['in','application_id', Applications::find()->select('id')->where(['member_id'=>$member->id])]);
            }
        }else if($this->grpno){
            $group=Groups::find()->select('id')->where(['grp_no'=>$this->grpno])->one();
            if(!empty($group)) {
//                $query="select loans.id,loans.inst_amnt,loans.loan_amount,loans.application_id,loans.project_id,loans.inst_type,
//                            loans.group_id,groups.grp_no as grp_no,projects.name as project_name,
//                            loans.inst_months,loans.date_disbursed,loans.sanction_no,applications.application_no,members.id as member_id,
//                            members.cnic as member_cnic,members.full_name as member_name from loans
//                             INNER JOIN applications on applications.id=loans.application_id
//                             INNER JOIN members on members.id=applications.member_id
//                             INNER JOIN groups on groups.id=loans.group_id
//                             INNER JOIN projects on projects.id=loans.project_id where loans.deleted=0 and  loans.group_id = '".$group->id."'";

                $query = "SELECT loans.id, loans.inst_amnt, loans.loan_amount, loans.application_id, loans.project_id, loans.inst_type,
                 loans.group_id, `groups`.grp_no as grp_no, projects.name as project_name,
                 loans.inst_months, loans.date_disbursed, loans.sanction_no, applications.application_no, members.id as member_id,
                 members.cnic as member_cnic, members.full_name as member_name 
                      FROM loans
                      INNER JOIN applications ON applications.id = loans.application_id
                      INNER JOIN members ON members.id = applications.member_id
                      INNER JOIN `groups` ON `groups`.id = loans.group_id
                      INNER JOIN projects ON projects.id = loans.project_id 
                      WHERE loans.deleted = 0 AND loans.group_id = '" . $group->id . "'";
                //$query = Loans::find()->where(['group_id' => $group->id]);
            }
        }
        if(!isset($query)){
            $query="select * from loans where id=0";
        }

        $dataProvider = new SqlDataProvider([
            'sql' => $query,
            //'totalCount' => '1',
            'sort' => false,
            'pagination' => [
                //'pageSize' => 10,
            ],
        ]);
        return $dataProvider;

    }



    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchNactaVerification($params)
    {
        $this->load($params);
        $connection = Yii::$app->db;
        $cnic=$params['GlobalsSearch']['cnic'];
        $cnic=preg_replace('/[^0-9]/', '', $cnic);

        $query='SELECT 
            CASE
                WHEN REPLACE(mem.cnic, "-", "")="'.$cnic.'" THEN "Matched in Member"
                WHEN REPLACE(mem.family_member_cnic, "-", "")="'.$cnic.'" THEN "Matched in Family Member"
                WHEN REPLACE(app.other_cnic, "-", "")="'.$cnic.'" THEN "Matched in Beneficiry"
                WHEN REPLACE(grntr.cnic, "-", "")="'.$cnic.'" THEN "Matched in guarantors"
            END as matched_status,

            app.application_no, app.application_date, app.status as application_status,ln.sanction_no, brnch.name as branch_name,mem.cnic as member_cnic, mem.full_name as member_name,mem.family_member_cnic,
            mem.family_member_name, grntr.name as guarantor_name, grntr.cnic as guarantors_cnic,
            app.other_cnic as beneficiary_cnic, app.name_of_other as beneficiary_name, ln.loan_amount, (ln.loan_amount - ln.balance) as recovered_amount, ln.status as loan_status,
            FORMAT(((ln.loan_amount - ln.balance) / ln.loan_amount) * 100, 0) as recovery_percentage


            FROM `applications` app
            LEFT JOIN `members` mem on mem.id=app.member_id
            LEFT JOIN `loans` ln on ln.application_id=app.id
            LEFT JOIN `branches` brnch on brnch.id=app.branch_id
            LEFT JOIN `guarantors` grntr on grntr.group_id=app.group_id

            where REPLACE(mem.cnic, "-", "")="'.$cnic.'" or REPLACE(mem.family_member_cnic, "-", "")="'.$cnic.'" or REPLACE(app.other_cnic, "-", "")="'.$cnic.'" or REPLACE(grntr.cnic, "-", "")="'.$cnic.'"
            ';

        $sql='INSERT INTO nacta_verification_logs (search_date, cnic) VALUES ("'.date('Y-m-d H:i:s').'", "'.$cnic.'")';
        $result = Yii::$app->db->createCommand($sql)->execute();

        $dataProvider = new SqlDataProvider([
            'sql' => $query,
            'totalCount' => '1',
            'sort' => false,
            'pagination'=>false,
            // 'pagination' => [
            // //'pageSize' => 10,
            // ],
        ]);

        return $dataProvider;

    }




    public function searchGlobal($params)
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);
        $query = Globals::find();
        $query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        $query->joinWith('branch');
        $query->joinWith('project');

        $this->load($params);

        $query->andFilterWhere(['=', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'members.cnic', $this->borrower_cnic])
            ->andFilterWhere(['=', 'groups.grp_no', $this->grpno]);
        return $query->all();
    }

    public function searchRandom($params)
    {
        ini_set('memory_limit', '1024M');
        ini_set('max_execution_time', 300);
        $query = Globals::find();
        $query->joinWith('application');
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        $query->joinWith('branch');
        $query->joinWith('project');

        $this->load($params);
        $query->andFilterWhere(['=', 'loans.status', 'collected']);
        /*$query->andFilterWhere(['=', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['like', 'members.cnic', $this->borrower_cnic])
            ->andFilterWhere(['=', 'groups.grp_no', $this->grpno]);*/

        $query->orderBy('loans.updated_at desc');
        $query->limit(5);
        return $query->all();
    }

    public function searchRecovery($params)
    {
        $end_date= strtotime(date('Y-m-t 23:59:59'));
        $query = GlobalsSearch::find();
        $query->select(['loans.*','(select coalesce(sum(r.amount),0) from recoveries r where r.loan_id = loans.id and r.receive_date <= '.$end_date.') as recovery_amount','(select coalesce(sum(s.schdl_amnt),0) from schedules s where s.loan_id = loans.id and s.due_date <= '.$end_date.') as schedule_amount']);
        $query->joinWith('schedules');
        $query->joinWith('recoveries');
        $query->joinWith('application.member');
        $query->joinWith('application.group');
        $query->joinWith('branch');
        $query->joinWith('project');

        $this->load($params);

        $query->andFilterWhere(['=', 'sanction_no', $this->sanction_no])
            ->andFilterWhere(['=', 'members.cnic', $this->borrower_cnic])
            ->andFilterWhere(['loans.status'=> 'collected']);
        /*print_r( $query->one());
        die();*/
        return $query->one();
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'sanction_no' => 'sanction_no',
            'cnic' => 'CNIC',
            'grp_no' => 'Group No',
        ];
    }


}
