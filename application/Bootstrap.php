<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/Bootstrap.php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {

    public function __construct($application) {
        parent::__construct($application);

        // handle PHP errors
        //Application_ErrorHandler::set();
    }

    protected function _initDoctype() {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
    }

    /* public function _initAcl()
      {
      $acl = new Zend_Acl();
      $auth = Zend_Auth::getInstance();
      $front = Zend_Controller_Front::getInstance();
      $front->registerPlugin(new Application_Controller_Plugin_Acl($auth, $acl));

      } */

    /* protected function _initView()
      {
      // Initialize view
      $view = new Zend_View();
      // Add it to the ViewRenderer
      $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper(
      'ViewRenderer'
      );
      $view->addHelperPath('Application/View/Helper', 'Application_View_Helper');
      $viewRenderer->setView($view);

      // Return it, so that it can be stored by the bootstrap
      return $view;
      } */

    protected function _initPage() {
        $this->bootstrap(array(
            'layout',
            'view',
            'frontController',
        ));

        $front = $this->getResource('frontController');
        $layout = $this->getResource('layout');
        $view = $this->getResource('view');

        $request = new Zend_Controller_Request_Http();
        $front->setRequest($request);
        $baseUrl = $request->getBasePath();

        $defaultsArray = array(
            'page' => array(
                'title' => array(
                    'separator' => '',
                    'content' => '',
                    'defaultAttachOrder' => 'APPEND',
                ),
                'css' => array(),
                'js' => array(),
                'keywords' => false,
                'description' => false,
                'extension' => 'phtml',
            )
        );
        $defaults = new Zend_Config($defaultsArray, true);

        $cfg = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini', 'production');
        $cfg = $defaults->merge($cfg);


        $view->headTitle()
                //->setDefaultAttachOrder($cfg->page->title->defaultAttachOrder)
                //->setSeparator($cfg->page->title->separator)
                ->headTitle($cfg->page->title->content);

        foreach ($cfg->page->css as $css) {
            if (isset($css->media)) {
                $view->headLink()->appendStylesheet($baseUrl . $css->href, $css->media);
            } else {
                $view->headLink()->appendStylesheet($baseUrl . $css->href);
            }
        }

        foreach ($cfg->page->js as $js) {
            $view->headScript()->appendFile(
                    $baseUrl . $js, 'text/javascript'
            );
        }

        if ($cfg->page->keywords) {
            $view->headMeta()->appendName('keywords', $cfg->page->keywords);
        }

        if ($cfg->page->description) {
            $view->headMeta()->appendName('description', $cfg->page->description);
        }

        if ($cfg->page->extension != 'phtml') {
            $layout->setViewSuffix($cfg->page->extension);
            $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
            $viewRenderer->setViewSuffix($cfg->page->extension);
            $viewRenderer->setView($view);
        }
    }

    protected function _initAutoload() {
        $autoloader = new Zend_Application_Module_Autoloader(array(
            'namespace' => 'Application',
            'basePath' => APPLICATION_PATH . '/modules/default',
            'resourceTypes' => array(
                'form' => array(
                    'path' => 'forms',
                    'namespace' => 'Form',
                ),
                'model' => array(
                    'path' => 'models',
                    'namespace' => 'Model',
                ),
            )
        ));
        return $autoloader;
    }

    protected function _initCache() {
        try {
            $bootstrapCacheMgr = $this->bootstrap('cachemanager');
        } catch (Zend_Application_Bootstrap_Exception $e) {
            // log error...
        }

        if (!empty($bootstrapCacheMgr) && $bootstrapCacheMgr instanceof
                Zend_Application_Bootstrap_BootstrapAbstract &&
                $bootstrapCacheMgr->hasResource('cachemanager')) {

            //
            // Get a handle on the existing cache manager
            //
            $cacheManager = $bootstrapCacheMgr->getResource('cachemanager');
            $generalCache = 'general';

            if ($cacheManager->hasCache($generalCache)) {
                $cache = $cacheManager->getCache($generalCache);
                // Only attempt to cache the metadata if we have a cache available
                if (!empty($cache)) {
                    try {
                        Zend_Registry::set('cache', $cache);
                        return $cache;
                    } catch (Zend_Db_Table_Exception $e) {
                        // log error...
                    }
                }
            }

            $dbMetadataCache = 'dbMetadataCache';
            if ($cacheManager->hasCache($dbMetadataCache)) {
                $cache = $cacheManager->getCache($dbMetadataCache);
                // Only attempt to cache the metadata if we have a cache available
                if (!empty($cache)) {
                    try {
                        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
                        Application_Db_Table::setDefaultCache($cache);
                        return $cache;
                    } catch (Zend_Db_Table_Exception $e) {
                        // log error...
                    }
                }
            }
        }
    }
    
    public function ___initDatabase() {
        $db = $this->bootstrap('db')->getResource('db');
        $db->query("SET NAMES 'utf8'");
        $db->query("SET CHARACTER SET 'utf8'");
    }

}
