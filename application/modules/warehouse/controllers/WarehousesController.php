<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Warehouse_WarehousesController extends Application_Controller_Abstract
{
    
    protected $_warehouses;
    
    protected $_dictionaries;
 
    public function init()
    {
        /* Initialize action controller here */
        $this->_warehouses = new Application_Model_Warehouses_Table();
        $this->_products = new Application_Model_Products_Table();
        $this->_warehouses->setItemCountPerPage($this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE);
        $this->_warehouses->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'name DESC');
        parent::init();
        
        $ajaxContext = $this->_helper->getHelper('AjaxContext');
        $ajaxContext->addActionContext('index', 'html')
            ->addActionContext('edit', 'html')
            ->setSuffix('html', '')
            ->initContext();
    }
    
    public function listAction()
    {
        $this->_forward('index');
    }
 
    public function indexAction()
    {
        // action body
        
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        
        $request = $this->getRequest();
        $parentid = $request->getParam('parentid');
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_warehouses->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        /*$columns = array('type', 'area', 'name');
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_warehouses->setOrderBy("{$columns[$orderBy[0]]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_warehouses->getOrderBy());
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $orderBy = "$ix {$orderBy[1]}";
        }*/
        $request->setParam('orderBy', $orderBy);
        $request->setParam('count', $this->_warehouses->getItemCountPerPage());
        if ($parentid) {
            $dictionary = $this->_warehouses->get($parentid);
            $this->view->parent = $dictionary;
        }
        $this->view->warehouses = $this->_warehouses->getAll();
        $this->view->paginator = $this->_warehouses->getPaginator();
        $this->view->request = $request->getParams();
    }
    
    public function editAction()
    {
        // action body
        
        $request = $this->getRequest();
        $id = $request->getParam('id');
        $form    = new Application_Form_Warehouses();
        $parents = $this->_warehouses->getAll();
        $types = $this->_dictionaries->getDictionaryList('warehouse', 'type');
        $areas = $this->_dictionaries->getDictionaryList('warehouse', 'area');
        $form->setOptions(array('parents' => $parents, 'types' => $types, 'areas' => $areas));
        if ($id) {
            $warehouse = $this->_warehouses->get($id);
            $form->setDefaults($warehouse->toArray());
        }
        $this->view->form = $form;
 
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($request->getPost())) {
                $values = $form->getValues();
                if ($id) {
                    $warehouse->setFromArray($values);
                } else {
                    $warehouse = $this->_warehouses->createRow($values);
                    $warehouse->id = null;
                }
                $warehouse->save();
                $this->view->success = 'Warehouse successfully saved';
            }
        }
    }  
    
}