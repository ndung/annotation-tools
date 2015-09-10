<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of BaseModel
 *
 * @author Andry Luthfi
 */
class BaseModel extends CActiveRecord {

    public $fieldTypes = array(
        'ID' => false
    );

    /**
     * Return the appropriate field type by lookup through $fieldTypes which
     * defined privately specific for this class.
     * @param string $attributeName looked up attribute's name 
     */
    public function fieldType($attributeName) {
        $fieldType = isset($this->fieldTypes[$attributeName]) ?
                $this->fieldTypes[$attributeName] :
                false;

        if (!$fieldType) {
            // rule based suggestion
            if (preg_match('/.*password.*/', $attributeName)) {
                $fieldType = 'password';
            }
            if (!$fieldType && preg_match('/.*(name|title).*/', $attributeName)) {
                $fieldType = 'text';
            }
            if (!$fieldType && preg_match('/^is[A-Z]+.*/', $attributeName)) {
                $fieldType = 'boolean';
            }
            if (!$fieldType && (preg_match('/.*[\w]Date.*/', $attributeName) || preg_match('/^date.*/', $attributeName))) {
                $fieldType = 'date';
            }
            if (!$fieldType && preg_match('/.*([cC]ontent|[dD]esc).*/', $attributeName)) {
                $fieldType = 'content';
            }
            if (!$fieldType && preg_match('/.*URL.*/', $attributeName)) {
                $fieldType = 'file';
            }
        }

        return $fieldType;
    }

    /**
     * Define appropriate fieldName by lookup via fieldType function
     * @see fieldType()
     * @param string $attributeName looked up attribute's name
     * @return string field's name
     */
    public function fieldName($attributeName) {
        $name = 'textField';
        switch ($this->fieldType($attributeName)) {
            case 'password':
                $name = 'passwordField';
                break;
            case 'date':
                $name = 'dateField';
                break;
            case 'boolean':
                $name = 'checkBox';
                break;
            case 'content':
                $name = 'textArea';
                break;
            case 'file':
                $name = 'fileField';
                break;

            case 0:
            case 'text':
                break;
        }
        return $name;
    }

    /**
     * Basically just update the attributes. Somehow it can be override to 
     * other purposes.
     * @param string[] $attributes attributes that replaced the old one
     */
    public function updateAttributes($attributes) {
        $this->setAttributes($attributes);
    }

    /**
     * This method is invoked after each record is instantiated by a find method.
     * The default implementation raises the {@link onAfterFind} event.
     * You may override this method to do postprocessing after each newly found 
     * record is instantiated. 
     */
    public function afterFind() {
        parent::afterFind();
    }

    /**
     * Retieve all relation which defined as CHasManyRelation
     * @see CHasManyRelation
     * @see relations()
     * @return string[] relations info on CHasManyRelation relation defined
     *                  in this class
     */
    public function relationsMany() {
        $relations = array();
        foreach ($this->relations() as $relation => $relationInfo) {
            if (strcasecmp($relationInfo[0], 'CHasManyRelation') === 0) {
                $relations[$relation] = $relationInfo;
            }
        }
        return $relations;
    }

    /**
     * Retieve all relation which defined as CBelongsToRelation
     * @see CBelongsToRelation
     * @see relations()
     * @return string[] relations info on CBelongsToRelation relation defined
     *                  in this class
     */
    public function relationsBelong() {
        $relations = array();
        foreach ($this->relations() as $relation => $relationInfo) {
            if (strcasecmp($relationInfo[0], 'CBelongsToRelation') === 0) {
                $relations[$relation] = $relationInfo;
            }
        }
        return $relations;
    }

}
