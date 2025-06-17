<?php

namespace frontend\controllers;

use common\components\Helpers\ListHelper;
use common\components\Helpers\TemplateHelper;
use common\models\Events;
use common\models\Lists;
use kartik\mpdf\Pdf;
use Yii;
use common\models\Templates;
use common\models\search\TemplatesSearch;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;

/**
 * TemplatesController implements the CRUD actions for Templates model.
 */
class TemplatesController extends Controller
{
    public $rbac_type = 'frontend';
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        return Yii::$app->response->redirect(['site/login']);
                    } else {
                        throw new UnauthorizedHttpException('You are not allowed to perform this action.');
                    }
                },
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id,$this->rbac_type)
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'bulk-delete' => ['post'],
                ],
            ],
        ];
    }
    /**
     * Lists all Templates models.
     * @return mixed
     */
    public function actionIndex()
    {


            /*$str='Hii,Dear Borrower<span style="background-color: rgb(214, 239, 214);"> <b>[[Name]]</b>&nbsp;</span>&nbsp;your requested to come on 1st feb 2020 to colect your cheque.'; $start='[['; $end=']]'; $with_from_to=true;
            $arr = [];
            $last_pos = 0;
            $last_pos = strpos($str, $start, $last_pos);
            while ($last_pos !== false) {
                $t = strpos($str, $end, $last_pos);
                $arr[] = ($with_from_to ? $start : '').substr($str, $last_pos + 1, $t - $last_pos - 1).($with_from_to ? $end : '');
                $last_pos = strpos($str, $start, $last_pos+1);
            }

            foreach($arr as $var){
                print_r($var);die();
                $new_str=str_replace($var,'ali',$str);
            }
            echo'<pre>';
            print_r($new_str);
            die();
            $string='alksjdlka (asass) (asdk)';
            $text_outside=array();
            $text_inside=array();
            $t="";
            for($i=0;$i<strlen($string);$i++)
            {
                if($string[$i]=='[')
                {
                    $text_outside[]=$t;
                    $t="";
                    $t1="";
                    $i++;
                    while($string[$i]!=']')
                    {
                        $t1.=$string[$i];
                        $i++;
                    }
                    $text_inside[] = $t1;

                }
                else {
                    if($string[$i]!=']')
                        $t.=$string[$i];
                    else {
                        continue;
                    }

                }
            }
            if($t!="")
                $text_outside[]=$t;

            //var_dump($text_outside);
        print_r($text_outside);die();

            $text = 'ignore everything except this  (kdsfkj)';
        preg_match('#\((.*?)\)#', $text, $match);
        print_r($text_outside);die();*/
        $searchModel = new TemplatesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        //$pages = new Pagination(['totalCount'=>($dataProvider->count), 'pageSize' => 1,]);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            //'pagination'=>$pages
        ]);
    }


    /**
     * Displays a single Templates model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {   
        $request = Yii::$app->request;
        if($request->isAjax){
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                    'title'=> "Templates #".$id,
                    'content'=>$this->renderAjax('view', [
                        'model' => $this->findModel($id),
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Edit',['update','id'=>$id],['class'=>'btn btn-primary','role'=>'modal-remote'])
                ];    
        }else{
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new Templates model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new Templates();
        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Create new Templates",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }else if($model->load($request->post()) && $model->save()){
                return [
                    'forceReload'=>'#crud-datatable-pjax',
                    'title'=> "Create new Templates",
                    'content'=>'<span class="text-success">Create Templates success</span>',
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::a('Create More',['create'],['class'=>'btn btn-primary','role'=>'modal-remote'])
        
                ];         
            }else{           
                return [
                    'title'=> "Create new Templates",
                    'content'=>$this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
        
                ];         
            }
        }else{
            /*
            *   Process for non-ajax request
            */

            if ($model->load($request->post()) ) {

                $model->send_to=implode(',',$request->post()['Templates']['send_to'] );
                if($request->post()['Templates']['is_active']=='on'){
                    $model->is_active=1;
                }else{
                    $model->send_to=0;
                }
                $model->send_to='1';

                if($model->save()){
                    return $this->redirect(['index']);
                }else{
                    return $this->render('create', [
                        'model' => $model,
                    ]);
                }

            } else {
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
        }
       
    }

    /**
     * Updates an existing Templates model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);       

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if($request->isGet){
                return [
                    'title'=> "Update Templates #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];         
            }else if($model->load($request->post()) && $model->save()){
                $model->send_to=implode(',',$request->post()['Templates']['send_to'] );
                if($request->post()['Templates']['is_active']=='on'){
                    $model->is_active=1;
                }else{
                    $model->send_to=0;
                }
                $model->send_to='1';

                if($model->save()){
                    return $this->redirect(['index']);
                }else{
                    return [
                        'title'=> "Update Templates #".$id,
                        'content'=>$this->renderAjax('update', [
                            'model' => $model,
                        ]),
                        'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                            Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                    ];
                }
            }else{
                 return [
                    'title'=> "Update Templates #".$id,
                    'content'=>$this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer'=> Html::button('Close',['class'=>'btn btn-default pull-left','data-dismiss'=>"modal"]).
                                Html::button('Save',['class'=>'btn btn-primary','type'=>"submit"])
                ];        
            }
        }else{
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $model->send_to=implode(',',$request->post()['Templates']['send_to'] );
                if($request->post()['Templates']['is_active']=='on'){
                    $model->is_active=1;
                }else{
                    $model->send_to=0;
                }
                $model->send_to='1';

                if($model->save()){
                    return $this->redirect(['index']);
                }else{
                    print_r($model->getErrors());
                    die();
                }
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing Templates model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }
    public function actionGetPlaceholders($module)
    {
        $placeholders=ListHelper::getLists($module.'_placeholders');
        $html='<ul class="tags">';
        $i=1;
        foreach ($placeholders as $key => $value) {
            //'.$i.'
            $html = $html . '<li><p id="tag-'.$i.'" href="#" class="tag">' . $value . '</p></li>';
            $i++;
        }
        $html=$html.'</ul>';
        return $html;
        print_r($html);die();
        /*foreach ($placeholders as $plac){
            $ul=$ul.'<li'
        }*/
    }
        /**
     * Delete multiple existing Templates model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionBulkDelete()
    {        
        $request = Yii::$app->request;
        $pks = explode(',', $request->post( 'pks' )); // Array or selected records primary keys
        foreach ( $pks as $pk ) {
            $model = $this->findModel($pk);
            $model->delete();
        }

        if($request->isAjax){
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose'=>true,'forceReload'=>'#crud-datatable-pjax'];
        }else{
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }
       
    }

    public function actionGetTemplate($module_id,$module,$template_id)
    {
        $module_=str_replace('_',' ',$module);
        $module_=ucwords($module_);
        $module_=str_replace(' ','',$module_);
        $class = '\common\models\\' . ucwords($module_);
        $model = $class::find()->where(['id' =>$module_id])->one();
        $template=Templates::find()->where(['id'=>$template_id])->one();
        $str = $template->template_text;
        $start = '[[';
        $end = ']]';
        $with_from_to = true;
        $arr = [];
        $last_pos = 0;
        $last_pos = strpos($str, $start, $last_pos);
        while ($last_pos !== false) {
            $t = strpos($str, $end, $last_pos);
            $arr[] = ($with_from_to ? $start : '') . substr($str, $last_pos + 1, $t - $last_pos - 1) . ($with_from_to ? $end : '');
            $last_pos = strpos($str, $start, $last_pos + 1);
        }
        $i=0;
        foreach ($arr as $var) {
            $var1= substr($var, 1);
            $plac_hold= preg_match("/\[\[(.*?)\]\]/i", $var1,$reg);
            if(in_array($reg[1],['Schedules Housing','Schedules Housing1','Project Wise Details','Signature'])){
                $rep_val=TemplateHelper::replacePlaceholder($reg[1],$model);
                if($i==0){
                    $new_str = str_replace($var1, $rep_val, $str);
                }else{
                    $new_str = str_replace($var1, $rep_val, $new_str);
                }
            }else{
                $placeholders=Lists::find()->where(['list_name'=>$module.'_placeholders','label'=>$reg[1]])->one();
                $arrr=explode(".",$placeholders->value);
                if(sizeof($arrr)==1){
                    $a=$arrr[0];
                    if(!empty($a)){
                        if(in_array($a,['cheque_date','date_disbursed','application_date'])){
                            $rep_val=date('d-M-Y',$model->$a);
                        }elseif (in_array($a,['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                            $rep_val=($model->$a!=0)?date('d M Y H:i',$model->$a):'--';
                        }elseif (in_array($a,['loan_amount','total_loans','approved_amount','requested_amount'])){
                            $rep_val=number_format($model->$a);
                        }else{
                            $rep_val=$model->$a;
                        }
                    }else{
                        if(in_array($reg[1],['cheque_date','date_disbursed','application_date'])){
                            $rep_val=date('d-M-Y',$reg[1]);
                        }elseif (in_array($reg[1],['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                            $rep_val=($reg[1]!=0)?date('d M Y H:i',$reg[1]):'--';
                        }elseif (in_array($reg[1],['loan_amount','total_loans','approved_amount','requested_amount'])){
                            $rep_val=number_format($reg[1]);
                        }else{
                            $rep_val==$reg[1];
                        }
                        //$rep_val=$reg[1];
                    }
                }elseif (sizeof($arrr)==2){
                    $a=$arrr[0];$b=$arrr[1];
                    if(in_array($b,['cheque_date','date_disbursed','application_date'])){
                        $rep_val=date('d-M-Y',$model->$a->$b);
                    }elseif (in_array($b,['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                        $rep_val=($model->$a->$b!=0)?date('d M Y H:i',$model->$a->$b):'--';
                    }elseif (in_array($b,['loan_amount','total_loans','approved_amount','requested_amount'])){
                        $rep_val=number_format($model->$a->$b);
                    }else{
                        $rep_val=$model->$a->$b;
                    }
                }elseif (sizeof($arrr)==3){
                    $a=$arrr[0];$b=$arrr[1];$c=$arrr[2];
                    if(in_array($c,['cheque_date','date_disbursed','application_date'])){
                        $rep_val=date('d-M-Y',$model->$a->$b->$c);
                    }elseif (in_array($c,['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                        $rep_val=($model->$a->$b->$c!=0)?date('d M Y H:i',$model->$a->$b->$c):'--';
                    }elseif (in_array($c,['loan_amount','total_loans','approved_amount','requested_amount'])){
                        $rep_val=number_format($model->$a->$b->$c);
                    }else{
                        $rep_val=$model->$a->$b->$c;
                    }
                }elseif (sizeof($arrr)==4){
                    $a=$arrr[0];$b=$arrr[1];$c=$arrr[2];$d=$arrr[3];
                    if(in_array($d,['cheque_date','date_disbursed','application_date'])){
                        $rep_val=date('d-M-Y',$model->$a->$b->$c->$d);
                    }elseif (in_array($d,['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                        $rep_val=($model->$a->$b->$c->$d!=0)?date('d M Y H:i',$model->$a->$b->$c->$d):'--';
                    }elseif (in_array($d,['loan_amount','total_loans','approved_amount','requested_amount'])){
                        $rep_val=number_format($model->$a->$b->$c->$d);
                    }else{
                        $rep_val=$model->$a->$b->$c->$d;
                    }
                }
                if($i==0){
                    $new_str = str_replace($var1, $rep_val, $str);
                }else{
                    $new_str = str_replace($var1, $rep_val, $new_str);
                }
            }
            $i++;
        }
        if(empty($new_str)){
            $new_str=$str;
        }
        $content=$new_str;
        $pdf = Yii::$app->pdf;
        $pdf->content = $content;
        //$pdf->mode = 'pakistan/urdu';
        return $pdf->render();
    }
    public function actionGetTemplateView($module_id,$module,$template_id)
    {
        $module_=str_replace('_',' ',$module);
        $module_=ucwords($module_);
        $module_=str_replace(' ','',$module_);
        $class = '\common\models\\' . ucfirst($module_);
        $model = $class::find()->where(['id' =>$module_id])->one();
        $template=Templates::find()->where(['id'=>$template_id])->one();
        $str = $template->template_text;
        $start = '[[';
        $end = ']]';
        $with_from_to = true;
        $arr = [];
        $last_pos = 0;
        $last_pos = strpos($str, $start, $last_pos);
        while ($last_pos !== false) {
            $t = strpos($str, $end, $last_pos);
            $arr[] = ($with_from_to ? $start : '') . substr($str, $last_pos + 1, $t - $last_pos - 1) . ($with_from_to ? $end : '');
            $last_pos = strpos($str, $start, $last_pos + 1);
        }
        $i=0;
        foreach ($arr as $var) {
            $var1= substr($var, 1);
            $plac_hold= preg_match("/\[\[(.*?)\]\]/i", $var1,$reg);
            if(in_array($reg[1],['Schedules Housing','Schedules Housing1','Project Wise Details','Signature'])){
                if($model->project_id == 77){

                    $rep_val = TemplateHelper::kamyabJawanPlaceholder($reg[1], $model);

                } else {
                    $rep_val = TemplateHelper::replacePlaceholder($reg[1], $model);
                }
                if($i==0){
                    $new_str = str_replace($var1, $rep_val, $str);
                }else{
                    $new_str = str_replace($var1, $rep_val, $new_str);
                }
            }else{
                $placeholders=Lists::find()->where(['list_name'=>$module.'_placeholders','label'=>$reg[1]])->one();
                $arrr=explode(".",$placeholders->value);

                if(sizeof($arrr)==1){

                    $a=$arrr[0];
                    if(!empty($a)){
                        if(in_array($a,['cheque_date','date_disbursed','application_date'])){
                            $rep_val=date('d-M-Y',$model->$a);
                        }elseif (in_array($a,['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                            $rep_val=($model->$a!=0)?date('d M Y H:i',$model->$a):'--';
                        }elseif (in_array($a,['loan_amount','total_loans','approved_amount','requested_amount','inst_months'])){
                            $rep_val=number_format($model->$a);
                        }else{
                            $rep_val=$model->$a;
                        }
                    }else{

                        if(in_array($reg[1],['cheque_date','date_disbursed','application_date'])){
                            $rep_val=date('d-M-Y',$reg[1]);
                        }elseif (in_array($reg[1],['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                            $rep_val=($reg[1]!=0)?date('d M Y H:i',$reg[1]):'--';
                        }elseif (in_array($reg[1],['loan_amount','total_loans','approved_amount','requested_amount'])){
                            $rep_val=number_format($reg[1]);
                        }else{
                            $rep_val==$reg[1];
                        }
                        //$rep_val=$reg[1];
                    }
                }elseif (sizeof($arrr)==2){
                    $a=$arrr[0];$b=$arrr[1];
                    if(in_array($b,['cheque_date','date_disbursed','application_date'])){
                        $rep_val=date('d-M-Y',$model->$a->$b);
                    }elseif (in_array($b,['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                        $rep_val=($model->$a->$b!=0)?date('d M Y H:i',$model->$a->$b):'--';
                    }elseif (in_array($b,['loan_amount','total_loans','approved_amount','requested_amount'])){
                        $rep_val=number_format($model->$a->$b);
                    }else{
                        $rep_val=$model->$a->$b;
                    }
                }elseif (sizeof($arrr)==3){

                    $a=$arrr[0];$b=$arrr[1];$c=$arrr[2];
                    if(in_array($c,['cheque_date','date_disbursed','application_date'])){
                        $rep_val=date('d-M-Y',$model->$a->$b->$c);
                    }elseif (in_array($c,['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                        $rep_val=($model->$a->$b->$c!=0)?date('d M Y H:i',$model->$a->$b->$c):'--';
                    }elseif (in_array($c,['loan_amount','total_loans','approved_amount','requested_amount'])){
                        $rep_val=number_format($model->$a->$b->$c);
                    }else{
                        $rep_val=$model->$a->$b->$c;
                    }
                }elseif (sizeof($arrr)==4){
                    $a=$arrr[0];$b=$arrr[1];$c=$arrr[2];$d=$arrr[3];
                    if(in_array($d,['cheque_date','date_disbursed','application_date'])){
                        $rep_val=date('d-M-Y',$model->$a->$b->$c->$d);
                    }elseif (in_array($d,['cheque_date','created_at','updated_at','processed_on','approved_on'])){
                        $rep_val=($model->$a->$b->$c->$d!=0)?date('d M Y H:i',$model->$a->$b->$c->$d):'--';
                    }elseif (in_array($d,['loan_amount','total_loans','approved_amount','requested_amount'])){
                        $rep_val=number_format($model->$a->$b->$c->$d);
                    }else{
                        $rep_val=$model->$a->$b->$c->$d;
                    }
                }

                if($i==0){
                    $new_str = str_replace($var1, $rep_val, $str);
                }else{
                    $new_str = str_replace($var1, $rep_val, $new_str);
                }
            }
            $i++;
        }

        if(empty($new_str)){
            $new_str=$str;
        }
        $content=$new_str;
        return $this->render('show_template', [
            'html' => $new_str,
        ]);
    }
    /**
     * Finds the Templates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Templates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Templates::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
