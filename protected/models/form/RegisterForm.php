<?php

/**
 * The followings are the available attributes:
 * @property string $domainPrivileges
 * 
 * @property string $email
 * @property string $password
 * @property string $oldPassword
 * @property string $repeatPassword
 * @property string $username
 * @property string $firstName
 * @property string $lastName
 * @property integer $gender
 * @property string $birthDate
 * @property string $avatarURL
 * @property string $verifyCode
 * @property string[] $privileges
 * @property User $user
 */
class RegisterForm extends CFormModel {

    /**
     * @var string[] 
     */
    private $domainPrivileges = [];

    const CHANGE_PASSWORD_SCENARIO = 'change-password';

    /**
     * @var string 
     */
    public $email;

    /**
     * @var string 
     */
    public $password;

    /**
     * @var string 
     */
    public $oldPassword;

    /**
     * @var string 
     */
    public $repeatPassword;

    /**
     * @var string 
     */
    public $username;

    /**
     * @var string
     */
    public $verifyCode;

    /**
     * @var User
     */
    public $user;

    /**
     * Construct the form based on scenario and User model given.
     * @param string $scenario scenario applied
     * @param User $user User model to be initialiazed
     */
    public function __construct($scenario = '', $user = null) {
        $this->user = $user;
        if ($this->user) {
            $this->attributes = $this->user->attributes;
            $this->unsetAttributes(['password']);
        }
        parent::__construct($scenario);
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array_merge(parent::rules(), [
            ['email, username', 'safe'],
            ['email, username', 'required'],
            ['email', 'length', 'max' => 255],
            ['password', 'length', 'min' => 6, 'max' => 128],
            ['username', 'length', 'min' => 3, 'max' => 20],
            ['username', 'match', 'pattern' => '/^[A-Za-z0-9_\-\.]+$/u', 'message' => 'Username can contain only alphanumeric characters, dot (.) and hyphens (-).'],
        ]);
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return CMap::mergeArray(parent::attributeLabels(), [
                    'username' => 'Username',
                    'email' => 'Email',
                    'password' => 'Password',
                    'joinDate' => 'Tanggal Bergabung',
        ]);
    }

    /**
     * Save
     * @return boolean
     */
    public function save() {
        $status = false;
        if (!$this->hasErrors() && $this->validate(null, false)) {
            $user = $this->user ? $this->user : new User;
            /* @var $user User */
            $oldHashPassword = $user->password;
            $user->attributes = $this->attributes;
            if ($user->isNewRecord) {
                $user->password = !empty($this->password) ? CPasswordHelper::hashPassword($this->password) : null;
            } else {
                if (!empty($this->password)) {
                    $isAuthorizedUpdate = ($oldHashPassword === crypt($this->oldPassword, $oldHashPassword) || $this->scenario !== self::CHANGE_PASSWORD_SCENARIO);
                    $isChangedPassword = $oldHashPassword !== crypt($this->password, $oldHashPassword);
                    if ($isChangedPassword) {
                        if ($isAuthorizedUpdate) {
                            $user->password = CPasswordHelper::hashPassword($this->password);
                        } else {
                            $this->addError('oldPassword', 'Password saat ini tidak sesuai dengan inputan');
                        }
                    }
                } else {
                    $user->password = $oldHashPassword;
                }
            }
            $status = $user->save();
            $this->addErrors($user->errors);
            $this->user = $user;
        }
        return $status;
    }

}
