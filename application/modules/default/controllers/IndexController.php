<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/controllers/IndexController.php

class IndexController extends Application_Controller_Abstract {

    public function init() {
        /* Initialize action controller here */
        parent::init();

        $this->_products = new Application_Model_Products_Statistics_Table();
        $this->_products->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : array('statusid'));
        $this->_services = new Application_Model_Services_Statistics_Table();
        $this->_services->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : array('statusid'));

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
        $this->view->services = $this->_services->getAll(array('technicianid' => $this->_auth->getIdentity()->id)); //var_dump($this->view->services);exit;
        if ($this->_auth->getIdentity()->role == 'technician')
            $this->view->products = $this->_products->getAll(array('technicianid' => $this->_auth->getIdentity()->id)); //var_dump($this->view->products);exit;
        else
            $this->view->products = $this->_products->getAll(array('userid' => $this->_auth->getIdentity()->id)); //var_dump($this->view->products);exit;
    }

}
