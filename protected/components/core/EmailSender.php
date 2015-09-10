<?php

Yii::import('ext.yii-mail.*');

class EmailSender extends YiiMailMessage {

    /**
     * @var string
     */
    public $removeSubjectWord = ', Administration System';

    /**
     * @var string 
     */
    public $subjectPrefix;

    /**
     * Construct
     * @param string $subject
     * @param string[] $body
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($subject = null, $body = null, $contentType = 'text/html', $charset = null) {
        parent::__construct($subject, $body, $contentType, $charset);
        Yii::app()->name = str_replace($this->removeSubjectWord, '', Yii::app()->name);
        $this->subjectPrefix = sprintf('[%s] %s | ', preg_replace('/^(https?:\/\/)|(www.)/is', '', Yii::app()->getBaseUrl(true)), Yii::app()->name);
        if ($this->message && !count($this->message->getFrom())) {
            $this->setFrom($this->emailNoReply());
        }
    }

    /**
     * Before send
     * @return boolean
     */
    public function beforeSend() {
        return count($this->message->getTo()) > 0;
    }

    /**
     * After send
     */
    public function afterSend() {
        
    }

    /**
     * Send Process
     * @return integer Total number of recipients who were accepted for delivery
     */
    public function send() {
        $count = 0;
        if ($this->beforeSend()) {
            $this->setSubject($this->subjectPrefix . $this->message->getSubject());
            $count = Yii::app()->mail->send($this);
            $this->afterSend();
        }
        return $count;
    }

    /**
     * @return string[]
     */
    public function emailStore() {
        return [Yii::app()->params['emails']['store'] => 'Online Store - ' . Yii::app()->name];
    }

    /**
     * No reply email for set as email from
     * @return string[]
     */
    public function emailNoReply() {
        return [Yii::app()->params['emails']['noReply'] => Yii::app()->name];
    }

}

?>
