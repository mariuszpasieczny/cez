<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/controllers/IndexController.php

class FilesController extends Application_Controller_Abstract {

    public function init() {
        /* Initialize action controller here */
        parent::init();

        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
                ->setSuffix('html', '')
                ->initContext();
    }

    public function listAction() {
        $this->_forward('index');
    }

    public function indexAction() {
        // action body
    }

    public function downloadAction() {
        $request = $this->getRequest();
        $file = base64_decode($request->getParam('file'));
        if (!file_exists($_SERVER['DOCUMENT_ROOT'] . $file)) {
            throw new Exception('Nie znaleziono pliku');
        }
        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary");
        header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
        readfile($_SERVER['DOCUMENT_ROOT'] . $file);
        exit;
    }

}
