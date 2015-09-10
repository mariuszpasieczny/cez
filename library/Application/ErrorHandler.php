<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_ErrorHandler {

    public static function handle() {

        if (!$error = error_get_last()) {
            return;
        }
        $wasFatal = ($error && ($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR));
        if (!$wasFatal) {
            $bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
            $log = $bootstrap->getResource('log');
            $log->log($error['message'], Zend_Log::WARN);
            $log->log(print_r($error, 1), Zend_Log::DEBUG);
            return;
        }

        $frontController = Zend_Controller_Front::getInstance();
        $request = $frontController->getRequest();
        $response = $frontController->GETRESPONSE();

        $response->setException(new Exception("Fatal error: $error[message] at $error[file]:$error[line]", $error['type']));

        // this clean what is before the error
        ob_end_clean();

        $frontController->dispatch($request, $response);

        /* $error = error_get_last();
          $wasFatal = ($error && ($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR));
          if ($wasFatal) {
          $frontController = Zend_Controller_Front::getInstance();
          $errorHandler = $frontController->getPlugin('Zend_Controller_Plugin_ErrorHandler');
          $request = $frontController->getRequest();
          $response = $frontController->getResponse();

          // Add the fatal exception to the response in a format that ErrorHandler will understand
          $response->setException(new Exception(
          "Fatal error: $error[message] at $error[file]:$error[line]", $error['type']));

          // Call ErrorHandler->_handleError which will forward to the Error controller
          $handleErrorMethod = new ReflectionMethod('Zend_Controller_Plugin_ErrorHandler', '_handleError');
          $handleErrorMethod->setAccessible(true);
          $handleErrorMethod->invoke($errorHandler, $request);

          // Discard any view output from before the fatal
          ob_end_clean();

          // Now display the error controller:
          $frontController->dispatch($request, $response);
          } */
    }

    public static function set() {
        register_shutdown_function(array(__CLASS__, 'handle'));
    }

}
