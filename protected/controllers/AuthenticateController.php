<?php

/**
 * Description of AuthenticateController
 */
class AuthenticateController extends ControllerCore {

    use ControlLogin;

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array_merge(parent::accessRules(), array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => ['confirm'],
                'users' => ['*'],
            ),
                )
        );
    }

    /**
     * Set the layout
     * @param CAction $action
     * @return boolean whether this process is succceded
     */
    public function beforeAction($action) {
        $this->layout = '//layouts/parent-raw';
        return parent::beforeAction($action);
    }

    /**
     * Register Form
     */
    public function actionRegister() {

        $register = new RegisterForm(ControllerCore::REQUIRE_CAPTCHA_SCENARIO);
        if (isset($_POST[CHtml::modelName($register)])) {
            $register->attributes = $_POST[CHtml::modelName($register)];
            if ($register->save()) {
                $form = new LoginUser;
                $form->username = $register->email;
                $form->password = $_POST[CHtml::modelName($register)]['password'];
                $form->rememberMe = 0;

                /**
                 * Generate random Assignment for newly registered user
                 */
                $user = $register->user;

                $criteria = new CDbCriteria();
                $criteria->with = ['corpusParseTreeConsensus'];
                $criteria->order = 'rand()';

                $ratio = [
                    'definitive' => 5,
                    'challenge' => [
                        'limit' => 15,
                        'range' => [1000, 2000]
                    ],
                    'corpus' => [
                        'Tutorial' => 5
                    ],
                ];

                $criteriaGolden = new CDbCriteria;
                $criteriaGolden->mergeWith($criteria);
                $criteriaGolden->addCondition('corpusParseTreeConsensus.corpusParseTreeStringID is not null');
                $criteriaGolden->limit = $ratio['definitive'];
                $stringsGolden = CorpusParseTreeString::model()->findAll($criteriaGolden);

                $criteriaChallenge = new CDbCriteria;
                $criteriaChallenge->mergeWith($criteria);
                $criteriaChallenge->addCondition('corpusParseTreeConsensus.corpusParseTreeStringID is null');
                $criteriaChallenge->addCondition('ID >= :min and ID <= :max');
                $criteriaChallenge->params = [':min' => $ratio['challenge']['range'][0], ':max' => $ratio['challenge']['range'][1]];
                $criteriaChallenge->limit = $ratio['challenge']['limit'];
                $stringsChallenge = CorpusParseTreeString::model()->findAll($criteriaChallenge);

                $stringsTutorial = [];
                foreach ($ratio['corpus'] as $corpusName => $limit) {
                    $criteriaTutorial = new CDbCriteria;
                    $criteriaTutorial->mergeWith($criteria);
                    $criteriaTutorial->with[] = 'corpusParseTree';
                    $criteriaTutorial->limit = $limit;
                    $criteriaTutorial->compare('corpusParseTree.name', $corpusName);
                    $stringsTutorial = array_merge($stringsTutorial, CorpusParseTreeString::model()->findAll($criteriaTutorial));
                }

                foreach ([$stringsTutorial, $stringsGolden, $stringsChallenge] as $parts) {
                    foreach ($parts as $string) {
                        /* @var $string CorpusParseTreeString */
                        $assigned = new StringAssigned();
                        $assigned->userID = $user->ID;
                        $assigned->corpusParseTreeStringID = $string->ID;
                        $assigned->save();
                    }
                }

                /**
                 * End Generate random Assignment
                 */
                if ($form->validate() && $form->login()) {
                    $this->emailVerify($register->user);
                    $this->redirect(['/parser']);
                }
            }
        }

        $this->render('register', ['register' => $register]);
    }

    /**
     * User Login
     */
    public function actionLogin() {
        $login = new LoginUser;
        if (isset($_POST[CHtml::modelName($login)])) {
            $login->attributes = $_POST[CHtml::modelName($login)];
            if ($login->validate() && $login->login()) {
                $module = $this->module;
                $this->redirect(["/"]);
            }
        }
        $this->render('login', ['login' => $login]);
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout() {
        Yii::app()->request->cookies['workspace'] = new CHttpCookie('workspace', null);
        Yii::app()->user->logout();
        $url = Yii::app()->request->urlReferrer ? Yii::app()->request->urlReferrer : $this->createUrl('authenticate/login');
        $this->redirect($url);
    }

    /**
     * Forget Password Page
     */
    public function actionForget() {
        $this->pageTitle = Yii::app()->name . ' | Forgot Password';
        if (isset($_POST['reference'])) {
            $user = User::model()->find('LOWER(email)=:input OR LOWER(username)=:input', [':input' => strtolower(trim($_POST['reference']))]);
            /* @var $user User */
            if ($user) {
                $password = Util::generateRandomString(12);
                if ($user->saveAttributes(['password' => CPasswordHelper::hashPassword($password)])) {
                    self::emailResetPassword($user, $password);
                    UserWeb::instance()->setMessage('alert-info', 'Password anda reset dan dikirim ke email anda, silahkan cek inbox atau spam anda');
                } else {
                    throw new CHttpException(500, 'Internal Server Error');
                }
            } else {
                UserWeb::instance()->setMessage('alert-warning', 'Email / Username tidak dapat ditemukan');
            }
        }
        $this->render('forget-password');
    }

    /**
     * Confirm token
     * @param integer $userID
     * @param string $token
     */
    public function actionConfirm($userID, $token) {
        $user = User::model()->with('verifiedUser')->findByPk($userID, 'verifiedUser.userID IS NULL');
        /* @var $user User */
        if ($this->token($user) == $token) {
            $verifiedUser = new VerifiedUser;
            $verifiedUser->userID = $user->ID;
            if ($verifiedUser->save()) {
                UserWeb::instance()->setMessage('alert-info', 'Selamat email anda sudah terverifikasi. dengan ini anda dapat menggunakan sistem ini secara penuh');
                $this->redirect(['/parser']);
            } else {
                throw new CHttpException(500, 'Internal Server Error');
            }
        } else {
            throw new CHttpException(403, 'Verifikasi email gagal dilakukan');
        }
    }

    /**
     * Generate token for verify
     * @param User $user
     */
    public function token($user) {
        $joinDate = new DateTime($user->joinDate);
        $interval = $joinDate->diff(new DateTime);
        /* @var $interval DateInterval */
        $effectiveDate = date('Y-m-d', strtotime("+$interval->days days", $joinDate->getTimestamp()));
        return md5(sha1(substr(md5($user->username), 0, 16) . substr(md5($effectiveDate), 0, 16)));
    }

    /**
     * Send email notification for reset password
     * @param Order $order
     * @param string $content
     */
    private static function emailResetPassword($user, $password) {
        $email = new EmailSender;
        $email->view = 'reset-password';
        $email->setSubject('Reset Password');
        $email->setBody(['user' => $user, 'password' => $password]);
        $email->setTo([$user->email => $user->username]);
        $email->send();
    }

    /**
     * Ask the System whether the current user is still authenticated for
     * using this system. It will check the timeout expiration
     */
    public function actionAsk() {
        header('Content-type: application/json');
        $json = array('isTimeout' => UserWeb::instance()->isGuest);
        if ($json['isTimeout']) {
            $json['backURL'] = $this->createUrl('/parser/authenticate/login');
        }
        echo CJSON::encode($json);
    }

}
