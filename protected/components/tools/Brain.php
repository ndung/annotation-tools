<?php

/**
 * Brain
 */
class Brain {

    /**
     * @var string
     */
    public $host;

    /**
     * @var string
     */
    public $database;

    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * Init
     */
    public function __construct() {
        $connectionString = Yii::app()->db->connectionString;
        if (preg_match('/dbname=([^;]*)/', $connectionString, $matchesDB) && preg_match('/host=([^;]*)/', $connectionString, $matchesHost)) {
            $this->host = $matchesHost[1];
            $this->database = $matchesDB[1];
            $this->username = Yii::app()->db->username;
            $this->password = Yii::app()->db->password;
        }
    }

    /**
     * Update current article relating with Brain
     */
    public function updateRelatedArticle() {
        $result = null;
        $command = isset(Yii::app()->params['articleRelatingCommand']) ? Yii::app()->params['articleRelatingCommand'] . sprintf(" %s %s %s %s", $this->host, $this->database, $this->username, $this->password) : null;
        if (!empty($command)) {
            $result = shell_exec($command);
        }
        return $result;
    }

}

?>
