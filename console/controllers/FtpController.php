<?php

namespace console\controllers;

use common\components\Helpers\RecoveriesHelper;
use common\components\Helpers\StructureHelper;
use common\models\Actions;
use common\models\ApplicationActions;
use common\models\Areas;
use common\models\AuthAssignment;
use common\models\AuthItem;
use common\models\Branches;
use common\models\CreditDivisions;
use common\models\Disbursements;
use common\models\Donations;
use common\models\FundRequests;
use common\models\GroupActions;
use common\models\Groups;
use common\models\LoanActions;
use common\models\Loans;
use common\models\Members;
use common\models\MembersAddress;
use common\models\MembersEmail;
use common\models\MembersPhone;
use common\models\Operations;
use common\models\ProgressReports;
use common\models\Recoveries;
use common\models\Applications;
use common\models\RecoveryErrors;
use common\models\RecoveryFiles;
use common\models\Regions;
use common\models\Schedules;
use common\models\Users;
use Yii;
use yii\db\Query;
use yii\web\NotFoundHttpException;
use yii\console\Controller;

class FtpController extends Controller
{


    public function actionDbBackup()
    {
        $server_file = 'backup.sql.gz';
        $download_file = 'backup-' . date('Y-m-d') . '.sql.gz';

        //establish ftp connection
        $ftp_server = "ftp.itbeam.com";
        $ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");
        $login = ftp_login($ftp_conn, 'akhuwat@itbeam.com', 'akhuwat@123');

        //download file
        ftp_pasv($ftp_conn, true);
        if (ftp_get($ftp_conn, $download_file, './' . $server_file, FTP_BINARY)) {
            echo "Successfully downloaded as " . $download_file . " .\n";
        } else {
            echo "There was a problem\n";
        }
        ftp_close($ftp_conn);

        // open the gz file
        $gz = gzopen($download_file, 'rb');
        if (!$gz) {
            throw new \UnexpectedValueException(
                "Could not open gzip file.\n"
            );
        }
        $dest = fopen('backup-' . date('Y-m-d') . '.sql', 'wb');
        if (!$dest) {
            gzclose($gz);
            throw new \UnexpectedValueException(
                "Could not open destination file. \n"
            );
        }
        //convert gz file
        stream_copy_to_stream($gz, $dest);
        echo "Successfully extracted " . $download_file . " .\n";
        gzclose($gz);
        fclose($dest);
        //read backup .sql file
        $sql = file_get_contents('backup-' . date('Y-m-d') . '.sql');
        //turn off foreign key checks
        Yii::$app->db4->createCommand("SET foreign_key_checks = 0")->execute();
        ////drop existing tables
        $tables = Yii::$app->db4->schema->getTableNames();
        foreach ($tables as $table) {
            Yii::$app->db4->createCommand()->dropTable($table)->execute();
        }
        //import .sql file
        Yii::$app->db4->createCommand($sql)->execute();
        //turn on foreign key checks
        Yii::$app->db4->createCommand("SET foreign_key_checks = 1")->execute();
        //delete files
        unlink('backup-' . date('Y-m-d') . '.sql');
        unlink($download_file);
        echo 'imported successfully';
    }
}