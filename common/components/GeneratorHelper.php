<?php
/**
 * Created by PhpStorm.
 * User: amina.asif
 * Date: 1/12/2018
 * Time: 10:07 AM
 */

namespace common\components;

use yii\gii\generators\model\Generator;
use yii\gii\generators\crud;
use yii\gii\generators\module;
use yii\gii;

class GeneratorHelper
{
    protected $id;
    public $module;
    public $code_file = [];
    public $generator;

    public $params = [];

    function __construct($id) {
        $this->id = $id;

        $this->module = new  \yii\gii\Module(null);
        if($id == 'model') {
            $this->module->generators[$id] = new Generator();
        } else if($id == 'crud') {
            $this->module->generators[$id] = new crud\Generator();
        } else if($id == 'module') {
            $this->module->generators[$id] = new module\Generator();
        }
        $this->generator = $this->module->generators[$id];

        print "Generator Initiated\n";
    }

    public function generate_model(){
        if ($this->generator->validate()) {
            $files = $this->generator->generate();
            $answers = $this->get_answers($files);
            $this->save($files,$answers,$results,$this->module);
            ///$this->generator->save_1($files,$answers,$results,$this->module);
            /*echo '<pre>';
            print_r('results '.$results);
            die();*/
        } else {
            print_r($this->generator->getErrors());
        }
    }

    public function load_params(){
        $model_params = $this->params;
        $this->generator->load($model_params);
    }

    protected function get_answers($files){
        $answers = [];
        if(!is_array($files)){
            return $answers;
        }
        foreach ($files as $f){
            if(isset($f->id)){
                $answers[$f->id] = 1;
            }
        }
        return $answers;
    }

    public function save($files, $answers, &$results,$module)
    {
        $lines = ['Generating code using template "' . $this->generator->getTemplatePath() . '"...'];
        $hasError = false;
        foreach ($files as $file) {
            $relativePath = $file->getRelativePath();
            $code_file = $file;
            if (isset($answers[$file->id]) && !empty($answers[$file->id]) && $file->operation !== $code_file::OP_SKIP) {
                $error = $this->save_file($code_file,$module);
                if (is_string($error)) {
                    $hasError = true;
                    $lines[] = "generating $relativePath\n<span class=\"error\">$error</span>";
                } else {
                    $lines[] = $file->operation === $code_file::OP_CREATE ? " generated $relativePath" : " overwrote $relativePath";
                }
            } else {
                $lines[] = "   skipped $relativePath";
            }
        }
        $lines[] = "done!\n";
        $results = implode("\n", $lines);

        return !$hasError;
    }

    public function save_file($code_file,$module_name)
    {
        $module = $module_name;
        if ($code_file->operation === $code_file::OP_CREATE) {
            $dir = dirname($code_file->path);

            if (!is_dir($dir)) {
                $mask = @umask(0);
                $result = @mkdir($dir, $module->newDirMode, true);
                @umask($mask);
                if (!$result) {
                    return "Unable to create the directory '$dir'.";
                }
            }
        }
        if (@file_put_contents($code_file->path, $code_file->content) === false) {
            return "Unable to write the file '{$code_file->path}'.";
        } else {
            $mask = @umask(0);
            @chmod($code_file->path, $module->newFileMode);
            @umask($mask);
        }

        return true;
    }
}