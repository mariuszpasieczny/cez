<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Warehouse_ReturnsController extends Application_Controller_Abstract {

    protected $_orders;

    public function init() {
        /* Initialize action controller here */
        $this->_warehouses = new Application_Model_Warehouses_Table();
        $this->_returns = new Application_Model_Returns_Table();
        $this->_returns->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_returns->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'dateadd DESC');
        $this->_users = new Application_Model_Users_Table();
        parent::init();

        $context = $this->_helper->getHelper('xlsContext');
        $context->addActionContext('index', 'html')
                ->addActionContext('list', array('html', 'json', 'xls'))
                ->addActionContext('confirm', 'html')
                ->setSuffix('html', '')
                ->initContext();

        if ($context->getCurrentContext() == 'xls') {
            $this->_returns->setItemCountPerPage(null);
        }
    }

    public function listAction() {
        $this->_forward('index');
    }

    public function indexAction() {
        // action body

        $request = $this->getRequest();
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_returns->setPageNumber($pageNumber);
        }
        $this->_returns->setLazyLoading(false);
        $this->view->returns = $this->_returns->getAll();
        $this->view->paginator = $this->_returns->getPaginator();
    }
    
}