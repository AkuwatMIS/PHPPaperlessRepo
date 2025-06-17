<?php

namespace frontend\modules\test\api\models;

use Yii;

/**
 * This is the model class for table "appraisals".
 *
 * @property int $id
 * @property string $name
 * @property string $appraisal_table
 * @property int $status
 */
class Appraisals extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'appraisals';
    }
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'appraisal_table'], 'required'],
            [['status'], 'integer'],
            [['name'], 'string', 'max' => 100],
            [['appraisal_table'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'appraisal_table' => 'Appraisal Table',
            'status' => 'Status',
        ];
    }

    public function getModel()
    {
        $model = ucwords(str_replace('_',' ',$this->appraisal_table));
        $model = str_replace(' ','',$model);
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        if (file_exists(Yii::getAlias('@anyname').'/api/models/' . $model . '.php')) {
            $model_class = 'frontend\modules\test\api\models\\' . $model;
            return $model_class;
        }
    }

}
