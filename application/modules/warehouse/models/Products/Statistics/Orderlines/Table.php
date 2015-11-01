<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Products_Statistics_Orderlines_Table extends Application_Db_Table
{
    protected $_name = 'productsstatistics';// table name
    protected $_primary = 'technicianid'; // primary column name
    protected $_rowClass = 'Application_Model_Products_Statistics_Orderlines_Row';
    
    protected $_dependentTables = array('Application_Model_Products_Table');
    
    protected $_referenceMap = array(
        'Warehouse' => array(
            'columns' => 'warehouseid',
            'refTableClass' => 'Application_Model_Warehouses_Table',
            'refColumns' => 'id'
        ),
        'Type' => array(
            'columns' => 'typeid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Technician' => array(
            'columns' => 'technicianid',
            'refTableClass' => 'Application_Model_Users_Table',
            'refColumns' => 'id'
        )
    );
    
    public function getSearchFields() {
        $fields = parent::getSearchFields();
        $fields[] = 'statusid';
        $fields[] = 'attributeacronym';
        return $fields;
    }
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        $select = parent::select()
            ->setIntegrityCheck(false)
            ->from('orderlinesview', array('technicianid', new Zend_Db_Expr("IF( ISNULL(  `orderlinesview`.`technicianid` ) ,  'nieprzypisane',  `orderlinesview`.`technician` )"),
                'product',
                'serial',
                'statusacronym',
                'status',
                'quantity' => new Zend_Db_Expr("COUNT(  `orderlinesview`.`id` )")))
                ->group(array('technicianid', 'technician', 'product', 'serial', 'statusacronym', 'status'));
        return $select;
    }
    
    public function getAll($params = array(), $rows = null, $root = null) {
        if (!empty($params['releasedatefrom'])) {
            $this->setWhere($this->getAdapter()->quoteInto("DATE_FORMAT(dateadd, '%Y-%m-%d') >= ?", $params['releasedatefrom']));
        }
        if (!empty($params['releasedatetill'])) {
            $this->setWhere($this->getAdapter()->quoteInto("DATE_FORMAT(dateadd, '%Y-%m-%d') <= ?", $params['releasedatetill']));
        }
        return parent::getAll($params, $rows, $root);
    }
    
}