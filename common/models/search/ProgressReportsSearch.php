<?php

namespace common\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\ProgressReports;

/**
 * ProgressReportsSearch represents the model behind the search form about `common\models\ProgressReports`.
 */
class ProgressReportsSearch extends ProgressReports
{
    /**
     * @inheritdoc
     */
    public $_report_date;
    public $_created_at;
    public $_updated_at;
    public $project_name;
    public function rules()
    {
        return [
            [['id', 'report_date', 'project_id', 'do_update', 'do_delete', 'assigned_to', 'created_by', 'updated_by', 'created_at', 'updated_at'], 'integer'],
            [['gender', 'period', 'comments', 'status', 'is_verified', 'deleted','project_name'], 'safe'],
            [['_report_date','_created_at','_updated_at'], 'safe'],
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
        //"DATE_FORMAT (FROM_UNIXTIME(progress_reports.updated_at), '%Y-%m-%d') as updated_at","DATE_FORMAT (FROM_UNIXTIME(progress_reports.created_at), '%Y-%m-%d') as created_at"
        $query = ProgressReports::find()->select(["progress_reports.id","report_date","progress_reports.created_at","progress_reports.updated_at","project_id","gender","period","comments","progress_reports.status","is_verified","do_update","do_delete","progress_reports.deleted","progress_reports.assigned_to","progress_reports.created_by","progress_reports.updated_by","DATE_FORMAT (FROM_UNIXTIME(progress_reports.report_date), '%Y-%m-%d') as _report_date","DATE_FORMAT (FROM_UNIXTIME(progress_reports.updated_at), '%Y-%m-%d') as _updated_at","DATE_FORMAT (FROM_UNIXTIME(progress_reports.created_at), '%Y-%m-%d') as _created_at"]);
        $query->joinWith('project');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);

        $query->andFilterWhere([
            'id' => $this->id,
            'project_id' => $this->project_id,
            'do_update' => $this->do_update,
            'do_delete' => $this->do_delete,
            'assigned_to' => $this->assigned_to,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);
        if(!empty($this->_report_date)){
            $query->andHaving(['_report_date'=>$this->_report_date]);
        }
        if(!empty($this->_updated_at)){
            $query->andHaving(['_updated_at'=>$this->_updated_at]);
        }
        if(!empty($this->_created_at)){
             $query->andHaving(['_created_at'=>$this->_created_at]);
        }
       // $query->having(['_updated_at'=>$this->_updated_at]);
       // $query->having(['_created_at'=>$this->_created_at]);

        $query->andFilterWhere(['like', 'gender', $this->gender])
            //->andFilterWhere(['like', 'progress_reports.report_date', ($this->report_date)])
            //->andFilterWhere(['like', 'progress_reports.updated_at', ($this->updated_at)])
            //->andFilterWhere(['like', 'progress_reports.created_at', ($this->created_at)])

            ->andFilterWhere(['like', 'period', $this->period])
            ->andFilterWhere(['like', 'comments', $this->comments])
            ->andFilterWhere(['like', 'progress_reports.status', $this->status])
            ->andFilterWhere(['like', 'is_verified', $this->is_verified])
            ->andFilterWhere(['like', 'progress_reports.deleted', $this->deleted])
        ->andFilterWhere(['like', 'projects.code', $this->project_name]);

        return $dataProvider;
    }
}
