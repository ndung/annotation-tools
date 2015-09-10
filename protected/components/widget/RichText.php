<?php

/**
 * Custom TextArea
 * @author Neotrouz
 */
class RichText extends CWidget {

    /**
     * @var string 
     */
    public $id = 'markitup';

    /**
     * @var CActiveRecord 
     */
    public $model;

    /**
     * @var string 
     */
    public $attribute;

    /**
     * @var string 
     */
    public $name;

    /**
     * @var string
     */
    public $value;

    /**
     * @var string[] 
     */
    public $htmlOptions = [];

    /**
     * Init
     */
    public function init() {
        $controller = $this->getController();
        $controller->registerJS('markitup/jquery.markitup.js', CClientScript::POS_BEGIN);
        $controller->registerJS('jquery/jquery-ui.min.js', CClientScript::POS_BEGIN);
        $controller->registerJS('jquery/jquery.caret.js', CClientScript::POS_BEGIN);
        $controller->registerJS('markitup/set.js', CClientScript::POS_BEGIN);

        $controller->registerCSS('markitup/style.css');
        $controller->registerCSS('jquery/jquery-ui.min.css');
        parent::init();
    }

    /**
     * Run script
     */
    public function run() {
        $this->htmlOptions['id'] = $this->id;
        if (!isset($this->htmlOptions['class'])) {
            $this->htmlOptions['class'] = 'form-control textarea-control markitup-control';
        }
        if (!isset($this->htmlOptions['contenteditable'])) {
            $this->htmlOptions['contenteditable'] = true;
        }

        $textarea = '';
        if ($this->model instanceof CModel) {
            if (!$this->attribute) {
                throw new CException(Yii::t('yii', 'The $attribute argument must be a instance of CActiveRecord.'));
            } else {
                $textarea = CHtml::activeTextArea($this->model, $this->attribute, $this->htmlOptions);
            }
        } else {
            if (!$this->name) {
                throw new CException(Yii::t('yii', 'The $model or $name argument must be a filled.'));
            } else {
                $textarea = CHtml::textArea($this->name, $this->value, $this->htmlOptions);
            }
        }

        $this->render('rich-text', [
            'textarea' => $textarea,
            'emoticonCategories' => EmoticonCategory::model()->findAll()
        ]);
    }

}

?>
