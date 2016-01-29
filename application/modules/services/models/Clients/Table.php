<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Clients_Table extends Application_Db_Table
{
    protected $_name = 'clients';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Clients_Row';
    
    protected $_dependentTables = array('Application_Model_Orders_Table');
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        return parent::select()->setIntegrityCheck(false)->from(($this->_schema ? ($this->_schema . '.') : '') . 'clientsview');
    }
    
    public function getAllStreets() {
        $select = $this->getAdapter()->select();
        $select = $select->from(array('c' => $this->_lazyLoading === true ? 'clients' : 'clientsview'), 'street')->distinct()->order(new Zend_Db_Expr('street COLLATE utf8_polish_ci'));
        return $rows = $select->query()->fetchAll();
        
        $data = array(
            'table' => $this,
            'data' => $rows,
            'readOnly' => true,
            'rowClass' => $this->_rowClass,
            'stored' => true
        );

        if (!class_exists($this->_rowsetClass)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($this->_rowsetClass);
        }
        return new $this->_rowsetClass($data);
    }
    
    public function getAllCities() {
        $select = $this->getAdapter()->select();
        $select = $select->from(array('c' => $this->_lazyLoading === true ? 'clients' : 'clientsview'), 'city')->distinct()->order(new Zend_Db_Expr('city COLLATE utf8_polish_ci'));
        return $rows = $select->query()->fetchAll();
    }
    
}