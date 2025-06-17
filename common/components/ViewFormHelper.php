<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 3/20/2018
 * Time: 3:18 PM
 */

namespace common\components;

use common\components\Helpers\CacheHelper;
use common\models\Applications;
use common\models\Documents;
use common\models\Loans;
use common\models\SectionFieldsConfigs;
use common\models\ViewConfigs;
use common\models\ViewSectionFields;
use common\models\ViewSections;
use common\models\ViewSectionsFields;
use yii\db\Schema;
use Yii;
use yii\web\Response;

class ViewFormHelper
{
    public static function getSectionsSchema($form, $type, $id = 0)
    {
        /*$schema = CacheHelper::getFormCache($form);
        if (empty($schema)) {*/

        $sections = ViewSections::find()->where(['section_table_name' => $form])->andWhere(['type' => $type])->orderBy('sort_order')->asArray()->all();
        $schema = [];
        if (isset($sections)) {

            foreach ($sections as $section) {
                $data['section_id'] = $section['section_table_name'];
                $data['section_title'] = $section['section_name'];
                $data['questions'] = self::getSectionFields($section['id']);
                $schema['sections'][] = $data;
            }
        }
        $immediate_actions = ViewSections::find()->where(['section_table_name' => $form])->andWhere(['type' => 'immediate_actions'])->orderBy('sort_order')->asArray()->all();
        if (!empty($immediate_actions)) {
            foreach ($immediate_actions as $immediate_action) {
                $schema['immediate_actions'] = self::getImmediateActions($immediate_action['id']);
            }

        }

        /* CacheHelper::setFormCache($form, $schema);
     }*/
        return $schema;
    }

    public static function getSectionFields($section_id)
    {
        $column_array = [];

        $section_fields = ViewSectionFields::find()->where(['section_id' => $section_id])->orderBy('sort_order')->asArray()->all();
        if (isset($section_fields)) {
            foreach ($section_fields as $section_field) {
                $data = [];
                $field_configs = SectionFieldsConfigs::find()->where(['field_id' => $section_field['id']])->andWhere(['!=', 'key_name', 'sub_form'])->all();
                $arrays = [];
                foreach ($field_configs as $k => $v) {
                    $arrays[$k] = array('id' => $v->id, 'parent_id' => $v->parent_id, 'key_name' => $v->key_name, 'value' => $v->value);
                }

                $data['table'] = $section_field['table_name'];
                if ($section_field['table_name'] == "members_phone" && $section_field['field'] == 'phone_type') {
                    $data['column'] = "phone";
                } else if ($section_field['table_name'] == "members_phone" && $section_field['field'] == 'phone') {
                    $data['column'] = "mobile";
                } else if ($section_field['table_name'] == "members_address" && $section_field['field'] == 'address_type') {
                    $data['column'] = "business_address";
                } else if ($section_field['table_name'] == "members_address" && $section_field['field'] == 'address') {
                    $data['column'] = "home_address";
                } else {
                    $data['column'] = $section_field['field'];
                }



                $data1 = self::buildTree($arrays);
                $data1 = self::parser($data1);
                $generic_data = ['question_id' => '', 'is_mandatory' => true, 'is_iterable' => false, 'is_hidden' => false, 'is_editable' => true, 'is_required_text_listner' => false, 'type' => '', 'format' => '', 'label' => '', 'place_holder' => '', 'default_value' => '', 'mask' => '', 'answers' => '', 'default_visibility' => '', 'style' => new \stdClass(), 'constraints' => new \stdClass(), 'dependent_constraints' => [], 'dependent_question' => []];
                //$data = array_merge($generic_data,array_filter($data1));
                $data1 = array_merge($generic_data, $data1);
                $data = array_merge($data, $data1);
                $sub_form_field = SectionFieldsConfigs::find()->where(['field_id' => $section_field['id']])->andWhere(['key_name' => 'sub_form'])->one();
                if (isset($sub_form_field)) {
                    $data['sub_form'] = self::getSectionsSchema($section_field['table_name'], 'sub_form', $sub_form_field->value);
                }
                $column_array[] = $data;
            }
        }

        return $column_array;
    }

    public static function getImmediateActions($immediate_action_id)
    {
        $column_array = [];
        $section_fields = ViewSectionFields::find()->where(['section_id' => $immediate_action_id])->orderBy('sort_order')->asArray()->all();
        if (isset($section_fields)) {
            foreach ($section_fields as $section_field) {
                $data = [];
                $field_configs = SectionFieldsConfigs::find()->where(['field_id' => $section_field['id']])->all();
                $arrays = [];

                foreach ($field_configs as $k => $v) {
                    $arrays[$k] = array('id' => $v->id, 'parent_id' => $v->parent_id, 'key_name' => $v->key_name, 'value' => $v->value);
                }

                $data['table_name'] = $section_field['table_name'];
                $data['column_name'] = $section_field['field'];
                $data1 = self::buildTree($arrays);
                $data1 = self::parser($data1);
                $generic_data = ['parent_key' => '', 'dependent_question' => [], 'dependent_constraints' => []];
                //$data = array_merge($generic_data,array_filter($data1));
                $data1 = array_merge($generic_data, $data1);
                $data = array_merge($data, $data1);

                $column_array[] = $data;
            }
        }

        return $column_array;
    }

    static function buildTree(array $elements, $parentId = 0)
    {
        $branch = array();

        foreach ($elements as $element) {
            if ($element['parent_id'] == $parentId) {
                $children = self::buildTree($elements, $element['id']);
                if ($children) {
                    $element['value'] = $children;
                }
                if ($element['key_name'] == 'dependent_question' || $element['key_name'] == 'header' || $element['key_name'] == 'dependent_constraints') {
                    $branch[$element['key_name']][] = $element;
                } else {
                    $branch[$element['key_name']] = $element;
                }
            }
        }

        return $branch;
    }

    static function parser(array $elements)
    {
        if (!is_array($elements)) {
            return FALSE;
        }
        $branch = array();

        foreach ($elements as $key => $element) {

            if ($key == 'dependent_question' || $key == 'header' || $key == 'dependent_constraints') {
                foreach ($element as $sub_element) {
                    $branch[$key][] = self::parser($sub_element['value']);
                }
            } else {
                if (is_array($element['value'])) {
                    $branch[$key] = self::parser($element['value']);
                } else {
                    if ($key == 'is_mandatory' || $key == 'is_iterable' || $key == 'is_editable' || $key == 'is_required_text_listner' || $key == 'is_required_formatting') {
                        $branch[$key] = boolval($element['value']);
                    } else {
                        if ($element['value'] == null) {
                            $branch[$key] = "";
                        } else {
                            $branch[$key] = $element['value'];
                        }
                    }
                }
            }

        }
        return $branch;
    }

    public static function getDocuments($table)
    {
        $class1 = '/common/models/' . ucfirst($table);
        $class = '\common\models\\' . ucfirst($table);
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $file_path = Yii::getAlias('@anyname') . $class1 . '.php';


        $documents_data = [];
        $documents = Documents::find()->where(['module_type' => $table])->all();
        foreach ($documents as $doc) {
            if ($doc->module_id != 0) {
                if (file_exists($file_path)) {
                    $child_data = $class::find()->all();
                    foreach ($child_data as $data) {
                        if ($data->id == $doc->module_id) {
                            $documents_data[$data->name][] = ['name' => $doc->name, 'is_required' => boolval($doc->is_required)];
                            if (isset($doc->parent_type)) {
                                $parent_documents = Documents::find()->where(['module_type' => $doc->parent_type])->all();
                                $flag = true;
                                foreach ($parent_documents as $parent_document) {
                                    foreach ($documents_data[$data->name] as $d) {
                                        if ($d['name'] == $parent_document->name) {
                                            $flag = false;
                                        }

                                    }
                                    if ($flag) {
                                        $documents_data[$data->name][] = ['name' => $parent_document->name, 'is_required' => boolval($parent_document->is_required)];
                                    }
                                }
                            }
                        } else {
                            $parent_documents = Documents::find()->where(['module_type' => $doc->parent_type])->all();
                            $flag = true;
                            foreach ($parent_documents as $parent_document) {
                                if (isset($documents_data[$data->name])) {
                                    foreach ($documents_data[$data->name] as $d) {
                                        if ($d['name'] == $parent_document->name) {
                                            $flag = false;
                                        }

                                    }
                                }
                                if ($flag) {
                                    $documents_data[$data->name][] = ['name' => $parent_document->name, 'is_required' => boolval($parent_document->is_required)];
                                }
                            }
                        }
                    }

                }

            } else {
                $documents_data[$doc->module_type][] = ['name' => $doc->name, 'is_required' => boolval($doc->is_required)];
            }
        }
        /*print_r($documents_data);
        die();*/
        return $documents_data;
    }

    public static function getDocumentsById($project_id)
    {
        $documents_data = [];
        $documents = Documents::find()->where(['module_type' => 'applications'])->all();
        foreach ($documents as $doc) {
            $documents_data[] = $doc->name;
        }

        $child_documents = Documents::find()->where(['module_type' => 'projects', 'module_id' => $project_id, 'parent_type' => 'applications'])->all();
        if (isset($child_documents)) {
            foreach ($child_documents as $doc) {
                $documents_data[] = $doc->name;
            }
        }
        return $documents_data;
    }
}