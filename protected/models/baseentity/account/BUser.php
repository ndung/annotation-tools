<?php

/**
 * This is the model class for table "user".
 *
 * The followings are the available columns in table 'user':
 * @property integer $ID
 * @property string $email
 * @property string $username
 * @property string $password
 * @property string $joinDate
 *
 * The followings are the available model relations:
 * @property CorpusParseTreeSolution[] $corpusParseTreeSolutions
 * @property Moderator $moderator
 * @property StringAssigned[] $stringAssigneds
 * @property VerifiedUser $verifiedUser
 */
class BUser extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'user';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('email, username, password, joinDate', 'required'),
			array('email', 'length', 'max'=>255),
			array('username', 'length', 'max'=>20),
			array('password', 'length', 'max'=>128),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('ID, email, username, password, joinDate', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'corpusParseTreeSolutions' => array(self::HAS_MANY, 'CorpusParseTreeSolution', 'userID'),
			'moderator' => array(self::HAS_ONE, 'Moderator', 'userID'),
			'stringAssigneds' => array(self::HAS_MANY, 'StringAssigned', 'userID'),
			'verifiedUser' => array(self::HAS_ONE, 'VerifiedUser', 'userID'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'ID' => 'ID',
			'email' => 'Email',
			'username' => 'Username',
			'password' => 'Password',
			'joinDate' => 'Join Date',
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
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('ID',$this->ID);
		$criteria->compare('email',$this->email,true);
		$criteria->compare('username',$this->username,true);
		$criteria->compare('password',$this->password,true);
		$criteria->compare('joinDate',$this->joinDate,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return BUser the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
