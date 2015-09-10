<?php

/**
 * This is the model class for table "corpus_parse_tree_solution".
 *
 * The followings are the available columns in table 'corpus_parse_tree_solution':
 * @property integer $ID
 * @property integer $corpusParseTreeStringID
 * @property integer $userID
 * @property string $string
 * @property string $dateModified
 *
 * The followings are the available model relations:
 * @property CorpusParseTreeString $corpusParseTreeString
 * @property User $user
 */
class BCorpusParseTreeSolution extends BaseModel {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'corpus_parse_tree_solution';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('corpusParseTreeStringID, userID, string, dateModified', 'required'),
            array('corpusParseTreeStringID, userID', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('ID, corpusParseTreeStringID, userID, string, dateModified', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'corpusParseTreeString' => array(self::BELONGS_TO, 'CorpusParseTreeString', 'corpusParseTreeStringID'),
            'user' => array(self::BELONGS_TO, 'User', 'userID'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'ID' => 'ID',
            'corpusParseTreeStringID' => 'Corpus Parse Tree String',
            'userID' => 'User',
            'string' => 'String',
            'dateModified' => 'Date Modified',
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * Typical usecase:
     * - Initialize the model fields with values from filter form.
     * - Execute this method to get CActiveDataProvider instance which will filter
     * models according to data in model fields.
     * - Pass data provider to CGridView, CListView or any similar widget.
     *
     * @return CActiveDataProvider the data provider that can return the models
     * based on the search/filter conditions.
     */
    public function search() {
        // @todo Please modify the following code to remove attributes that should not be searched.

        $criteria = new CDbCriteria;

        $criteria->compare('ID', $this->ID);
        $criteria->compare('corpusParseTreeStringID', $this->corpusParseTreeStringID);
        $criteria->compare('userID', $this->userID);
        $criteria->compare('string', $this->string, true);
        $criteria->compare('dateModified', $this->dateModified, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BCorpusParseTreeSolution the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
