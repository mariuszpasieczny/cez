<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Controller_Action_Helper_XlsContext extends Zend_Controller_Action_Helper_AjaxContext {

    /**
     * Constructor
     *
     * Add HTML context
     * 
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->addContext('xls', array('suffix' => 'xls', 'headers'
            => array('Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            //'Content-Disposition' => 'attachment; filename=the_filename'
            //'Content-Description' => 'File Transfer',
            //'Content-Transfer-Encoding' => 'binary'
            ),
            'callbacks' => array(
                'init' => 'initXlsContext',
                'post' => 'postXlsContext'
        )));
    }

    /**
     * JSON context extra initialization
     *
     * Turns off viewRenderer auto-rendering
     *
     * @return void
     */
    public function initXlsContext() {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        if ($view instanceof Zend_View_Interface) {
            //$viewRenderer->setNoRender(true);
        }
    }

    /**
     * JSON post processing
     *
     * JSON serialize view variables to response body
     *
     * @return void
     */
    public function postXlsContext() {
        error_reporting(E_ERROR);
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $view = $viewRenderer->view;
        if ($view instanceof Zend_View_Interface) {
            /**
             * @see Zend_Json
             */
            if (method_exists($view, 'getVars')) {
                $vars = $view->getVars();
                
                $script = $viewRenderer->getViewScript();
                require_once $view->getScriptPath($script);
                
                if (!$viewRenderer->getNoRender()) {
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment; filename=' . ($vars['filename'] ? ($vars['filepath'] ? $vars['filepath'] : '') . $vars['filename'] : 'documents.xlsx'));
                }
                
                //$objWriter->save('some_excel_file.xlsx');
                //$result = $_SERVER['DOCUMENT_ROOT'] . '/some_excel_file.xlsx';
                //header('Content-Description: File Transfer');
                //header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                //header('Content-Disposition: attachment; filename=some_excel_file.xlsx');
                
                //header('Content-Transfer-Encoding: binary');
                //header('Expires: 0');
                //header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
                //header('Pragma: public');
                //header('Content-Length: ' . filesize($result));
                /*ob_clean();
                flush();
                readfile($result);*/
                exit;
            } else {
                require_once 'Zend/Controller/Action/Exception.php';
                throw new Zend_Controller_Action_Exception('View does not implement the getVars() method needed to encode the view into JSON');
            }
        }
    }

}
