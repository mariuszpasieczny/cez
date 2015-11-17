<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Warehouse_ReportsController extends Application_Controller_Abstract {

    protected $_orders;

    public function init() {
        $this->_users = new Application_Model_Users_Table();
        $this->_products = new Application_Model_Products_Table();
        $this->_products->setItemCountPerPage(null/*$this->_hasParam('count') ? $this->_getParam('count') : Application_Db_Table::ITEMS_PER_PAGE*/);
        $this->_products->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'name DESC');

        parent::init();

        $context = $this->_helper->getHelper('xlsContext');
        $context->addActionContext('index', 'html')
                ->addActionContext('order-lines', array('html', 'xls'))
                ->setSuffix('html', '')
                ->initContext();

        if ($context->getCurrentContext() == 'xls') {
            $this->_products->setItemCountPerPage(null);
        }
    }

    public function indexAction() {
        $this->_forward('order-lines');
    }

    public function orderLinesAction() {
        // action body

        $request = $this->getRequest();
        $warehouseid = $request->getParam('warehouseid');
        if ($warehouseid) {
            $warehouse = $this->_warehouses->get($warehouseid);
            $this->view->warehouse = $warehouse;
        }
        $pageNumber = $request->getParam('page');
        if ($pageNumber) {
            $this->_products->setPageNumber($pageNumber);
        }
        $orderBy = $request->getParam('orderBy');
        /*$columns = array('number', 'servicetype', 'client', 'technician', 'planneddate', 'status');
        if ($orderBy) {
            $orderBy = explode(" ", $orderBy);
            $this->_servicereports->setOrderBy("{$columns[$orderBy[0]]} {$orderBy[1]}");
        }
        $orderBy = explode(" ", $this->_servicereports->getOrderBy()); //var_dump($orderBy);
        foreach ($columns as $ix => $columnName) {
            if ($columnName != $orderBy[0]) {
                continue;
            }
            $orderBy = "$ix {$orderBy[1]}";
        }*/
        $this->view->request['orderBy'] = $orderBy;
        $status = $this->_dictionaries->getStatusList('users')->find('active', 'acronym');
        $params['statusid'] = $status->id;
        $params['role'] = 'technician';
        if ($this->_auth->getIdentity()->role == 'technician') {
            $params['id'] = $this->_auth->getIdentity()->id;
        }
        $this->_users->setOrderBy(array('lastname','firstname'));
        $technicians = $this->_users->getAll($params);
        $this->view->technicians = $technicians;
        $this->view->statuses = $this->_dictionaries->getStatusList('products');
        $params = array_filter(array_intersect_key($request->getParams(), array_flip(array('technicianid', 'name', 'serial', 'releasedatefrom', 'releasedatetill'))));
        if (empty($params)) {
            $releasedatefrom = date('Y-m-01');
            $releasedatetill = date('Y-m-d');
            $request->setParam('releasedatefrom', $releasedatefrom);
            $request->setParam('releasedatetill', $releasedatetill);
        }
        $this->_products->setLazyLoading(false);
        $statusDeleted = $this->_dictionaries->getStatusList('products', 'deleted')->current();
        $statusReturned = $this->_dictionaries->getStatusList('orders', 'returned')->find('returned','acronym');
        $this->view->products = $this->_products->select()
                ->where($this->_products->getAdapter()->quoteInto("statusid != ?", $statusDeleted->id))
                ->group(array('name','unitacronym'))->query()->fetchAll();
        $this->view->request = $request->getParams();
        $this->view->filepath = '/../data/temp/';
        $this->view->filename = 'Raport_wydan-' . date('YmdHis') . '.xls';
        $this->view->rowNo = 1;
        if ($this->_auth->getIdentity()->role == 'technician') {
            $request->setParam('technicianid', $this->_auth->getIdentity()->id);
        }
        
        if (!$this->getRequest()->isPost()&&0) {
            return;
        }
        
        $model = new Application_Model_Products_Statistics_Orderlines_Table();
        $model->setLazyLoading(false);
        $model->setItemCountPerPage(null);
        $model->setOrderBy($this->_hasParam('orderBy') ? $this->_getParam('orderBy') : 'technicianid');
        $model->setWhere($this->_products->getAdapter()->quoteInto("statusid != ?", $statusReturned->id));

        $reports = array();
        foreach ($model->getAll($request->getParams()) as $stats) {
            $reports[$stats['product']][$stats['statusacronym']] += $stats['quantity'];
            $reports[$stats['technicianid']][$stats['product']] += $stats['quantity'];
        }
        $this->view->reports = $reports;
    }

}
