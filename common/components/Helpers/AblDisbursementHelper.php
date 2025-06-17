<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

use common\models\Applications;
use common\models\ApplicationsCib;
use common\models\Branches;
use common\models\CibTypes;
use common\models\ConnectionBanks;
use common\models\LoanTranches;
use common\models\Visits;
use Yii;

class AblDisbursementHelper
{
    public static function actionPushDisbursement()
    {
        // Fetch data from Applications table
//        $applications = Applications::find()
//            ->where(['project_id' => 1])
//            ->all();
//
//        $csvData = [];
//        foreach ($applications as $app) {
//            $csvData[] = [
//                $app->from_account,
//                $app->amount,
//                $app->to_account,
//                $app->email,
//                $app->description,
//            ];
//        }

        // CSV Data
        $csvData = [
            ['06650010001169410053', '10209.00', '01630010000064620044', 'bank@abl.com', 'SalaryDec']
        ];

        print_r($csvData);

        // Create CSV File
        $csvFileName = 'disbursement.csv';
        $csvFilePath = Yii::getAlias('@runtime/') . $csvFileName;

        $fp = fopen($csvFilePath, 'w');
        if ($fp === false) {
            return 'Failed to create file.';
        }

        foreach ($csvData as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);

        // Upload
        $apiUrl = 'http://10.84.12.95/api/v1/integration/file-upload';
        $apiKey = '123456789';

        $curlFile = new \CURLFile($csvFilePath, 'text/csv', $csvFileName);

        $postData = [
            'FI' => 'SALARY_IFT',
            'File' => $curlFile,
        ];

        Yii::info("CSV file generated at: $csvFilePath", __METHOD__);
        Yii::info("CSV file contents:\n" . file_get_contents($csvFilePath), __METHOD__);

        $headers = [
             'x-api-key: ' . $apiKey
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        print_r($response);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        // Log or return everything for inspection
        Yii::info("cURL HTTP Status: $httpCode", __METHOD__);
        Yii::info("cURL Response: $response", __METHOD__);
        Yii::info("cURL Error: $error", __METHOD__);

        @unlink($csvFilePath); // Clean up

        if ($httpCode === 200) {
            return 'Upload success! API Response: ' . $response;
        } else {
            return 'Upload failed! Status: ' . $httpCode . ' | Response: ' . $response;
        }
    }



}