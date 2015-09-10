<?php

/**
 * Base UserWeb
 */
class BUserWeb extends CWebUser {

    /**
     * @var User cached User information
     */
    private $user;

    /**
     * Load user model.
     * @param integer $id selected ID of user
     * @return User selected user which given ID of user
     */
    protected function loadUser($id = null) {
        if ($this->user === null) {
            if ($id !== null) {
                $this->user = User::model()->findByPk($id);
            } else if (!$this->isGuest) {
                try {
                    $this->user = User::model()->findByPk(Yii::app()->user->id);
                } catch (CHttpException $exc) {
                    $this->logout();
                }
            }
        }
        return $this->user;
    }

    /**
     * Return this instance UserWeb which stored in session of this App.
     * @return UserWeb instance UserWeb which stored in session
     */
    public static function instance() {
        return Yii::app()->user;
    }

    /**
     * @return User get User
     */
    public function user() {
        return $this->loadUser();
    }

    /**
     * Set flash for common message system / notification system. 
     * @param string $status basically the code for differentiate one message to 
     *                       another. eg. : info, warning, alert-info, etc. 
     * @param string $message the message to displayed
     * @param string $context postfix for flashes' name
     */
    public function setMessage($status, $message, $context = "") {
        $this->setFlash("status$context", $status);
        $this->setFlash("message$context", $message);
    }

    /**
     * Check the availability of message
     * @param string $context postfix for flashes' name
     * @return boolean availbility of message
     */
    public function hasMessage($context = "") {
        return $this->hasFlash("message$context") || $this->hasFlash("code$context");
    }

    /**
     * Get the message's status
     * @param string $context postfix for flashes' name
     * @return string message's status
     */
    public function getMessageStatus($context = "") {
        return $this->hasMessage($context) ? $this->getFlash("status$context") : "";
    }

    /**
     * Get the message's content
     * @param string $context postfix for flashes' name
     * @return string message's content
     */
    public function getMessageContent($context = "") {
        return $this->hasMessage($context) ? $this->getFlash("message$context") : "";
    }

    /**
     * Add file's info about temporary uploaded file.
     * @param string[] $fileInfo
     * @param string $context
     */
    public function addTemporaryFileUploaded($fileInfo, $context) {
        $keyState = "files-$context";
        $files = $this->hasState($keyState) ? $this->getState($keyState) : array();
        $files[$fileInfo['path']] = $fileInfo;
        $this->setState($keyState, $files);
    }

    /**
     * Add file's info about temporary uploaded file.
     * @param string[] $fileInfo
     * @param string $context
     */
    public function deleteTemporaryFileUploaded($path, $context) {
        $keyState = "files-$context";
        $files = $this->hasState($keyState) ? $this->getState($keyState) : array();
        if (isset($files[$path])) {
            unset($files[$path]);
        }
        $this->setState($keyState, $files);
    }

    /**
     * Get all temporary uploaded files' info
     * @param string $context
     * @return string[] all temporary uploaded files' info
     */
    public function getTemporaryFilesUploaded($context) {
        $keyState = "files-$context";
        return $this->hasState($keyState) ? $this->getState($keyState) : array();
    }

    /**
     * Clear up all temporary uploaded files' info
     * @param string $context
     */
    public function clearTemporaryFilesUploaded($context) {
        $keyState = "files-$context";
        if ($this->hasState($keyState)) {
            $filesInfo = $this->getState($keyState);
            foreach ($filesInfo as $fileInfo) {
                $basePath = Yii::app()->basePath . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
                $filePath = $basePath . $fileInfo['path'];
                if (file_exists($filePath)) {
                    // to-do: delete the folder which wrap it out
                    unlink($filePath);
                }
            }
            $this->setState($keyState, null);
        }
    }

}
