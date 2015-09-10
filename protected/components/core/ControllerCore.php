<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend 
 * from this base class. All common requirment is defined in this Controller.
 * This is specific of ControllerCore. all this method is specific to 
 * this project.
 */
class ControllerCore extends ControllerBase {

    const REQUIRE_CAPTCHA_SCENARIO = 'require-captcha';

    public $language = 'id';
    public $headerMenus = [];

    /**
     * Initialize all scripts and styles.
     */
    public function registers() {
        $this->registerCSS('bootstrap/bootstrap.readable.css');
        $this->registerCSS('pace/pace-theme-minimal.css');
        $this->registerCSS('fontawesome/font-awesome.min.css');
        $this->registerCSS('vendor/toastr/toastr.css');
        $this->registerCSS('main.css');

        $this->registerJS('jquery/jquery-1.11.0.min.js');
        $this->registerJS('jquery/jquery.easing.min.js');
        $this->registerJS('util/underscore-1.5.2.min.js');
        $this->registerJS('bootstrap/bootstrap.min.js');
        $this->registerJS('vendor/toastr/toastr.min.js');
        $this->registerJS('pace/pace.min.js');
        $this->registerJS('core/core.js');
        $this->registerJS('core/pooling.js');

        /**
         * Register meta tag
         */
        $this->registerMeta('id_ID', null, null, array('property' => 'og:locale'));
        $this->registerMeta(Yii::app()->name, null, null, array('property' => 'og:site_name'));
    }

    /**
     * Query all notifications
     */
    public function notifications() {
        $userWeb = UserWeb::instance();
        $cookie = Yii::app()->request->cookies;
        if (!$userWeb->isGuest) {
            $user = $userWeb->user();
            /* @var $user User */
        }
    }

    /**
     * Register any values that needed on beforeAction
     * @param CAction $action action
     * @return boolean whether the action is allowed ?
     */
    public function beforeAction($action) {
        if ($action->controller->route !== Yii::app()->errorHandler->errorAction) {
            $userWeb = UserWeb::instance();
            if (isset($_GET['lang'])) {
                $cookie = new CHttpCookie('lang', $_GET['lang']);
                $cookie->expire = time() + 60 * 60 * 24 * 180;
                Yii::app()->request->cookies['lang'] = $cookie;
                $this->redirect(array("$this->route"));
            }
            $this->language = Yii::app()->request->cookies->contains('lang') ? Yii::app()->request->cookies['lang']->value : 'id';

            $this->renderJS(['baseURL' => Yii::app()->baseUrl], ['lang' => $this->language]);
            $isIndonesian = $this->language === 'id';

            if (!$userWeb->isGuest) {
                $user = $userWeb->user();
                if (isset($user) && !$user->verifiedUser) {
                    $this->data['notifications']['verifyEmail'] = [
                        'URL' => $this->createUrl('/site/verify'),
                        'title' => 'Pastikan email kamu terverifikasi',
                        'message' => "Silahkan periksa email anda untuk verifikasi",
                        'labelButton' => 'Verifikasi Ulang'
                    ];
                }
            }
        }
        return parent::beforeAction($action);
    }

    /**
     * Declares class-based actions.
     */
    public function actions() {
        return CMap::mergeArray(parent::actions(), array(
                    'oauth' => array(
                        'class' => 'ext.hoauth.HOAuthAction',
                        'alwaysCheckPass' => true,
                        'usernameAttribute' => 'username',
                        'model' => 'HOAuthUser',
                        'attributes' => array(
                            'email' => 'email',
                            'firstName' => 'firstName',
                            'lastName' => 'lastName',
                            'birthDate' => 'birthDate',
                            'gender' => 'genderShort',
                            'avatarURL' => 'photoURL',
                        ),
                    ),
                    // captcha action renders the CAPTCHA image displayed
                    'captcha' => array(
                        'class' => 'CCaptchaAction',
                        'backColor' => 0xFFFFFF,
                        'testLimit' => 5,
                    ),
        ));
    }

    /**
     * Specifies the access control rules.
     * This method is used by the 'accessControl' filter.
     * @return array access control rules
     */
    public function accessRules() {
        return array_merge(parent::accessRules(), array(
            array('allow', // allow authenticated user to perform 'create' and 'update' actions
                'actions' => array('captcha'),
                'users' => array('*'),
            ),
                )
        );
    }

    /**
     * Access control filter
     * @return string[]
     */
    public function filters() {
        return array(
            'accessControl',
        );
    }

    /**
     * Check if order has phase
     * @param Order $order
     * @param string $phase
     * @return boolean
     */
    public function hasStatus($order, $phase) {
        $status = false;
        switch ($phase) {
            case self::PHASE_OPEN_ORDER:
                $status = is_object($order);
                break;

            case self::PHASE_PAYMENT_CONFIRMATION:
                $status = is_object($order->paymentConfirmation);
                break;

            case self::PHASE_PAID_ORDER:
                $status = is_object($order->paidOrder);
                break;

            case self::PHASE_PROCESSING:
                $status = is_object($order->paidOrder ? $order->paidOrder->process : null);
                break;

            case self::PHASE_SHIPMENT:
                $status = is_object($order->paidOrder ? ($order->paidOrder->process ? $order->paidOrder->process->send : null) : null);
                break;

            case self::PHASE_ARRIVE:
                $status = is_object($order->paidOrder ? ($order->paidOrder->process ? ($order->paidOrder->process->send ? $order->paidOrder->process->send->arrive : null) : null) : null);
                break;

            default:
                break;
        }
        return $status;
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
     * Send token for verify email
     * @param User $user
     */
    public function emailVerify($user) {
        $email = new EmailSender;
        $email->view = 'verify';
        $email->setSubject('Verifikasi Email');
        $email->setBody(['user' => $user, 'token' => $this->token($user)]);
        $email->setTo([$user->email => $user->username]);
        return $email->send();
    }

    /**
     * Initialize toastr.js message on beforeRender
     * @param string $view view
     * @return boolean render success status
     */
    public function beforeRender($view) {
        if (UserWeb::instance()->hasMessage()) {
            $this->renderJS([
                    ], array('message' => array(
                    'status' => UserWeb::instance()->getMessageStatus(),
                    'content' => UserWeb::instance()->getMessageContent()
            )));
        }
        $this->renderJS(['authenticationURL' => $this->createUrl('/parser/authenticate/ask')], []);
        return parent::beforeRender($view);
    }

    /**
     * Get translated name for controller and its action
     * @param Controller $controller
     * @return string
     */
    public function translateMenuName($controller) {
        $name = '';
        switch ($controller->id) {
            case 'tree':
                switch ($controller->action->id) {
                    case 'index':
                        $name = 'Anotasi';
                        break;
                    case 'browser':
                        $name = 'Periksa';
                        break;
                }
                break;
        }
        return $name;
    }

}
