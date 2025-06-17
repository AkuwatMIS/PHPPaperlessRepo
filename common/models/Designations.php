<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "designations".
 *
 * @property int $id
 * @property string $name
 * @property string $desig_label
 * @property string $code
 * @property int $sorting
 * @property int $network
 * @property int $progress_report
 * @property int $projects
 * @property int $districts
 * @property int $products
 * @property int $analysis
 * @property int $search_loan
 * @property int $news
 * @property int $maps
 * @property int $staff
 * @property int $links
 * @property int $filters
 */
class Designations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'designations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['sorting', 'network', 'progress_report', 'projects', 'districts', 'products', 'analysis', 'search_loan', 'news', 'maps', 'staff', 'links', 'filters','housing','audit'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['desig_label'], 'string', 'max' => 100],
            [['code'], 'string', 'max' => 10],
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
            'desig_label' => 'Desig Label',
            'code' => 'Code',
            'sorting' => 'Sorting',
            'network' => 'Network',
            'progress_report' => 'Progress Report',
            'projects' => 'Projects',
            'districts' => 'Districts',
            'products' => 'Products',
            'analysis' => 'Analysis',
            'search_loan' => 'Search Loan',
            'news' => 'News',
            'maps' => 'Maps',
            'staff' => 'Staff',
            'links' => 'Links',
            'filters' => 'Filters',
            'housing' => 'Housing',
            'audit' => 'Audit',
        ];
    }
}
