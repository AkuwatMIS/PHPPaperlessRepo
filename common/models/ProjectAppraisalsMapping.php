<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project_appraisals_mapping".
 *
 * @property int $id
 * @property int $project_id
 * @property int $appraisal_id
 */
class ProjectAppraisalsMapping extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_appraisals_mapping';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'appraisal_id'], 'required'],
            [['project_id', 'appraisal_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'appraisal_id' => 'Appraisal ID',
        ];
    }

    public function getAppraisal()
    {
        return $this->hasOne(Appraisals::className(), ['id' => 'appraisal_id']);
    }

    public function getProject()
    {
        return $this->hasOne(Projects::className(), ['id' => 'project_id']);
    }
}
