<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Statistics_Servicecodes_Table extends Application_Db_Table
{
    protected $_name = 'servicecodesstatistics';// table name
    protected $_primary = 'technicianid'; // primary column name
    protected $_rowClass = 'Application_Model_Services_Statistics_Servicecodes_Row';
    
    protected $_dependentTables = array('Application_Model_Services_Table');
    
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
        return $fields;
    }
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        $select = parent::select()
            ->setIntegrityCheck(false)
            ->from('servicecodesview', array('technicianid', new Zend_Db_Expr("IF( ISNULL(  `servicecodesview`.`technicianid` ) ,  'nieprzypisane',  `servicecodesview`.`technician` )"),
                'technician' => 
                'attributeacronym',
                'codeacronym',
                'quantity' => new Zend_Db_Expr("COUNT(  `servicecodesview`.`id` )")))
                ->group(array('technicianid', 'technician', 'attributeacronym', 'codeacronym'));
        return $select;
    }
    
    public function getAll($params = array(), $rows = null, $root = null) {
        if (!empty($params['planneddatefrom'])) {
            $this->setWhere($this->getAdapter()->quoteInto("planneddate >= ?", $params['planneddatefrom']));
        }
        if (!empty($params['planneddatetill'])) {
            $this->setWhere($this->getAdapter()->quoteInto("planneddate <= ?", $params['planneddatetill']));
        }
        return parent::getAll($params, $rows, $root);
    }
    
}