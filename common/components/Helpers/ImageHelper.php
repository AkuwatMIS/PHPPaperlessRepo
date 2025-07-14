<?php
/**
 * Created by PhpStorm.
 * User: umairawan
 * Date: 10/08/17
 * Time: 5:20 PM
 */

namespace common\components\Helpers;

use common\components\Parsers\ApiParser;
use common\models\ConfigRules;
use common\models\Images;
use common\models\Members;
use common\models\NadraVerisys;
use common\models\Users;
use common\models\Visits;
use Yii;
use yii\db\Exception;

class ImageHelper{

    public static function getAttachmentApiPath()
    {
        $path = "https://credit.akhuwat.org.pk/api/attachments/image";
        return $path;
    }

    public static function getAttachmentPath()
    {
        $path = ConfigRules::findOne(['group'=>'configurations','key'=> 'attachment_path']);
        return $path->value;
    }

    public static  function getImageFromDisk($type,$id,$file_name, $download)
    {

        $attachment_path = self::getAttachmentPath();
        $types = [
            'no_image'=>'uploads/' ,
            'users'=>'uploads/users/' ,
            'members' =>'uploads/members/'.$id.'/' ,
            'applications' => 'uploads/applications/'.$id.'/',
            'guarantors' => 'uploads/guarantors/'.$id.'/',
            'visits' => 'uploads/visits/'.$id.'/',
            'projects' =>'uploads/projects/',
            'products' =>'uploads/products/',
            'activities' =>'uploads/activities/',
            'awp_files' =>'uploads/awp_files/',
            'portfolio' =>'exports/portfolio/',
            'duelists' =>'exports/duelists/',
            'blacklist' =>'blacklist/',
            'dynamic_reports' =>'dynamic_reports/',
            'banners'=>'uploads/banners/',
            'provinces'=>'uploads/provinces/'
        ];
        $file_url = $attachment_path.$types[$type].'/'.$file_name;
        if($download=="true")
        {
            header('Content-Type: application/octet-stream');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\"");
            readfile($file_url);
        } else {
            if (file_exists($file_url)) {
                $imageData = base64_encode(file_get_contents($file_url));
            } else {
                $imageData =  'noimage.png';
            }

            return "data:image/jpeg;base64,".$imageData;
        }
    }

    public static function imageUpload($parent_id,$parent_type,$image_type,$image_name,$base_code)
    {
        $finger_print_array = array('right_index','left_index','right_thumb','left_thumb','thumb_impression');
        if(in_array($image_type,$finger_print_array)){
            if($parent_type == 'members'){
                $member = Members::find()->where(['id'=>$parent_id])->asArray()->one();
                $type = $image_type;
                $update_query = "update members set ".$type." = '".$base_code."' where id = '".$member['id']."' ";
                Yii::$app->db->createCommand($update_query)->execute();
                $image = new Images();
                $image->parent_id = $parent_id;
                $image->parent_type = $parent_type;
                $image->image_type = $image_type;
                $image->image_name = $image_name;
                return $image;
            }else if($parent_type == 'users'){
                $user = Users::find()->where(['id'=>$parent_id])->asArray()->one();
                $type = $image_type;
                $update_query = "update members set ".$type." = '".$base_code."' where id = '".$user['id']."' ";
                Yii::$app->db->createCommand($update_query)->execute();
                $image = new Images();
                $image->parent_id = $parent_id;
                $image->parent_type = $parent_type;
                $image->image_type = $image_type;
                $image->image_name = $image_name;
                return $image;
            }
        }else{
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $image = Images::find()->where(['parent_id' => $parent_id, 'parent_type' => $parent_type, 'image_type' => $image_type])->one();
                if(isset($image))
                {
                    if ($image_type != 'nadra_document') {
                        $img = $image->image_name;
                        $image->image_name = $image_name;
                        $flag = $image->save();
                    }
                } else {
                    $image = new Images();
                    $image->parent_id = $parent_id;
                    $image->parent_type = $parent_type;
                    $image->image_type = $image_type;
                    $image->image_name = $image_name;
                    $flag = $image->save();
                }
                if ($image_type == 'nadra_document') {
                    $nadraModel = NadraVerisys::find()->where(['member_id'=>$parent_id])
                        ->andWhere(['status'=>0])
                        ->andWhere(['deleted'=>0])
                        ->orderBy([
                            'id' => SORT_DESC
                        ])
                        ->one();
                    if(!empty($nadraModel) && $nadraModel!=null){
                        $nadraModel->status = 1;
                        $nadraModel->document_name = $image_name;
                        $nadraModel->save();
                    }
                }

                /*if($flag == false)
                {
                    $transaction->rollBack();
                }*/

                if($flag)
                {
                    //Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../../'));
                    //$path =Yii::getAlias('@anyname'). '/frontend/web/uploads/'.$parent_type.'/'.$parent_id.'/';
                    $path = self::getAttachmentPath().'uploads/'.$parent_type.'/'.$parent_id.'/';

                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                        chmod($path, 0777);
                    }

                    if(isset($img)) {
                        if (file_exists($path . $img)) {
                            unlink($path . $img);
                        }
                    }
                    $image_base64 = base64_decode($base_code);
                    $file = $path . $image_name;
                    if(file_put_contents($file, $image_base64) !== false)
                    {
                        $transaction->commit();
                        //return $flag;
                        return $image;
                    } else {
                        $transaction->rollBack();
                        return false;
                    }

                } else {
                    $transaction->rollBack();
                    return false;
                }
            }
            catch (Exception $e) {
                $transaction->rollBack();
            }
        }
    }

    public static function imageUploadApp($parent_id,$parent_type,$image_name,$base_code)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $nadraModel = NadraVerisys::find()->where(['application_id'=>$parent_id])
                ->andWhere(['status'=>0])
                ->andWhere(['deleted'=>0])
                ->orderBy([
                    'id' => SORT_DESC
                ])
                ->one();
            if(!empty($nadraModel) && $nadraModel!=null){
                $nadraModel->status = 0;
                $nadraModel->document_name = $image_name;
                if($nadraModel->save()){
                    $flag = true;
                }else{
                    $flag = false;
                }
            }else{
                $flag = false;
            }

            if($flag)
            {
                //Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../../'));
                //$path =Yii::getAlias('@anyname'). '/frontend/web/uploads/'.$parent_type.'/'.$parent_id.'/';
                $path = self::getAttachmentPath().'uploads/'.$parent_type.'/'.$parent_id.'/';

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                    chmod($path, 0777);
                }

                if(isset($img)) {
                    if (file_exists($path . $img)) {
                        unlink($path . $img);
                    }
                }
                $image_base64 = base64_decode($base_code);
                $file = $path . $image_name;
                if(file_put_contents($file, $image_base64) !== false)
                {
                    $transaction->commit();
                    //return $flag;
                    return $nadraModel;
                } else {
                    $transaction->rollBack();
                    return false;
                }

            } else {
                $transaction->rollBack();
                return false;
            }
        }
        catch (Exception $e) {
            $transaction->rollBack();
            print_r($e->getMessage());
            die();
        }
    }

    public static function updateImage($data)
    {
        $finger_print_array = array('right_index','left_index','right_thumb','left_thumb','thumb_impression');
        if(in_array($data->image_type,$finger_print_array)){
            if($data->parent_type == 'members'){
                $member = Members::find()->where(['id'=>$data->parent_id])->asArray()->one();
                $type = $data->image_type;
                $update_query = "update members set ".$type." = '".$data->image_data."' where id = '".$member['id']."' ";
                Yii::$app->db->createCommand($update_query)->execute();
                return true;
            }else if($data->parent_type == 'users'){
                $user = Users::find()->where(['id'=>$data->parent_id])->one();
                $user->$data->image_type = $data->image_data;
                $user->save();
                return true;
            }
        }
    }

    public static function ThumbImpressionsUpload($parent_id,$parent_type,$image_type,$image_name,$base_code)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $class = '\common\models\\' . ucfirst($parent_type);
            $model = $class::find()->where(['id' => $parent_id])->one();
            $pic_url = $parent_type . "/" . $parent_id . "/" . $image_name;
            if (isset($model)) {

                $model->$image_type = $pic_url;
                //return ('here');
                $flag = $model->save();
                //return ('here 1');
                //die();


            }

            if($flag) {

                //Yii::setAlias('@anyname', realpath(dirname(__FILE__) . '/../../../'));
                //$path =Yii::getAlias('@anyname'). '/frontend/web/uploads/'.$parent_type.'/'.$parent_id.'/';
                $path = self::getAttachmentPath().'uploads/'.$parent_type.'/'.$parent_id.'/';

                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                    chmod($path, 0777);
                }

                if (isset($model->$image_type)) {
                    if (file_exists(self::getAttachmentPath().'uploads/' . $model->$image_type)) {
                        unlink(self::getAttachmentPath().'uploads/' . $model->$image_type);
                    }
                }

                $image_base64 = base64_decode($base_code);
                $file = $path . $image_name;
                if (file_put_contents($file, $image_base64) !== false) {
                    $transaction->commit();
                    return $model;
                } else {
                    $transaction->rollBack();
                    return false;
                }
            }  else {
                $transaction->rollBack();
                return false;
            }
        }
        catch (Exception $e) {
            $transaction->rollBack();
        }
    }

    public static function getBase64Image($url)
    {
        $path = self::getAttachmentPath().'uploads\\' . $url ;
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = base64_encode($data);
        //$base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public static function syncImage($data)
    {
        $image_name = $data->image_type.'_'.rand(111111, 999999) . '.png';

        /*$thumb_impressions_array = ['thumb_impression','right_index','left_index','right_thumb', 'left_thumb'];
        if(in_array($data->image_type, $thumb_impressions_array)) {
            $model = self::ThumbImpressionsUpload($data->parent_id, $data->parent_type, $data->image_type, $image_name, $data->image_data);
        } else {*/
            $model = self::imageUpload($data->parent_id, $data->parent_type, $data->image_type, $image_name, $data->image_data);
        //}

        return $model;
    }

    public static function syncImageObject($data)
    {
        $image_name = $data['image_type'].'_'.rand(111111, 999999) . '.png';
        $model = self::imageUpload($data['parent_id'], $data['parent_type'], $data['image_type'], $image_name, $data['image_data']);

        return $model;
    }

    public static function getImageUrl($id,$type,$parent_type)
    {
        $pic_url = '';
        $image = Images::findOne(['parent_id' => $id, 'parent_type' => $parent_type, 'image_type' => $type]);
        if (!empty($image)) {
            $user_image = (!empty($image->image_name)) ? ($image->image_name) : 'noimage.png';
            $pic_url = "/uploads/" .$image->parent_type . "/" . $image->parent_id . "/" . $user_image;
        }
        return $pic_url;
    }

    public static function getVisitImages($id,$download)
    {
        $images_url = [];
        $images = Images::find()
            ->where(['parent_type' => 'visits', 'parent_id' => $id])
            ->all();
        foreach ($images as $image)
        {
            $images_url[] = self::getAttachmentApiPath(). '?type='. $image->parent_type . "&id=" . $image->parent_id . "&file_name=" . $image->image_name .'&download='.$download;
            //$images_url[] = $image->parent_type . "/" . $image->parent_id . "/" . $image->image_name;
        }
        return $images_url;
    }
    public static function getVisitImagesLatest($download)
    {
        $images_url = [];
        //$images = Images::find()->where(['parent_type' => 'visits'])->limit(10)->orderBy('id desc')->all();
        //$images = Images::find()->where(['parent_type' => 'visits'])->andWhere(['in','id',[46602,45003,49251,49253,44693,45232,44933,44778,56869,41354]])->all();
        $images = Images::find()->where(['parent_type' => 'visits'])->andWhere(['is_published'=>1])->limit(20)->orderBy('id desc')->all();
        $i=0;
        foreach ($images as $image)
        {
            $visit=Visits::find()->where(['id'=>$image->parent_id])->one();
            $images_url[$i]['image'] = self::getAttachmentApiPath(). '?type='. $image->parent_type . "&id=" . $image->parent_id . "&file_name=" . $image->image_name .'&download='.$download;
            $images_url[$i]['application_id']=$visit->parent_id;
            //$images_url[] = $image->parent_type . "/" . $image->parent_id . "/" . $image->image_name;
            $i++;
        }
        return $images_url;
    }

    public static function getVisitFloodImagesLatest($download)
    {
        $images_url = [];
        $images = Images::find()
            ->innerJoin('visits', 'visits.id = images.parent_id')
            ->innerJoin('applications', 'applications.id = visits.parent_id')
            ->innerJoin('loans', 'loans.application_id = applications.id')
            ->select(['images.*'])
            ->andWhere(['applications.project_id'=>98])
            ->andWhere(['images.parent_type' => 'visits'])
            ->andWhere(['loans.status' => 'collected'])
            ->groupBy(['parent_id'])
//            ->andWhere(['images.is_published'=>1])
            ->limit(20)
            ->orderBy('images.id desc')
            ->all();
        $i=0;
        foreach ($images as $image)
        {
            $visit=Visits::find()->where(['id'=>$image->parent_id])->one();
            if(!empty($visit) && $visit!=null){
                $images_url[$i]['image'] = self::getAttachmentApiPath(). '?type='. $image->parent_type . "&id=" . $image->parent_id . "&file_name=" . $image->image_name .'&download='.$download;
                $images_url[$i]['application_id']=$visit->parent_id;
                $i++;
            }
        }
        return $images_url;
    }

    public static function getVisitAcagImagesLatest($download)
    {
        $images_url = [];
        $images = Images::find()
            ->innerJoin('visits', 'visits.id = images.parent_id')
            ->innerJoin('applications', 'applications.id = visits.parent_id')
            ->innerJoin('loans', 'loans.application_id = applications.id')
            ->select(['images.*'])
            ->andWhere(['applications.project_id'=>132])
            ->andWhere(['images.parent_type' => 'visits'])
            ->andWhere(['loans.status' => 'collected'])
            ->groupBy(['parent_id'])
//            ->andWhere(['images.is_published'=>1])
            ->limit(20)
            ->orderBy('images.id desc')
            ->all();
        $i=0;
        foreach ($images as $image)
        {
            $visit=Visits::find()->where(['id'=>$image->parent_id])->one();
            if(!empty($visit) && $visit!=null){
                $images_url[$i]['image'] = self::getAttachmentApiPath(). '?type='. $image->parent_type . "&id=" . $image->parent_id . "&file_name=" . $image->image_name .'&download='.$download;
                $images_url[$i]['application_id']=$visit->parent_id;
                $i++;
            }
        }
        return $images_url;
    }
}