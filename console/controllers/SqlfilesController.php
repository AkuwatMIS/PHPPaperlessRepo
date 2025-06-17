<?php
namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\FileHelper;

class SqlfilesController extends Controller
{


    public function actionImport()
    {
        Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $files = FileHelper::findFiles(Yii::getAlias('@anyname') . '/common/sql_files', ['only' => ['*.sql']]);

        if(isset($files)) {
            foreach ($files as $file) {
                $table = explode('.sql', basename($file));
                $truncate_table = Yii::$app->db->createCommand()->truncateTable($table[0])->execute();
                if (Yii::$app->db->driverName == 'mysql') {
                    $result = Yii::$app->db->createCommand(file_get_contents($file))->execute();
                }

                if (Yii::$app->db->driverName == 'sqlsrv') {
                    $sql = 'sqlcmd -S L-MIS-005\SQLExpress -d db -U sa -P Pakistan123 -i ';
                    $sql .= $file;
                    $result = shell_exec($sql);
                }
            }
        }

    }

    public function actionDb()
    {
        \Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../'));
        $db_structures = FileHelper::findFiles(Yii::getAlias('@anyname') . '/db/structure', ['only' => ['*.sql']]);
        if(isset($db_structures)) {
            foreach ($db_structures as $db_structure) {
                if (Yii::$app->db->driverName == 'mysql') {
                    $result = Yii::$app->db->createCommand(file_get_contents($db_structure))->execute();
                }

                if (Yii::$app->db->driverName == 'sqlsrv') {
                    $sql = 'sqlcmd -S L-MIS-005\SQLExpress -d db -U sa -P Pakistan123 -i ';
                    $sql .= $db_structure;
                    $result = shell_exec($sql);
                }
            }
        }
        $files = FileHelper::findFiles(Yii::getAlias('@anyname') . '/db/data', ['only' => ['*.sql']]);

        if(isset($files)) {
            foreach ($files as $file) {
                /*$table = explode('.sql', basename($file));
                $truncate_table = Yii::$app->db->createCommand()->truncateTable($table[0])->execute();*/
                if (Yii::$app->db->driverName == 'mysql') {
                    $result = Yii::$app->db->createCommand(file_get_contents($file))->execute();
                }

                if (Yii::$app->db->driverName == 'sqlsrv') {
                    $sql = 'sqlcmd -S L-MIS-005\SQLExpress -d db -U sa -P Pakistan123 -i ';
                    $sql .= $file;
                    $result = shell_exec($sql);
                }
            }
        }
    }
}