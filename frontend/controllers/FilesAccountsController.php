<?php

namespace frontend\controllers;

use common\components\Helpers\ExportHelper;
use common\components\Helpers\ImageHelper;
use common\components\Helpers\MemberHelper;
use common\models\Applications;
use common\models\Members;
use common\models\MembersAccount;
use common\widgets\Alert;
use Yii;
use common\models\FilesAccounts;
use common\models\search\FilesAccountsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\web\Response;
use yii\helpers\Html;
use yii\web\UnauthorizedHttpException;
use yii\web\UploadedFile;

/**
 * FilesAccountsController implements the CRUD actions for FilesAccounts model.
 */
class FilesAccountsController extends Controller
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
                'rules' => Yii::$app->Permission->getRules(Yii::$app->controller->id, $this->rbac_type)
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
     * Lists all FilesAccounts models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new FilesAccountsSearch();
        $searchModel->type = 0;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRejected()
    {
        $searchModel = new FilesAccountsSearch();
        $searchModel->type = 6;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('rejected_index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPublishFiles()
    {
        $searchModel = new FilesAccountsSearch();
        $searchModel->type = 1;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('publish-files', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPublishFilesReject()
    {
        $searchModel = new FilesAccountsSearch();
        $searchModel->type = 5;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('publish-files-reject', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    public function actionPinFiles()
    {
        $searchModel = new FilesAccountsSearch();
        $searchModel->type = 2;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('pin-files', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionRejectedFiles()
    {
        $searchModel = new FilesAccountsSearch();
        $searchModel->type = 3;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('rejected-files', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionChequePresented()
    {
        $searchModel = new FilesAccountsSearch();
        $searchModel->type = 4;
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('cheque-presented', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }


    /**
     * Displays a single FilesAccounts model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $request = Yii::$app->request;

        if (isset($_GET['download']) && isset($_GET['acc'])) {
            $model = $this->findModel($id);
            $file_name = $model->file_path;

            $file_path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;

            if (file_exists($file_path)) {
                return Yii::$app->response->sendFile($file_path);
            } else {
                throw new NotFoundHttpException('File not exist');
            }
        }

        if (isset($_GET['download']) && isset($_GET['bank'])) {
            $model = $this->findModel($id);
            $file_name = $model->bank_file_path;

            $file_path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;

            if (file_exists($file_path)) {
                return Yii::$app->response->sendFile($file_path);
            } else {
                throw new NotFoundHttpException('File not exist');
            }
        }


        if ($request->isAjax) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'title' => "FilesAccounts #" . $id,
                'content' => $this->renderAjax('view', [
                    'model' => $this->findModel($id),
                ]),
                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote']) .
                    Html::a('Download Accounts File', ['view', 'id' => $id, 'download' => true, 'acc' => true], ['class' => 'btn btn-info']) .
                    Html::a('Download Email File', ['view', 'id' => $id, 'download' => true, 'bank' => true], ['class' => 'btn btn-info'])
            ];
        } else {
            return $this->render('view', [
                'model' => $this->findModel($id),
            ]);
        }
    }

    /**
     * Creates a new FilesAccounts model.
     * For ajax request will return json object
     * and for non-ajax request if creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $request = Yii::$app->request;
        $model = new FilesAccounts();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new File Accounts",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $model->bank_file = UploadedFile::getInstance($model, 'bank_file');

                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $bank_file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->bank_file->baseName . '.' . $model->bank_file->extension;

                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name);
                $model->bank_file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $bank_file_name);

                $model->file_path = $file_name;
                $model->bank_file_path = $bank_file_name;

                $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                /* $fileCount= file($path);
                $model->total_records=count($fileCount)-1;*/
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['cnic', 'account_no'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "File Accounts ",
                                'content' => '<span class="text-success">Accounts File have not required fields.</span>',
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Download Sample', ['download-sample'], ['title' => 'Download Sample File', 'class' => 'btn btn-primary', 'data-pjax' => '0', 'target' => '_blank']) .
                                    Html::a('Create New', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }

                    }
                }
                if ($model->save()) {
                    $modelFA = FilesAccounts::find()->where(['id'=>$model->id])->one();
                    if (!empty($modelFA) && $modelFA!=null && $modelFA->type == 0 && $modelFA->project_id ==132) {
                        $file_name = $modelFA->file_path;
                        $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                        $total_records = 1;
                        $errors = [];
                        if (($handle = fopen($path, "r")) !== FALSE) {
                            $header = fgetcsv($handle);
                            while (($row = fgetcsv($handle)) !== FALSE) {
                                $total_records++;
                                $account_no = str_replace("'", "", $row[1]);
                                $member = Members::find()->where(['cnic' => $row[0]])->select(['id','cnic'])->one();
                                if (isset($member) && !empty($member)) {
                                    $member_account = MembersAccount::find()->where(['account_no' => $account_no, 'member_id' => $member->id, 'is_current' => 1])->one();
                                    if (isset($member_account)) {
                                        $acagApplication = Applications::find()
                                            ->andFilterWhere(['=', 'applications.member_id', $member->id])
                                            ->andFilterWhere(['=', 'applications.deleted', 0])
                                            ->andFilterWhere(['!=','applications.status','rejected'])
                                            ->andFilterWhere(['=','applications.project_id',132])
                                            ->one();
                                        if(!empty($acagApplication) && $acagApplication!=null){
                                            $member_account->acc_file_id = $modelFA->id;
                                            if ($member_account->save()) {
                                            } else {
                                                $errors[] = [$row[1] => $member_account->getErrors()];
                                            }
                                        }

                                    } else {
                                        $errors[] = [$row[1] => 'account not found'];
                                    }
                                } else {
                                    $errors[] = [$row[0] => 'member not found'];
                                }
                            }
                        }
                        $modelFA->total_records = $total_records - 1;
                        if (isset($errors) && !empty($errors)) {
                            $modelFA->error_description = json_encode($errors);
                        }
                        $modelFA->save();
                    }

                    if (!empty($modelFA) && $modelFA!=null && $modelFA->type == 0 && $modelFA->project_id !=132) {
                        $file_name = $modelFA->file_path;
                        $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                        $total_records = 1;
                        $errors = [];
                        if (($handle = fopen($path, "r")) !== FALSE) {
                            $header = fgetcsv($handle);
                            while (($row = fgetcsv($handle)) !== FALSE) {
                                $total_records++;
                                $account_no = str_replace("'", "", $row[1]);
                                $member = Members::find()->where(['cnic' => $row[0]])->select(['id','cnic'])->one();
                                if (isset($member) && !empty($member)) {
                                    $member_account = MembersAccount::find()->where(['account_no' => $account_no, 'member_id' => $member->id, 'is_current' => 1])->one();
                                    if (isset($member_account)) {
                                        $otherApplication = Applications::find()
                                            ->andFilterWhere(['=', 'applications.member_id', $member->id])
                                            ->andFilterWhere(['=', 'applications.deleted', 0])
                                            ->andFilterWhere(['!=','applications.status','rejected'])
                                            ->andFilterWhere(['!=','applications.project_id',132])
                                            ->one();

                                        if(!empty($otherApplication) && $otherApplication!=null){
                                            $member_account->acc_file_id = $modelFA->id;
                                            if ($member_account->save()) {
                                            } else {
                                                $errors[] = [$row[1] => $member_account->getErrors()];
                                            }
                                        }

                                    } else {
                                        $errors[] = [$row[1] => 'account not found'];
                                    }
                                } else {
                                    $errors[] = [$row[0] => 'member not found'];
                                }
                            }
                        }
                        $modelFA->total_records = $total_records - 1;
                        if (isset($errors) && !empty($errors)) {
                            $modelFA->error_description = json_encode($errors);
                        }
                        $modelFA->save();
                    }
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => " File Accounts",
                        'content' => '<span class="text-success">File uploaded successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                } else {
                    print_r($model->getErrors());
                    return [
                        'title' => "Create new File Accounts",
                        'content' => $this->renderAjax('create', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new FilesAccounts",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {

                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['cnic', 'account_no'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            $model->addError('file', 'Accounts File have not required fields.');
                            return $this->render('create', [
                                'model' => $model,
                            ]);
                        }

                    }
                }
                if ($model->save()) {
                    return $this->redirect(['index'/*'view', 'id' => $model->id*/]);
                } else {
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

    public function actionCreateRejected()
    {
        $request = Yii::$app->request;
        $model = new FilesAccounts();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new File Accounts",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->type = 6;
                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                /* $fileCount= file($path);
                $model->total_records=count($fileCount)-1;*/
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['cnic', 'account_no'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "File Accounts ",
                                'content' => '<span class="text-success">Accounts File have not required fields.</span>',
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Download Sample', ['download-sample'], ['title' => 'Download Sample File', 'class' => 'btn btn-primary', 'data-pjax' => '0', 'target' => '_blank']) .
                                    Html::a('Create New', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }

                    }
                }
                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => " File Accounts",
                        'content' => '<span class="text-success">File uploaded successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                } else {
                    print_r($model->getErrors());
                    return [
                        'title' => "Create new File Accounts",
                        'content' => $this->renderAjax('create', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new FilesAccounts",
                    'content' => $this->renderAjax('create', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {
                $model->type = 6;
                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['cnic', 'account_no'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            $model->addError('file', 'Accounts File have not required fields.');
                            return $this->render('create', [
                                'model' => $model,
                            ]);
                        }

                    }
                }
                if ($model->save()) {
                    return $this->redirect(['index'/*'view', 'id' => $model->id*/]);
                } else {
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

    public function actionCreatePublish()
    {
        $request = Yii::$app->request;
        $model = new FilesAccounts();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new File Accounts",
                    'content' => $this->renderAjax('create-publish', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                /* $fileCount= file($path);
                $model->total_records=count($fileCount)-1;*/
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'account_no', 'transaction_id', 'date_disbursement'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "File Accounts ",
                                'content' => '<span class="text-success">Accounts File have not required fields.</span>',
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Download Sample', ['download-sample'], ['title' => 'Download Sample File', 'class' => 'btn btn-primary', 'data-pjax' => '0', 'target' => '_blank']) .
                                    Html::a('Create New', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }

                    }
                }


                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => " File Accounts",
                        'content' => '<span class="text-success">File uploaded successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                } else {
                    print_r($model->getErrors());
                    return [
                        'title' => "Create new File Accounts",
                        'content' => $this->renderAjax('create-publish', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new FilesAccounts",
                    'content' => $this->renderAjax('create-publish', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {

                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'account_no', 'transaction_id'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            $model->addError('file', 'Accounts File have not required fields.');
                            return $this->render('create-publish', [
                                'model' => $model,
                            ]);
                        }

                    }
                }
                if ($model->save()) {
                    return $this->redirect(['index'/*'view', 'id' => $model->id*/]);
                } else {
                    return $this->render('create-publish', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('create-publish', [
                    'model' => $model,
                ]);
            }
        }

    }

    public function actionCreatePublishReject()
    {
        $request = Yii::$app->request;
        $model = new FilesAccounts();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new Publish Reject Response File",
                    'content' => $this->renderAjax('create-reject-publish', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                /* $fileCount= file($path);
                $model->total_records=count($fileCount)-1;*/
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'account_no', 'transaction_id'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "Publish Reject Response File",
                                'content' => '<span class="text-success">Accounts File have not required fields.</span>',
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Download Sample', ['download-sample'], ['title' => 'Download Sample File', 'class' => 'btn btn-primary', 'data-pjax' => '0', 'target' => '_blank']) .
                                    Html::a('Create New', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }

                    }
                }


                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => " Publish Reject Response File",
                        'content' => '<span class="text-success">File uploaded successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                } else {
                    print_r($model->getErrors());
                    return [
                        'title' => "Create new Publish Reject Response File",
                        'content' => $this->renderAjax('create-reject-publish', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new Publish Reject Response File",
                    'content' => $this->renderAjax('create-reject-publish', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {

                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/verified_accounts/' . $file_name;
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'account_no', 'transaction_id'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            $model->addError('file', 'Accounts File have not required fields.');
                            return $this->render('create-publish', [
                                'model' => $model,
                            ]);
                        }

                    }
                }
                if ($model->save()) {
                    return $this->redirect(['index'/*'view', 'id' => $model->id*/]);
                } else {
                    return $this->render('create-publish', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('create-publish', [
                    'model' => $model,
                ]);
            }
        }

    }

    public function actionCreatePin()
    {
        $request = Yii::$app->request;
        $model = new FilesAccounts();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new File Accounts",
                    'content' => $this->renderAjax('create-pin', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/update_pin_files/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/update_pin_files/' . $file_name;
                /* $fileCount= file($path);
                $model->total_records=count($fileCount)-1;*/
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'account_no', 'transaction_id', 'pin'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "File Accounts ",
                                'content' => '<span class="text-success">Accounts File have not required fields.</span>',
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Download Sample', ['download-sample-pin'], ['title' => 'Download Sample File', 'class' => 'btn btn-primary', 'data-pjax' => '0', 'target' => '_blank']) .
                                    Html::a('Create New', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }

                    }
                }


                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => " File Accounts",
                        'content' => '<span class="text-success">File uploaded successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                } else {
                    print_r($model->getErrors());
                    return [
                        'title' => "Create new File Accounts",
                        'content' => $this->renderAjax('create-pin', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new FilesAccounts",
                    'content' => $this->renderAjax('create-pin', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {

                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/update_pin_files/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/update_pin_files/' . $file_name;
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'account_no', 'transaction_id', 'pin'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            $model->addError('file', 'Accounts File have not required fields.');
                            return $this->render('create-pin', [
                                'model' => $model,
                            ]);
                        }

                    }
                }
                if ($model->save()) {
                    return $this->redirect(['index'/*'view', 'id' => $model->id*/]);
                } else {
                    return $this->render('create-pin', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('create-pin', [
                    'model' => $model,
                ]);
            }
        }

    }

    public function actionCreateRejectedDisbursement()
    {
        $request = Yii::$app->request;
        $model = new FilesAccounts();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new File Accounts",
                    'content' => $this->renderAjax('create-rejected', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/rejected_transaction/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/rejected_transaction/' . $file_name;

                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'account_no', 'transaction_id'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "File Accounts ",
                                'content' => '<span class="text-success">Accounts File have not required fields.</span>',
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Download Sample', ['download-sample-rejected'], ['title' => 'Download Sample File', 'class' => 'btn btn-primary', 'data-pjax' => '0', 'target' => '_blank']) .
                                    Html::a('Create New', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }

                    }
                }


                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => " File Accounts",
                        'content' => '<span class="text-success">File uploaded successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                } else {
                    print_r($model->getErrors());
                    return [
                        'title' => "Create new File Accounts",
                        'content' => $this->renderAjax('create-rejected', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new FilesAccounts",
                    'content' => $this->renderAjax('create-rejected', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {

                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/rejected_transaction/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/rejected_transaction/' . $file_name;
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'account_no', 'transaction_id'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            $model->addError('file', 'Accounts File have not required fields.');
                            return $this->render('create-rejected', [
                                'model' => $model,
                            ]);
                        }

                    }
                }
                if ($model->save()) {
                    return $this->redirect(['index'/*'view', 'id' => $model->id*/]);
                } else {
                    return $this->render('create-rejected', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('create-rejected', [
                    'model' => $model,
                ]);
            }
        }

    }

    public function actionCreateChequePresented()
    {
        $request = Yii::$app->request;
        $model = new FilesAccounts();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Create new File Accounts",
                    'content' => $this->renderAjax('create-cheque-presented', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            } else if ($model->load($request->post())) {
                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/cheque_presented/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/cheque_presented/' . $file_name;

                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'cheque_no'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "File Accounts ",
                                'content' => '<span class="text-success">Accounts File have not required fields.</span>',
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Download Sample', ['download-sample-rejected'], ['title' => 'Download Sample File', 'class' => 'btn btn-primary', 'data-pjax' => '0', 'target' => '_blank']) .
                                    Html::a('Create New', ['create'], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }

                    }
                }


                if ($model->save()) {
                    return [
                        'forceReload' => '#crud-datatable-pjax',
                        'title' => " File Accounts",
                        'content' => '<span class="text-success">File uploaded successfully</span>',
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"])
                    ];
                } else {
                    print_r($model->getErrors());
                    return [
                        'title' => "Create new File Accounts",
                        'content' => $this->renderAjax('create-cheque-presented', [
                            'model' => $model,
                        ]),
                        'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                            Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                    ];
                }
            } else {
                return [
                    'title' => "Create new FilesAccounts",
                    'content' => $this->renderAjax('create-cheque-presented', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])

                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post())) {

                $model->file = UploadedFile::getInstance($model, 'file');
                $file_name = strtotime(date('Y-m-d H:i:s')) . '_' . $model->file->baseName . '.' . $model->file->extension;
                $model->file->saveAs(ImageHelper::getAttachmentPath() . '/cheque_presented/' . $file_name);
                $model->file_path = $file_name;
                $path = ImageHelper::getAttachmentPath() . '/cheque_presented/' . $file_name;
                if (($handle = fopen($path, "r")) !== FALSE) {
                    $header = fgetcsv($handle);
                    $columns = ['sanction_no', 'cheque_no'];
                    foreach ($columns as $column) {
                        if (!in_array($column, $header)) {
                            fclose($handle);
                            unlink($path);
                            $model->addError('file', 'Accounts File have not required fields.');
                            return $this->render('create-cheque-presented', [
                                'model' => $model,
                            ]);
                        }

                    }
                }
                if ($model->save()) {
                    return $this->redirect(['index'/*'view', 'id' => $model->id*/]);
                } else {
                    return $this->render('create-cheque-presented', [
                        'model' => $model,
                    ]);
                }
            } else {
                return $this->render('create-cheque-presented', [
                    'model' => $model,
                ]);
            }
        }

    }

    /**
     * Updates an existing FilesAccounts model.
     * For ajax request will return json object
     * and for non-ajax request if update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update FilesAccounts #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post()) && $model->save()) {
                return [
                    'forceReload' => '#crud-datatable-pjax',
                    'title' => "FilesAccounts #" . $id,
                    'content' => $this->renderAjax('view', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                ];
            } else {
                return [
                    'title' => "Update FilesAccounts #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    public function actionReview($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update FilesAccounts #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post())) {
                $project_id = $model->project_id;
                $user_id = Yii::$app->user->id;

                if ($project_id == null || $project_id == 0) {
                    if (in_array($user_id, [2007, 5311])) {
                        if ($model->save()) {
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "FilesAccounts #" . $id,
                                'content' => $this->renderAjax('view', [
                                    'model' => $model,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        } else {
                            return [
                                'title' => "Update FilesAccounts #" . $id,
                                'content' => $this->renderAjax('update', [
                                    'model' => $model,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                            ];
                        }
                    } else {
                        return [
                            'forceReload' => '#crud-datatable-pjax',
                            'title' => "FilesAccounts #" . $id,
                            'content' => '<span class="text-success">This User Not Allowed to perform this action.</span>',
                            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                        ];
                    }
                } else {
                    if (in_array($user_id, [2007, 2012])) {
                        if ($model->save()) {
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "FilesAccounts #" . $id,
                                'content' => $this->renderAjax('view', [
                                    'model' => $model,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        } else {
                            return [
                                'title' => "Update FilesAccounts #" . $id,
                                'content' => $this->renderAjax('update', [
                                    'model' => $model,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                            ];
                        }
                    } else {
                        return [
                            'forceReload' => '#crud-datatable-pjax',
                            'title' => "FilesAccounts #" . $id,
                            'content' => '<span class="text-success">This User Not Allowed to perform this action.</span>',
                            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                        ];
                    }
                }
            } else {
                return [
                    'title' => "Update FilesAccounts #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    public function actionApprove($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($request->isGet) {
                return [
                    'title' => "Update FilesAccounts #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            } else if ($model->load($request->post())) {
                $errors = [];
                $updated_records = 0;

                $project_id = $model->project_id;
                $user_id = Yii::$app->user->id;
                if ($project_id == null || $project_id == 0) {
                    if (in_array($user_id, [4494, 2012])) {
                        if($model->save()){
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "FilesAccounts #" . $id,
                                'content' => $this->renderAjax('view', [
                                    'model' => $model,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }else{
                            return [
                                'title' => "Update FilesAccounts #" . $id,
                                'content' => $this->renderAjax('update', [
                                    'model' => $model,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                            ];
                        }
                    } else {
                        return [
                            'forceReload' => '#crud-datatable-pjax',
                            'title' => "FilesAccounts #" . $id,
                            'content' => '<span class="text-success">This User Not Allowed to perform this action.</span>',
                            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                        ];
                    }
                } else {
                    if (in_array($user_id, [4494, 4504, 6128])) {
                        if($model->save()){
                            return [
                                'forceReload' => '#crud-datatable-pjax',
                                'title' => "FilesAccounts #" . $id,
                                'content' => $this->renderAjax('view', [
                                    'model' => $model,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                            ];
                        }else{
                            return [
                                'title' => "Update FilesAccounts #" . $id,
                                'content' => $this->renderAjax('update', [
                                    'model' => $model,
                                ]),
                                'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                    Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                            ];
                        }
                    } else {
                        return [
                            'forceReload' => '#crud-datatable-pjax',
                            'title' => "FilesAccounts #" . $id,
                            'content' => '<span class="text-success">This User Not Allowed to perform this action.</span>',
                            'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                                Html::a('Edit', ['update', 'id' => $id], ['class' => 'btn btn-primary', 'role' => 'modal-remote'])
                        ];
                    }
                }
            } else {
                return [
                    'title' => "Update FilesAccounts #" . $id,
                    'content' => $this->renderAjax('update', [
                        'model' => $model,
                    ]),
                    'footer' => Html::button('Close', ['class' => 'btn btn-default pull-left', 'data-dismiss' => "modal"]) .
                        Html::button('Save', ['class' => 'btn btn-primary', 'type' => "submit"])
                ];
            }
        } else {
            /*
            *   Process for non-ajax request
            */
            if ($model->load($request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }
    }

    /**
     * Delete an existing FilesAccounts model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $request = Yii::$app->request;
        $this->findModel($id)->delete();

        if ($request->isAjax) {
            /*
            *   Process for ajax request
            */
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['forceClose' => true, 'forceReload' => '#crud-datatable-pjax'];
        } else {
            /*
            *   Process for non-ajax request
            */
            return $this->redirect(['index']);
        }


    }

    /**
     * Delete multiple existing FilesAccounts model.
     * For ajax request will return json object
     * and for non-ajax request if deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */

    public function actionDownloadSample()
    {
        $file_path = ImageHelper::getAttachmentPath() . 'verified_accounts/sample_sheet.csv';
        //die($file_path);
        if (file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        } else {
            throw new NotFoundHttpException('File not exist');
        }
    }

    public function actionDownloadSamplePublish()
    {
        $file_path = ImageHelper::getAttachmentPath() . 'verified_accounts/sample_sheet (Bank).csv';
        //die($file_path);
        if (file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        } else {
            throw new NotFoundHttpException('File not exist');
        }
    }


    public function actionDownloadSamplePin()
    {
        $file_path = ImageHelper::getAttachmentPath() . 'update_pin_files/sample_pin_sheet (Bank).csv';
        if (file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        } else {
            throw new NotFoundHttpException('File not exist');
        }
    }

    public function actionDownloadSampleRejected()
    {
        $file_path = ImageHelper::getAttachmentPath() . 'rejected_transaction/sample_rejected_sheet (Bank).csv';
        if (file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        } else {
            throw new NotFoundHttpException('File not exist');
        }
    }

    public function actionDownloadSampleChequePresented()
    {
        $file_path = ImageHelper::getAttachmentPath() . 'cheque_presented/sample_cheque_sheet (Bank).csv';
        if (file_exists($file_path)) {
            return Yii::$app->response->sendFile($file_path);
        } else {
            throw new NotFoundHttpException('File not exist');
        }
    }


    public function actionError($id)
    {
        $request = Yii::$app->request;
        $model = $this->findModel($id);
        $data = json_decode($model->error_description, true);
        $i = 0;
        foreach ($data as $k => $val) {
            foreach ($val as $key => $d) {
                $error[$i]['data'] = $key;
                $error[$i]['error'] = $d;
            }
            $i++;
        }
        ExportHelper::ExportCSV('File Errors', ['data', 'error'], $error);
        die();
    }

    /**
     * Finds the FilesAccounts model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return FilesAccounts the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = FilesAccounts::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
