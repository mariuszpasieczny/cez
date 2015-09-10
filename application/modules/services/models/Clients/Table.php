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
    
    public function getAllStreets() {
        $select = parent::select();
        $rows = $select->from(array('c' => 'clients'), 'street')->distinct()->order(new Zend_Db_Expr('street COLLATE utf8_polish_ci'))->query()->fetchAll();
        
        $data = array(
            'table' => $this,
            'data' => $rows,
            'readOnly' => $select->isReadOnly(),
            'rowClass' => $this->_rowClass,
            'stored' => true
        );

        if (!class_exists($this->_rowsetClass)) {
            require_once 'Zend/Loader.php';
            Zend_Loader::loadClass($this->_rowsetClass);
        }
        return new $this->_rowsetClass($data);
    }
    
}