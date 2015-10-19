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