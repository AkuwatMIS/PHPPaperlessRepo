<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "user_transfer_hierarchy".
 *
 * @property int $id
 * @property string $role
 * @property string $type
 * @property string $value
 * @property string $recommeded_by
 * @property string $approved_by
 * @property string $finalized_by
 */
class UserTransferHierarchy extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_transfer_hierarchy';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role', 'type', 'value'], 'required'],
            [['role', 'type', 'recommeded_by', 'approved_by','accepted_by', 'finalized_by'], 'string', 'max' => 20],
            [['value'], 'string', 'max' => 150],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role' => 'Role',
            'type' => 'Type',
            'value' => 'Value',
            'recommeded_by' => 'Recommeded By',
            'approved_by' => 'Approved By',
            'finalized_by' => 'Finalized By',
        ];
    }
}
