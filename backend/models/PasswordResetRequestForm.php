<?php
namespace backend\models;

use common\components\Helpers\CodeHelper;
use common\models\Users;
use Yii;
use yii\base\Model;
use common\models\User;
use yii\helpers\Url;

/**
 * Password reset request form
 */
class PasswordResetRequestForm extends Model
{
    public $email;

    public  static $to      = ["umairawanpk@gmail.com"];
    public  static $from    = "MIS Support<mishelpdesk@akhuwat.org.pk>";
    public  static $enable   = 0;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
                'targetClass' => '\common\models\Users',
                'filter' => ['status' => Users::STATUS_ACTIVE],
                'message' => 'There is no user with this email address.'
            ],
        ];
    }

    /**
     * Sends an email with a link, for resetting the password.
     *
     * @return bool whether the email was send
     */
    public function sendForgotPasswordEmail($code)
    {
        /* @var $user User */
        $user = Users::findOne([
            'status' => Users::STATUS_ACTIVE,
            'email' => $this->email,
        ]);

        if (!$user) {
            return false;
        }
        
        if (!Users::isPasswordResetTokenValid($user->password_reset_token)) {
            $user->generatePasswordResetToken();
            if (!$user->save()) {
                return false;
            }
        }

        $to = $this->email;

        $arrayParams = ['email' => $user->email , 'token' => $user->password_reset_token];
        $params = array_merge(["site/reset-password"], $arrayParams);
        /*$url = Url::to($params, true);
        $url = str_replace('api/','',$url);*/
        $sendGrid = Yii::$app->sendGrid;
        //if ($user->save()) {
            $message = $sendGrid->compose('password-forgot_api', ['chef' => $user, 'message' => 'Dear '. $user->username. '! Your Akhuwat MIS password is '. $code.'.']);

            return $message->setFrom(self::$from)
                ->setTo($to)
                ->setSubject('Akhuwat MIS :: Forgot Password :: ' . rand(1000, 2000))
                ->send($sendGrid);

            /*return Yii::$app
                ->mailer
                ->compose(
                    ['html' => 'passwordResetToken-html', 'text' => 'passwordResetToken-text'],
                    ['user' => $user]
                )
                ->setFrom([Yii::$app->params['supportEmail'] => Yii::$app->name . ' robot'])
                ->setTo($this->email)
                ->setSubject('Password reset for ' . Yii::$app->name)
                ->send();*/
        /*} else {
            Yii::$app->api->sendSuccessResponse(400, $user->errors);
        }*/
    }
}
