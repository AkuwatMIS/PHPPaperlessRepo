<?php
namespace frontend\modules\test\api\models;

use common\components\Helpers\ActionsHelper;
use common\components\Helpers\AppraisalsHelper;
use common\models\ApplicationActions;
use common\models\Applications;
use common\models\Appraisals;
use yii\base\Model;
use common\models\Users;
use Yii;
use yii\db\Exception;

/**
 * Signup form
 */
class AppraisalsForm extends Appraisals
{
    public $application_id;
    public $date_of_maturity;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['application_id'],'required'],
            //[['application_id'],'exist','targetClass' => 'common\models\Applications', 'message' => 'Application is invalid.','targetAttribute' => ['application_id' => 'id']],
            [['application_id'],'safe'],
            ['application_id', 'validateApplication'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'application_id' => 'Application ID',
        ];
    }

    public function validateApplication($attribute){

        if($this->application_id <= 0) {
            $this->addError('application_id','Application ID is invalid.');
        }
    }

    public function syncAppraisal($data)
    {
        if($this->appraisal_table == 'appraisals_business' || $this->appraisal_table == 'appraisals_housing'){
            $action_model = ApplicationActions::findOne(['parent_id' => $data['application_id'], 'action' => 'social_appraisal']);
            $application =  Applications::findOne(['id' => $this->application_id, 'deleted' => 0]);
            if($action_model->status == 0){
                $errors['error'] = 'Social appraisal must be done before '.$this->name;
                $errors['cnic'] = $application->member->cnic;
                $errors['data_sync_status'] = false;
                return $errors;
            }
        }
        $this->load($data);
        if(!$this->validate())
        {
            $errors['error'] = $this->getErrors();
            $application =  Applications::findOne(['id' => $this->application_id, 'deleted' => 0]);
            if(isset($application))
            {
                $errors['cnic'] = $application->member->cnic;
            }
            $errors['data_sync_status'] = false;
            return $errors;
        }
        $class = $this->getModel();
        $model = $class::find()->where(['application_id' => $data['application_id'], 'deleted' => 0])->one();
        if(isset($model))
        {
            $model->attributes = $data;
        } else {

            $model = new $class();
            /*$model = yii::createObject([
                'class' => $class,
            ]);*/
            $model->attributes = $data;
        }

        $address_column = $this->name. '_address';
        $model->$address_column = AppraisalsHelper::getAppraisalAddress($model->latitude,$model->longitude);


        $transaction = Yii::$app->db->beginTransaction();
        //try {

        if (!$model->save()) {

            $transaction->rollBack();
            $errors['error'] = $model->getErrors();
            $application =  Applications::findOne(['id' => $model->application_id, 'deleted' => 0]);
            if(isset($application))
            {
                $errors['cnic'] = $application->member->cnic;
            }
            $errors['data_sync_status'] = false;
            return $errors;
        } else {

                $action_model = ActionsHelper::updateAction('application',$model->application_id,$this->name);
                $application =  Applications::findOne(['id' => $this->application_id, 'deleted' => 0]);
                if(isset($action_model['sync_status']) && $action_model['sync_status'] == false)
                {
                    $transaction->rollBack();
                    $errors['error'] = $action_model->getErrors();

                    if(isset($application))
                    {
                        $errors['cnic'] = $application->member->cnic;
                    }
                    $errors['data_sync_status'] = false;
                    return $errors;
                } else {
                    ActionsHelper::insertActions('appraisal',$application->project_id,$application->id,$model->created_by,1);
                    $transaction->commit();
                }

            return $model;
        }
        /*} catch (Exception $e) {
            print_r($e);
            print_r($model);
            die('hsdhj');
            $transaction->rollBack();
        }*/

    }
}
