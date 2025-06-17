<?php

namespace common\models;

use common\components\Parsers\ApiParser;
use Yii;
use common\components\Helpers\ImageHelper;
use consik\yii2websocket\events\WSClientMessageEvent;
use consik\yii2websocket\WebSocketServer;

class EchoServer extends WebSocketServer
{

    public function init()
    {
        parent::init();

        $this->on(self::EVENT_CLIENT_MESSAGE, function (WSClientMessageEvent $e) {
            //$e->client->send( 'Hello');
            $images = json_decode($e->message);
            //$e->client->send( json_encode($images[0]->image_data));
            $response = [
                'status_type' => 'success',
                'success'=>[],
                'errors'=>[]
            ];

            foreach ($images as $data) {
                $model = ImageHelper::syncImage($data);
                if($model == false)
                {
                    $image['temp_id'] =  $data->temp_id;
                    $image['parent_id'] =  $data->parent_id;
                    $image['error'] =  "Image not uploaded";
                    $response['errors'][] = $image;

                } else {
                    $image = ApiParser::parseImageData($data);
                    $response['success'][] = $image;
                }
                  /*$image_name = $data->image_type.'_'.rand(111111, 999999) . '.png';
                if (ImageHelper::imageUpload($data->parent_id, $data->parent_type, $data->image_type, $image_name, $data->image_data)) {
                    $image['parent_id'] =  $data->parent_id;
                    $image['parent_type'] =  $data->parent_type;
                    $image['image_type'] =  $data->image_type;
                    $image['temp_id'] =  $data->temp_id;
                    $response['success'][] = $image;
                } else {
                    $image['parent_id'] =  $data->parent_id;
                    $image['error'] =  "Image not uploaded";
                    $response['errors'][] = $image;
                }*/
            }
            $e->client->send( json_encode($response));
        });
    }

}