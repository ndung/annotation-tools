<?php

/**
 * This is the model class for table "corpus_parse_tree_string".
 *
 * The followings are the available columns in table 'corpus_parse_tree_string':
 * @property integer $ID
 * @property integer $corpusParseTreeID
 * @property string $string
 *
 * The followings are the available model relations:
 * @property CorpusParseTreeConsensus $corpusParseTreeConsensus
 * @property CorpusParseTreeSolution[] $corpusParseTreeSolutions
 * @property CorpusParseTree $corpusParseTree
 * @property StringAssigned[] $stringAssigneds
 */
class BCorpusParseTreeString extends CActiveRecord {

    /**
     * @return string the associated database table name
     */
    public function tableName() {
        return 'corpus_parse_tree_string';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules() {
        // NOTE: you should only define rules for those attributes that
        // will receive user inputs.
        return array(
            array('corpusParseTreeID, string', 'required'),
            array('corpusParseTreeID', 'numerical', 'integerOnly' => true),
            // The following rule is used by search().
            // @todo Please remove those attributes that should not be searched.
            array('ID, corpusParseTreeID, string', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations() {
        // NOTE: you may need to adjust the relation name and the related
        // class name for the relations automatically generated below.
        return array(
            'corpusParseTreeConsensus' => array(self::HAS_ONE, 'CorpusParseTreeConsensus', 'corpusParseTreeStringID'),
            'corpusParseTreeSolutions' => array(self::HAS_MANY, 'CorpusParseTreeSolution', 'corpusParseTreeStringID'),
            'corpusParseTree' => array(self::BELONGS_TO, 'CorpusParseTree', 'corpusParseTreeID'),
            'stringAssigneds' => array(self::HAS_MANY, 'StringAssigned', 'corpusParseTreeStringID'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels() {
        return array(
            'ID' => 'ID',
            'corpusParseTreeID' => 'Corpus Parse Tree',
            'string' => 'String',
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
        $criteria->compare('corpusParseTreeID', $this->corpusParseTreeID);
        $criteria->compare('string', $this->string, true);

        return new CActiveDataProvider($this, array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return BCorpusParseTreeString the static model class
     */
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

}
