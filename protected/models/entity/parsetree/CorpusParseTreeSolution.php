<?php

/**
 * This is the model class for table "corpus_parse_tree_solution".
 *
 * The followings are the available columns in table 'corpus_parse_tree_solution':
 * @property string $sentence
 * The followings are the available model relations:
 */
class CorpusParseTreeSolution extends BCorpusParseTreeSolution {
    
    use ParseTreeStringFactory;
    
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

    /**
     * Insert date modified right before validate the model then save the model
     * into database
     * @return boolean if success in beforeValidate
     */
    public function beforeValidate() {
        $this->dateModified = date('Y-m-d H:i:s');
        return parent::beforeValidate();
    }

}
