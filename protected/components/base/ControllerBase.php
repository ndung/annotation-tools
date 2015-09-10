<?php

/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend 
 * from this base class. All common requirement is defined in this Controller.
 * This is base of ControllerBase. in order to make this class more specific
 * in each project, you'll need to inherit this class.
 */
abstract class ControllerBase extends CController {

    /**
     * @var string the default layout for the controller view. Defaults to 
     * '//layouts/parent-main', 
     */
    public $layout = '//layouts/parent-main';

    /**
     * @var string[] Stacked JS which initialize more than once.
     */
    public $stackedJS = ['urls' => [], 'values' => []];

    /**
     * @var mixed all data passes through layout and it's children 
     *      [
     *          'notification' => [],
     *          'title' => '',
     *          'menus' => [],
     *          'breadcrumbs' => [],
     *      ]
     */
    public $data = [
        'notifications' => [],
        'title' => '',
        'menus' => [],
        'breadcrumbs' => []
    ];

    /**
     * @var string  Served as overider landingURL. Ussually used for canonical 
     * URL that have multiple URLs which identical.
     */
    public $landingURL = null;

    /**
     * Default defined behaviors on this Class.
     * @return mixed default behaviors on this Class.
     */
    public function behaviors() {
        return array(
        );
    }

    /**
     * Retrieve rendered on string format on /views/template. depends on 
     * given sub-view. eg. : /views/template/template-dateParameter
     * @param string $view name of sub-view
     * @param mixed $data data which pass into
     * @return string string version of rendered sub-view
     */
    public function template($view, $data = array()) {
        return $this->renderPartial("//template/$view", $data, true);
    }

    /**
     * Initialize JS values and urls. in order to make JS code so much tidier.
     * such doge isn't? you can access the urls or values via indexes of
     * simakbmn.values or simakbmn.urls
     * @param string[] $values list of values
     * @param string[] $urls list of urls
     * @param integer $placement placement of the script init.
     */
    public function renderJS($urls, $values, $placement = CClientScript::POS_HEAD) {
        $this->stackedJS['values'] = CMap::mergeArray($this->stackedJS['values'], $values);
        $this->stackedJS['urls'] = CMap::mergeArray($this->stackedJS['urls'], $urls);
        $optionValues = CJavaScript::encode($this->stackedJS['values']);
        $optionURLs = CJavaScript::encode($this->stackedJS['urls']);
        Yii::app()->clientScript->registerScript(__CLASS__ . '#core', "core.init($optionURLs, $optionValues); ", $placement);
    }

    /**
     * Register any values that needed on beforeAction
     * @param CAction $action action
     * @return boolean whether the action is allowed ?
     */
    public function beforeAction($action) {
        $this->registers();
        $this->notifications();

        $this->renderJS(array('baseURL' => Yii::app()->baseUrl), array());
        return parent::beforeAction($action);
    }

    /**
     * Initialize toastr.js message on beforeRender
     * @param string $view view
     * @return boolean render success status
     */
    public function beforeRender($view) {
        if (UserWeb::instance()->hasMessage()) {
            $this->renderJS(array(), array('message' => array(
                    'status' => UserWeb::instance()->getMessageStatus(),
                    'content' => UserWeb::instance()->getMessageContent()
            )));
        }
        return parent::beforeRender($view);
    }

    /**
     * try to catch reliable client's IP
     * @return string client's IP
     */
    public function catchClientIP() {
        $IP = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] :
                !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] :
                        $_SERVER['REMOTE_ADDR'];
        return $IP;
    }

    /**
     * Helps to register script. must be under directory 
     * <code>scripts/</code>
     * @param string $URL URL defined under scripts directory
     * @param integer $position position of defined script
     */
    public function registerJS($URL, $position = null) {
        /* @var $clientScripts CClientScript */
        $baseUrl = Yii::app()->baseUrl;
        $clientScripts = Yii::app()->getClientScript();
        $absoluteURL = "$baseUrl/scripts/$URL";
        $clientScripts->registerScriptFile($absoluteURL, $position);
    }

    /**
     * Helps to register styles. must be under directory 
     * <code>styles/</code>
     * @param string $URL URL defined under styles directory
     */
    public function registerCSS($URL) {
        /* @var $clientScripts CClientScript */
        $baseUrl = Yii::app()->baseUrl;
        $clientScripts = Yii::app()->getClientScript();
        $absoluteURL = "$baseUrl/styles/$URL";
        $clientScripts->registerCssFile($absoluteURL);
    }

    /**
     * Helps to register meta.
     * @param string $URL URL defined under styles directory
     */
    public function registerMeta($content, $name = null, $httpEquiv = null, $options = array()) {
        /* @var $clientScripts CClientScript */
        $clientScripts = Yii::app()->getClientScript();
        $clientScripts->registerMetaTag($content, $name, $httpEquiv, $options);
    }

    /**
     * Exception when File Not Found
     * @throws CHttpException Exception when File Not Found
     */
    public function exceptionFileNotFound() {
        throw new CHttpException('404', 'Maaf, sepertinya tautan untuk berkas ini sudah hilang');
    }

    /**
     * Exception when Model Not Found
     * @throws CHttpException Exception when File Not Found
     */
    public function exceptionModelNotFound() {
        throw new CHttpException('404', 'Maaf, sepertinya tautan untuk data ini sudah hilang');
    }

    /**
     * Initialize all scripts and styles.
     */
    public abstract function registers();

    /**
     * Query all notifications
     */
    public abstract function notifications();

    /**
     * Register implements meta tag
     * @param MetaModel $metaModels
     */
    public function registerMetaModel($metaModels) {
        foreach ($metaModels as $metaModel) {
            if ($metaModel instanceof MetaModel) {
                foreach ($metaModel->getMeta() as $meta) {
                    $this->registerMeta(
                            isset($meta[0]) ? $meta[0] : null, isset($meta[1]) ? $meta[1] : null, isset($meta[2]) ? $meta[2] : null, isset($meta[3]) ? $meta[3] : array()
                    );
                }
            }
        }
    }

    /**
     * String Summarizer
     */
    public function summarize($string, $options = array()) {
        $label = isset($options['label']) ? $options['label'] : ' [Selengkapnya] ';
        $link = isset($options['link']) ? $options['link'] : 'javascript:;';
        $htmlOptions = isset($options['htmlOptions']) ? $options['htmlOptions'] : [];
        $limit = isset($options['limit']) ? $options['limit'] : 500;

        $append = '';
        $rawContent = strip_tags(Util::stripBBCode($string));
        if (strlen($rawContent) > $limit) {
            $append = CHtml::link($label, $link, $htmlOptions);
        }
        return Util::characterLimiter($rawContent, $limit) . '<br/>' . $append;
    }

    /**
     * Get total view or statistic value of total view in given URL
     * @return integer
     */
    public function statisticView($url) {
        return PageLanding::model()->countByAttributes(['landingURL' => $url]);
    }

}
