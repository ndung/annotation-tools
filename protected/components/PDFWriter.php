<?php

Yii::import('ext.mpdf.*');

class PDFWriter extends mPDF {
    /* ------------------------------------------------------ */
    /*                  Custom static value                   */
    /* ------------------------------------------------------ */

    /**
     * @var string Define pdf creator
     */
    public $creator = '';

    /* ------------------------------------------------------ */

    /**
     * @var string
     */
    public $mode = '';

    /**
     * @var integer
     */
    public $defaultFontSize = 12;

    /**
     * @var string
     */
    public $defaultFont = 'chelvetica';

    /**
     * @var integer 
     */
    public $marginLeft = 12;

    /**
     * @var integer 
     */
    public $marginRight = 12;

    /**
     * @var integer 
     */
    public $marginTop = 10;

    /**
     * @var integer 
     */
    public $marginBottom = 14;

    /**
     * @var integer 
     */
    public $marginHeader = 10;

    /**
     * @var integer 
     */
    public $marginFooter = 10;

    /**
     *
     * @var string
     */
    public $layouts = '//layouts/PDFLayout';

    /**
     * Initializes the pager by setting some default property values.
     * @param string $html
     * @param string $title
     * @param mixed $format
     * @param string $orientation
     */
    public function __construct($html, $title = '', $format = 'A4', $orientation = 'P') {
        parent::__construct($this->mode, $format, $this->defaultFontSize, $this->defaultFont, $this->marginLeft, $this->marginRight, $this->marginTop, $this->marginBottom, $this->marginHeader, $this->marginFooter, $orientation);
        $controller = new CController('PDF');
        $controller->layout = $this->layouts;
        $html = $controller->renderText($html, true);

        $this->SetCreator($this->creator);
        $this->SetTitle($title);
        if (!UserWeb::instance()->isGuest) {
            $this->SetFooter(sprintf('Berkas dicetak oleh %s, %s||{PAGENO}', UserWeb::instance()->user()->username, date('Y-m-d H:i:s')));
        } else {
            throw new CHttpException(403, 'Anda tidak memiliki otoritas untuk mengakses halaman ini');
        }
        $this->writeHTML($html);
    }

}

?>
