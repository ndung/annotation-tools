<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available custom columns :
 * The followings are the available custom model relations:
 */
class User extends BUser {

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array_merge(parent::rules(), array(
            array('email, username', 'unique'),
            array('email', 'email'),
        ));
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return CMap::mergeArray(parent::attributeLabels(), array());
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BPost the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    /**
     * Initiate join date and last activity value
     */
    public function afterConstruct() {
        if ($this->scenario == 'insert') {
            $this->joinDate = date('Y-m-d H:i:s');
        }
        return parent::afterConstruct();
    }

}
