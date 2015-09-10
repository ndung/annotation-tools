<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of DatabaseConnection
 *
 * @author Neotrouz
 */
class DatabaseConnection extends CDbConnection {

    /**
     * Opens DB connection if it is currently not
     * @throws CException if connection fails
     */
    protected function open() {
        try {
            parent::open();
        } catch (Exception $exception) {
            // email notif ke Developer/Sysadmin buat restart service
            $email = new EmailSender;
            $email->setSubject('Critical Error - Database not active');
            $email->setBody($exception->getMessage() . '<br />' . CHtml::link('Restart Database Service', 'https://bmustudio.com:8083/restart/service/?srv=mysql'));
            $email->setTo([Yii::app()->params['emails']['sysadmin'] => Yii::app()->params['emails']['sysadmin']]);
            $email->setCC(Yii::app()->params['emails']['developerList']);
            $email->send();
            throw new CHttpException(500, "Maaf database sedang kami matikan sementara, coba akses beberapa saat lagi.");
        }
    }

}

?>
