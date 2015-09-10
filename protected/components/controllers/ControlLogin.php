<?php

/**
 * Description of ControlLogin
 *
 * @author Andry Luthfi
 */
trait ControlLogin {

    /**
     * list of defined filters mode on this Controller and each classes which
     * inherit.
     * @return string[] defined filters on this Controller and Sub-Class
     */
    public function filters() {
        return ["accessControl"];
    }

    /**
     * make sure the system only can be accessed by user. therefore any 
     * unauthorized user will be redirected to login page to proceed. 
     * @return type mixed accessRules for this Controller. 
     */
    public function accessRules() {
        return array(
            array(
                'allow',
                'actions' => ['login', 'register'],
                'expression' => 'Yii::app()->user->isGuest',
            ),
            array(
                'deny',
                'expression' => 'Yii::app()->user->isGuest',
            ),
            array(
                'deny',
                'actions' => array('login'),
                'expression' => '!Yii::app()->user->isGuest',
            )
        );
    }
}
