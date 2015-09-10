<?php

/**
 * This is the model class for table "string_assigned".
 *
 * The followings are the available columns in table 'string_assigned':
 */
class StringAssigned extends BStringAssigned {

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array_merge(parent::rules(), array());
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

}
