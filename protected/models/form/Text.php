<?php

/**
 * Text class.
 */
class Text extends CFormModel {

    public $value;

    /**
     * Declares the validation rules.
     */
    public function rules() {
        return array(
            array('value', 'required'),
        );
    }

}
