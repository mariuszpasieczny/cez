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
                ->addActionContext('list', array('html', 'xls'))
                ->addActionContext('accept', 'html')
                ->addActionContext('send', 'html')
                ->setSuffix('html', '')
                ->initContext();

        if ($context->getCurrentContext() == 'xls') {
            $this->_returns->setItemCountPerPage(null);
        }
    }

    public function indexAction() {
        $this->_forward('list');
    }

    public function listAction() {
        // action body

        $request = $this->getRequest();
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_returns->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        $columns = array('name', 'catalogname', 'demaged', 'demagecodeacronym', 'dateadd', 'service', 'clientnumber', 'client', 'technician', 'waybill', 'status');
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_returns->setOrderBy("{$columns[$orderBy[0] - 1]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_returns->getOrderBy());
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $ix++;
            $orderBy = "$ix {$orderBy[1]}";
        }
        $request->setParam('orderBy', $orderBy);
        $request->setParam('count', $this->_returns->getItemCountPerPage());
        $this->view->filepath = '/../data/temp/';
        $this->view->filename = 'Zestawienie_zwrotow-' . date('YmdHis') . '.xlsx';
        
        $this->_returns->setLazyLoading(false);
        $this->view->returns = $this->_returns->getAll($request->getParams());
        $this->view->paginator = $this->_returns->getPaginator();
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        $params['statusid'] = $status->id;
        $params['role'] = 'technician';
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll($params);
        $this->view->technicians = $technicians;
        $this->view->request = $request->getParams();
        $this->view->statuses = $this->_dictionaries->getStatusList('returns');
    }
    
    public function acceptAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $product = $this->_returns->get($id);
        if (!$product) {
            throw new Exception('Nie znaleziono zwrotu');
        }
        $this->view->productsCount = $product->count();
        $form = new  Application_Form_Returns_Accept(array('productsCount' => $product->count()));
        $form->setProducts($product);
        $status = $this->_dictionaries->getStatusList('returns')->find('accepted', 'acronym');
        $this->view->form = $form;
        $this->view->product = $product;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                if (!$product) {
                    $form->setDescription('Nie zaznaczono zwrotów do przyjęcia');
                    return;
                }
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($product as $i => $item) {
                    if (!$item->isNew()) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nie można przyjąć zwrotu'));
                        return;
                    }
                    $item->statusid = $status->id;
                    $item->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zwrot zaakceptowany';
            }
        }
    }
    
    public function sendAction() {
        $request = $this->getRequest();
        $id = (array) $request->getParam('id');
        $typeid = $request->getParam('typeid');
        //$id = array_unique((array)$id);
        $product = $this->_returns->get($id);
        if (!$product) {
            throw new Exception('Nie znaleziono zwrotu');
        }
        $this->view->productsCount = $product->count();
        $form = new  Application_Form_Returns_Send(array('productsCount' => $product->count()));
        $form->setProducts($product);
        $status = $this->_dictionaries->getStatusList('returns')->find('sent', 'acronym');
        $this->view->form = $form;
        $this->view->product = $product;

        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                if (!$product) {
                    $form->setDescription('Nie zaznaczono zwrotów do wysłania');
                    return;
                }
                Zend_Db_Table::getDefaultAdapter()->beginTransaction();
                foreach ($product as $i => $item) {
                    if (!$item->isConfirmed()) {
                        $form->getElement('id-' . $i)->setErrors(array('id-' . $i => 'Nie można wysłać zwrotu'));
                        return;
                    }
                    $item->statusid = $status->id;
                    $item->waybill = $values['waybill'];
                    $item->save();
                }
                Zend_Db_Table::getDefaultAdapter()->commit();
                $this->view->success = 'Zwrot wysłany';
            }
        }
    }
    
}