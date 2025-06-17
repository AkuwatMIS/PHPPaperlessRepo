<?php

namespace common\models\search;


use common\models\Applications;
use common\models\Loans;
use common\models\reports\Duelist;
use common\models\reports\Portfolio;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LoansSearch represents the model behind the search form about `common\models\Loans`.
 */
class CreditSearch extends Applications
{
    /**
     * @inheritdoc
     */

    public $sanction_no;
    public function rules()
    {
        return [
            [['branch_id'],'required'],
            [['id', 'application_id', 'loan_amount', 'inst_amnt', 'inst_months', 'disbursement_id', 'branch_id', 'area_id', 'region_id', 'br_serial', 'created_by', 'is_lock', 'project_id'], 'integer'],
            [['dateapprove', 'recovery', 'cheque_no', 'acccode', 'inst_type', 'date_disbursed', 'dateexpiry', 'cheque_dt', 'dsb_status', 'funding_line', 'loan_expiry', 'remarks', 'old_sanc_no', 'sanction_no', 'expiry_date', 'dt_entry'], 'safe'],
            [['due', 'overdue', 'balance'], 'number'],
            [['region_name', 'area_name', 'branch_name'], 'safe'],
            [['name', 'cnic', 'gender', 'parentage', 'address', 'mobile', 'grpno', 'province_id', 'city_id', 'district_id', 'division_id', /*'report_date',*/ 'application_date','branch_ids'], 'safe']
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
    public function search($params, $export = false)
    {
        ini_set('memory_limit', '16128M'); // or you could use 1G

        $this->load($params);
        $cond = '';
        $rec_cond = '';
        if (isset($this->branch_id) && !empty($this->branch_id)) {
            $cond = " and applications.branch_id=" . $this->branch_id . "";
        }
        if (isset($this->application_date) && !empty($this->application_date)) {
           /* $date = explode(' - ', $this->application_date);
            $date[1]=$date[1].'-23:59';
            $cond .= " and (applications.application_date  BETWEEN " . strtotime($date[0]) . " and " . strtotime($date[1]) . ")";
            $rec_cond .= " and recoveries.receive_date  BETWEEN " . strtotime($date[0]) . " and " . strtotime($date[1]) . "";*/
           /* $month = date('Y-m-01', strtotime($this->application_date));
            print_r($month);die();*/
           // $application_date = date('Y-'."$month".'-d');
            $date_1 = strtotime(date('Y-m-01', strtotime($this->application_date)));
            $date_2= strtotime(date('Y-m-t', strtotime($this->application_date)));
            $cond .= " and (applications.application_date  BETWEEN ". "'$date_1'"  . ' and ' .  "'$date_2'" .")";

        } else {
            $application_date = date('Y-m-d');
            $date_1 = strtotime(date('Y-m-01', strtotime($application_date)));
            $date_2= strtotime(date('Y-m-t', strtotime($application_date)));
            $cond .= " and (applications.application_date  BETWEEN ". "'$date_1'"  . ' and ' .  "'$date_2'" .")";

        }
        if (isset($this->project_id) && !empty($this->project_id)) {
            $cond .= " and applications.project_id=" . $this->project_id . "";
        }
        if (isset($this->sanction_no) && !empty($this->sanction_no)) {
            $cond .= " and loans.sanction_no='" . $this->sanction_no . "'";
        }
        if (isset($this->name) && !empty($this->name)) {
            $cond .= " and members.full_name='" . $this->name . "'";
        }
        if (isset($this->parentage) && !empty($this->parentage)) {
            $cond .= " and members.parentage='" . $this->parentage . "'";
        }

        $connection = Yii::$app->db;
        $query = "SELECT application_date,application_no as app_no,full_name,cnic,applications.id as app_id,members.id as member,
                         loans.sanction_no,applications.status,loans.date_disbursed , groups.grp_no,loans.cheque_no ,
                         regions.name as region, branches.name as branch, areas.name as area,projects.name as project,
                         (select count(application_id) from loans  INNER JOIN applications ON loans.application_id=applications.id
                          INNER JOIN members ON applications.member_id = members.id where members.id = member and loans.id !=0) as member_count
                            FROM applications
                
                    
                       INNER JOIN members ON applications.member_id = members.id
                       LEFT JOIN loans ON loans.application_id=applications.id
                       LEFT JOIN groups ON applications.group_id = groups.id
                       INNER JOIN branches ON branches.id = applications.branch_id
                       INNER JOIN areas ON areas.id = applications.area_id
                       INNER JOIN regions ON regions.id = applications.region_id
                       INNER JOIN projects ON projects.id = applications.project_id
                    
              WHERE 1  " . $cond . "
              AND (applications.deleted=0)  ";
        $credit = $connection->createCommand($query)->queryAll();
        //echo '<pre>';print_r($credit);die('blah');
        return $credit;
        if ($export) {
            return $credit;
        } else {
            return $dataProvider;
        }

    }
}
