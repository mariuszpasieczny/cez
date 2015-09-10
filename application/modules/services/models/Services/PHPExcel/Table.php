<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_PHPExcel_Table extends Application_Db_Table_PHPExcel_Table {
    
    //protected $_rowClass = 'Application_Model_Services_PHPExcel_Row';
    
    /**
     * Initialize primary key from metadata.
     * If $_primary is not defined, discover primary keys
     * from the information returned by describeTable().
     *
     * @return void
     * @throws Zend_Db_Table_Exception
     */
    protected function _setupPrimaryKey()
    {}
    
    public function getAll()
    {
        $result = $this->getAdapter()->fetchAll();
        $data = array(
            'table' => $this,
            'data' => $result->getRowIterator(3),
            'count' => $result->getHighestRow(),
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
    
}