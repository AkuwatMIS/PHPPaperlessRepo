<?php
namespace common\widgets;

use common\models\Users;
use Yii;
use yii\base\Widget;

class LogsWidget extends Widget
{
    public $id;
    public $table;
    public $field;
    public $logs;

    public function init(){
        parent::init();
        if($this->table==null) {
            print_r('table name cannot blank');
            die();
        } else {

            $model_class = 'common\models\\' . ucfirst($this->table.'Logs');
            Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));


            if (file_exists( Yii::getAlias('@anyname').'/common/models/' . ucfirst($this->table.'Logs') . '.php')) {
                if ($this->id == null && $this->field == null) {
                    $this->logs = $model_class::find()->where(['action' => 'CHANGE'])->orderBy(['stamp'=> SORT_DESC])->all();
                } else if ($this->id != null && $this->field != null){
                    $this->logs = $model_class::find()->where(['id' => $this->id,'field' => $this->field, 'action' => 'CHANGE'])->orderBy(['stamp'=> SORT_DESC])->all();
                } else if ($this->id != null && $this->field == null){
                    $this->logs = $model_class::find()->where(['id' => $this->id, 'action' => 'CHANGE'])->orderBy(['stamp'=> SORT_DESC])->all();
                }
            } else {
                print_r('Logs Model not found');
                die();
            }
        }
    }

    public function run()
    {
        if ($this->id == null && $this->field == null) {
            return $this->render('logs/view_logs',['model_logs'=> $this->logs]);
        }  else if ($this->id != null && $this->field != null){
            return $this->render('logs/view_record_field_logs',['model_logs'=> $this->logs]);
        }  else if ($this->id != null && $this->field == null){
            return $this->render('logs/view_field_logs',['model_logs'=> $this->logs]);
        }
    }

}
