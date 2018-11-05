<?php

/**
 * Description of CoreController
 *
 * @author Andry Luthfi
 */
class SiteController extends ControllerCore {

    use ControlREST,
        ControlLogin;

    /**
     * Main Page
     */
    public function actionIndex() {
        $cookie = Yii::app()->request->cookies;
        $userWeb = UserWeb::instance();
        $user = $userWeb->user();
        /* @var $user User */
        $workspace = isset($cookie['workspace']) ? $cookie['workspace']->value : null;
        if ($workspace === null || !$user->moderator) {
            $strings = $user->stringAssigneds;
            $workspace = implode(',', CHtml::listData($strings, 'corpusParseTreeStringID', 'corpusParseTreeStringID'));
        }
        $documents = $this->retrieveDocumentsByStringID(explode(',', $workspace));

        if (UserWeb::instance()->isModerator()) {
            $this->data['contextMenu'] = [
                [
                    'label' => 'Buat atau Buka Jawaban Sendiri',
                    'symbol' => 'glyphicon glyphicon-cloud-download',
                    'htmlOptions' => [
                        'data-toggle' => 'modal',
                        'data-target' => '#modal-explore-mystring',
                        'toggle-input' => 'input-resource-corpus',
                        'class' => 'btn btn-default btn-xs action-toggle-input'
                    ]
                ],
                [
                    'label' => 'Kosongkan Workspace',
                    'symbol' => 'glyphicon glyphicon-star-empty',
                    'htmlOptions' => [
                        'class' => 'btn btn-default btn-xs action-clear-workspace'
                    ]
                ],
				[
                            'label' => 'Unduh',
                            'symbol' => 'glyphicon glyphicon-save',
                            'htmlOptions' => [
                                'class' => 'btn btn-default btn-xs action-download'
                            ]
                        ],
                        [
                            'label' => 'Simpan ini',
                            'symbol' => 'glyphicon glyphicon-cloud-upload',
                            'htmlOptions' => [
                                'method' => 'current',
                                'class' => 'btn btn-default btn-xs action-save'
                            ]
                        ],
                        [
                            'label' => 'Simpan semua',
                            'symbol' => 'glyphicon glyphicon-cloud-upload',
                            'htmlOptions' => [
                                'method' => 'all',
                                'class' => 'btn btn-default btn-xs action-save'
                            ]
                        ]
            ];
        }
        else {
            $this->data['contextMenu'] = [
                        [
                            'label' => 'Unduh',
                            'symbol' => 'glyphicon glyphicon-save',
                            'htmlOptions' => [
                                'class' => 'btn btn-default btn-xs action-download'
                            ]
                        ],
                        [
                            'label' => 'Simpan ini',
                            'symbol' => 'glyphicon glyphicon-cloud-upload',
                            'htmlOptions' => [
                                'method' => 'current',
                                'class' => 'btn btn-default btn-xs action-save'
                            ]
                        ],
                        [
                            'label' => 'Simpan semua',
                            'symbol' => 'glyphicon glyphicon-cloud-upload',
                            'htmlOptions' => [
                                'method' => 'all',
                                'class' => 'btn btn-default btn-xs action-save'
                            ]
                        ]
            ];
        }
        $this->renderJS([
            'write' => $this->createUrl('write'),
            'load-explorer' => $this->createUrl('explorerMyString'),
            'load-documents' => $this->createUrl('documents'),
                ], []);
        $this->render('manage-annotate', ['documents' => $documents, 'user' => $user]);
    }

    /**
     * Browser Page
     */
    public function actionBrowser() {
        $cookie = Yii::app()->request->cookies;
        $userWeb = UserWeb::instance();
        $user = $userWeb->user();
        /* @var $user User */
        $stringsSolution = isset($cookie['strings-inspect']) ? $cookie['strings-inspect']->value : null;
        $solutions = $this->retrieveSolutionsByStringID(explode(',', $stringsSolution));

        $this->data['contextMenu'] = [
            [
                'label' => 'Buka Jawaban',
                'symbol' => 'glyphicon glyphicon-cloud-download',
                'htmlOptions' => [
                    'data-toggle' => 'modal',
                    'data-target' => '#modal-explore-allstring',
                    'toggle-input' => 'input-resource-corpus',
                    'class' => 'btn btn-default btn-xs action-toggle-input'
                ]
            ],
        ];

        $this->renderJS([
            'write' => $this->createUrl('write'),
            'load-explorer' => $this->createUrl('explorerAllString'),
            'load-solutions' => $this->createUrl('solutions'),
                ], []);
        $this->render('manage-browse', ['solutions' => $solutions, 'user' => $user]);
    }

    /**
     * Explorer Page
     */
    public function actionExplorerMyString($corpusID = null) {
        $userWeb = UserWeb::instance();
        $user = $userWeb->user();
        $criteria = new CDbCriteria;
        $criteria->limit = 21;
        if ($corpusID) {
            $criteria->compare('corpusParseTreeID', $corpusID);
        }
        $solutions = new CActiveDataProvider('CorpusParseTreeString', ['criteria' => $criteria, 'pagination' => ['pageSize' => 21]]);
        $corpuses = $corpusID ? [] : CorpusParseTree::model()->findAll();
        $this->render('explore-my-strings', ['solutions' => $solutions, 'user' => $user, 'corpuses' => $corpuses]);
    }

    /**
     * Explorer Page
     */
    public function actionExplorerAllString($corpusID = null, $userID = null, $stringID = null) {
        $userWeb = UserWeb::instance();
        $user = $userWeb->user();
        $criteria = new CDbCriteria;
        $criteria->limit = 21;
        if ($corpusID) {
            $criteria->with[] = 'corpusParseTreeString';
            $criteria->compare('corpusParseTreeString.corpusParseTreeID', $corpusID);
        }
        if ($userID) {
            $criteria->compare('userID', $userID);
        }
        if ($stringID) {
            $criteria->compare('corpusParseTreeStringID', $stringID);
        }
        $solutions = new CActiveDataProvider('CorpusParseTreeSolution', ['criteria' => $criteria, 'pagination' => ['pageSize' => 21]]);
        $this->render('explore-all-strings', ['solutions' => $solutions, 'user' => $user]);
    }

    /**
     * Load a Session
     */
    public function actionLoad($sessionHash, $documentID = null) {
        $session = Session::model()->find('hash=:signature', [':signature' => $sessionHash]);
        /* @var $session Session */
        $documents = [];
        if ($session) {
            if ($documentID) {
                $document = Document::model()->findByAttributes(['sessionID' => $session->ID, 'ID' => $documentID]);
                if ($document) {
                    $documents[] = $document;
                }
            } else {
                $documents = $session->documents;
            }
        }

        $this->sendResponse(200, CJSON::encode(['sessionID' => $session->ID, 'documents' => $documents]), 'application/json');
    }

    /**
     * Update a document a Session
     */
    public function actionWrite($stringID) {
        $responseCode = isset($_POST['value']) ? 200 : 500;
        $response = null;
        if ($responseCode === 200) {
            $userWeb = UserWeb::instance();
            $string = CorpusParseTreeSolution::model()->findByAttributes(['userID' => $userWeb->id, 'corpusParseTreeStringID' => $stringID]);
            /* @var $string CorpusParseTreeSolution */
            if (!$string) {
                $string = new CorpusParseTreeSolution;
                $string->userID = $userWeb->id;
                $string->corpusParseTreeStringID = $stringID;
            }
            $string->string = $_POST['value'];
            $responseCode = $string->save() ? 200 : 500;

            if ($responseCode === 200) {
                $response = ['message' => 'Sesi anda sudah disimpan di cloud'];
            } else {
                $response = ['errors' => $string->errors];
            }
        }
        $this->sendResponse($responseCode, CJSON::encode($response), 'application/json');
    }

    /**
     * Partially show list of Workspace requested
     * @throws CHttpException
     */
    public function actionDocuments() {
        if (Yii::app()->request->isAjaxRequest && isset($_POST['documentsID'])) {
            $this->renderPartial('partial/list-my-strings', [
                'documents' => $this->retrieveDocumentsByStringID(explode(',', $_POST['documentsID'])),
                'user' => UserWeb::instance()->user()
            ]);
        } else {
            throw new CHttpException(403, "Maaf halaman ini tidak dapat diakses secara langsung");
        }
    }

    /**
     * Partially show list of Solutions requested
     * @throws CHttpException
     */
    public function actionSolutions() {
        if (Yii::app()->request->isAjaxRequest && isset($_POST['documentsID'])) {
            $this->renderPartial('partial/list-all-strings', [
                'solutions' => $this->retrieveSolutionsByStringID(explode(',', $_POST['documentsID'])),
                'user' => UserWeb::instance()->user()
            ]);
        } else {
            throw new CHttpException(403, "Maaf halaman ini tidak dapat diakses secara langsung");
        }
    }

    /**
     * Return current logged User Solution to this document
     * @param \CorpusParseTreeString $document
     * @param \User $selectedUser
     * @return \CorpusParseTreeSolution
     */
    public function userSolution($document, $selectedUser = null) {
        return CorpusParseTreeSolution::model()->findByAttributes(['corpusParseTreeStringID' => $document->ID, 'userID' => $selectedUser ? $selectedUser->ID : UserWeb::instance()->id]);
    }

    /**
     * Retrieve documents based on given string ID
     * @param string $stringsID list of ID of range of ID unified by a string.
     *                          eg. 1,2,4-5,22
     * @return \CorpusParseTreeString[]
     */
    protected function retrieveDocumentsByStringID($stringsID) {
        $userWeb = UserWeb::instance();
        $user = $userWeb->user();
        /* @var $user User */
        $documents = [];
        foreach ($stringsID as $stringID) {
            $condition = explode('-', $stringID);
            $case = count($condition);
            switch ($case) {
                case 1:
                    $document = $user->moderator ?
                            CorpusParseTreeString::model()->findByPk($condition[0]) :
                            CorpusParseTreeString::model()->with('stringAssigneds')->findByPk($condition[0], 'stringAssigneds.userID=:currentUserID', [':currentUserID' => $user->ID]);
                    if ($document) {
                        $documents[] = $document;
                    }
                    break;
                case 2:
                    $retrieved = $user->moderator ?
                            CorpusParseTreeString::model()->findAll('ID>=:min and ID<=:max', [':min' => $condition[0], ':max' => $condition[1]]) :
                            CorpusParseTreeString::model()->with('stringAssigneds')->findAll('ID>=:min and ID<=:max and stringAssigneds.userID=:currentUserID', [':min' => $condition[0], ':max' => $condition[1], ':currentUserID' => $user->ID]);
                    $documents = array_merge($documents, $retrieved);
                    break;
            }
        }

        return $documents;
    }

    /**
     * Retrieve solutions based on given string ID
     * @param string $stringsID list of ID of range of ID unified by a string.
     *                          eg. 1,2,4-5,22
     * @return \CorpusParseTreeSolution[]
     */
    protected function retrieveSolutionsByStringID($stringsID) {
        $userWeb = UserWeb::instance();
        $user = $userWeb->user();
        /* @var $user User */
        $solutions = [];
        foreach ($stringsID as $stringID) {
            $solution = CorpusParseTreeSolution::model()->findByPk($stringID);
            if ($solution) {
                $solutions[] = $solution;
            }
        }

        return $solutions;
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        $this->layout = '//layouts/parent-raw';
        $this->pageTitle = Yii::app()->name . ' - Error';

        $error = Yii::app()->errorHandler->error;
        if ($error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        }
    }

}
