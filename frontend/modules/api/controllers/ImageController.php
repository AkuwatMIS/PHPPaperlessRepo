<?php

namespace frontend\modules\api\controllers;


use common\components\Helpers\ApplicationHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\JsonHelper;
use common\components\Helpers\UsersHelper;
use common\models\Verification;
use yii\filters\AccessControl;
use frontend\modules\api\behaviours\Verbcheck;
use frontend\modules\api\behaviours\Apiauth;
use Yii;


class ImageController extends RestController
{
    public $rbac_type = 'api';
    public function behaviors()
    {

        $behaviors = parent::behaviors();

        return $behaviors + [

                'apiauth' => [
                    'class' => Apiauth::className(),
                    'exclude' => [],
                    'callback'=>[]
                ],
                'access' => [
                    'class' => AccessControl::className(),
                    'denyCallback' => function ($rule, $action) {
                        JsonHelper::asJson($this->sendFailedResponse('401','You are not allowed to perform this action.'));
                    },
                    'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type,UsersHelper::getUserIdByAccessToken(Yii::$app->getRequest()->getHeaders()->get('x-access-token'))),
                ],
                'verbs' => [
                    'class' => Verbcheck::className(),
                    'actions' => [
                        'upload' => ['POST'],
                    ],
                ],

            ];
    }

    public function actionUpload()
    {
        /*$json = '
        [
{"temp_id": 2,
    "parent_id": 14652,
    "parent_type": "members",
    "image_type": "left_thumb",
    "image_data": "AwFVKIgAAAIAAgACAAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANkiJeNJdjvm8ZpV52IBB+K4gM/1yIzf9RmY4fFKSd31oppb8JqhwXGa5avzu1lB8PNcHfYsCmnxaHo90mmGIdS6CknU4jT902pmd9UhANenM55tpWkOQbaDOQ+22/ELs3n1V4D4tLuRWLi5kbFGH5IJRiORWU4Xk5KZV5bDVQeW65EBk5O9P5Ob7TuT5Akvksm6G0ULpn1Q5CQVUVOlmzAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA="},
{"temp_id": 2,
    "parent_id": 14652,
    "parent_type": "members",
    "image_type": "right_thumb",
    "image_data": "AwFVKIgAAAIAAgACAAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAANkiJeNJdjvm8ZpV52IBB+K4gM/1yIzf9RmY4fFKSd31oppb8JqhwXGa5avzu1lB8PNcHfYsCmnxaHo90mmGIdS6CknU4jT902pmd9UhANenM55tpWkOQbaDOQ+22/ELs3n1V4D4tLuRWLi5kbFGH5IJRiORWU4Xk5KZV5bDVQeW65EBk5O9P5Ob7TuT5Akvksm6G0ULpn1Q5CQVUVOlmzAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA="}
]

        ';
        $gz = gzencode($json);
        $base_64 = base64_encode($gz);
        print_r($base_64);
        die();
        $base_64_decode = base64_decode($base_64);
        $gzdecode = gzdecode($base_64_decode);
        print_r($gzdecode);
        die();*/
        $request = $this->request;
        $base64_decode_data = base64_decode($request['image']);
        $images_data = json_decode(gzdecode($base64_decode_data));
        $response = [];
        foreach ($images_data as $data)
        {
            /*print_r($data->parent_id);
            die();*/
            $image = ImageHelper::syncImage($data);
            if($image != false)
            {
                $response[] = ['parent_id' => $data->parent_id,'parent_type' => $data->parent_type, 'image_type' => $data->image_type,'temp_id' =>$data->temp_id];
            } /*else {
                $response['error'][] = $data;
            }*/
        }

        return $this->sendSuccessResponse(200, $response);
    }
    public function actiongz(){

    }
    public function actionUpload_()
    {
        if(isset($this->request['parent_id']) && isset($this->request['parent_type']) && isset($this->request['image_data']) && isset($this->request['image_type']))
        {
            //$thumb_impressions_array = ['thumb_impression','right_index','left_index','right_thumb', 'left_thumb'];
            //$thumb_impressions_array = array();
            $image_name = $this->request['image_type'].'_'.rand(111111, 999999) . '.png';
            /*if(in_array($this->request['image_type'], $thumb_impressions_array))
            {
                $image = ImageHelper::ThumbImpressionsUpload($this->request['parent_id'], $this->request['parent_type'], $this->request['image_type'], $image_name, $this->request['image_data']);
                if($image) {
                    $class = '\common\models\\' . ucfirst($this->request['parent_type']);
                    $model = $class::findOne(['id' => $this->request['parent_id'], 'deleted' => 0]);
                    if (isset($model)) {
                        $model[$this->request['image_type']] = $image;
                        $model->save();
                        $response['message'] = "Image Upload Successfully";
                        return $this->sendSuccessResponse(200, $response);
                    } else {
                        return $this->sendFailedResponse(400, "Invalid Record Requested.");
                    }
                } else {
                    return $this->sendFailedResponse(400, "Image not upload.");
                }

            } else {*/
            if (ImageHelper::imageUpload($this->request['parent_id'], $this->request['parent_type'], $this->request['image_type'], $image_name, $this->request['image_data'])) {
                $response['message'] = "Image Upload Successfully";
                return $this->sendSuccessResponse(200, $response);
            } else {
                return $this->sendFailedResponse(400, "Image not upload.");
            }
            //}

        } else {
            return $this->sendFailedResponse(400, "Request Format not Valid");
        }
    }
}