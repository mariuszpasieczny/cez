<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/controllers/ErrorController.php
 
class ErrorController extends Application_Controller_Abstract
{
    
    public function init()
    {
        /* Initialize action controller here */
        parent::init();
        
        $ajaxContext = $this->_helper->getHelper('xlsContext');
        $ajaxContext->addActionContext('error', array('html','xls'))
            ->addActionContext('noauth', array('html','xls'))
            ->setSuffix('html', '')
            ->initContext();
    }
 
    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $log = $bootstrap->getResource('log');
        $log->log($errors->exception->getMessage(), Zend_Log::ERR);
        $log->log($errors->exception->getTraceAsString(), Zend_Log::DEBUG);
        $log->log($errors->request->getParams(), Zend_Log::DEBUG);
 
        switch ($errors->type) {
            //case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
 
                // 404 error -- controller or action not found
                //$this->getResponse()->setHttpResponseCode(404);
                $this->view->statusCode = 404;
                $this->view->message = 'Page not found';
                break;
            default:
                // application error
                //$this->getResponse()->setHttpResponseCode(500);
                $this->view->statusCode = 500;
                $this->view->message = 'Application error';
                break;
        }
 
        $this->view->exception = $errors->exception;
        $this->view->request   = $errors->request;
    }
    
    public function noauthAction()
    {
        $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $log = $bootstrap->getResource('log');
        $log->log('Forbidden', Zend_Log::ERR);
        $log->log($this->_getAllParams(), Zend_Log::DEBUG);
        
        //$this->view->statusCode = 403;
        $this->view->message = 'Forbidden';
        $this->view->request   = $this->getRequest();
    }
}