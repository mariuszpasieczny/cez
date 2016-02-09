<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Table extends Application_Db_Table
{
    protected $_name = 'services';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Services_Row';
    
    protected $_dependentTables = array('Application_Model_Services_Products_Table', 'Application_Model_Services_Codes_Table');
    
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
        'ServiceType' => array(
            'columns' => 'servicetypeid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Status' => array(
            'columns' => 'statusid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Calendar' => array(
            'columns' => 'calendarid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'System' => array(
            'columns' => 'systemid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Region' => array(
            'columns' => 'regionid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Laborcode' => array(
            'columns' => 'laborcodeid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Blockadecode' => array(
            'columns' => 'blockadecodeid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Complaintcode' => array(
            'columns' => 'complaintcodeid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Area' => array(
            'columns' => 'areaid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Client' => array(
            'columns' => 'clientid',
            'refTableClass' => 'Application_Model_Clients_Table',
            'refColumns' => 'id'
        ),
        'Technician' => array(
            'columns' => 'technicianid',
            'refTableClass' => 'Application_Model_Users_Table',
            'refColumns' => 'id'
        )
    );
    
    public function init($config = array()) {
        //$this->setCacheInClass(true);
        //$this->_setCache(Application_Db_Table::getDefaultCache());
    }
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        $select = parent::select()->setIntegrityCheck(false)
                ->from(array('s' => 'servicesview'),
                        array('*',
                            'installationcode' => new Zend_Db_Expr('(select GROUP_CONCAT(codeacronym order by codeacronym SEPARATOR \', \') from servicecodesview where serviceid = s.id and attributeacronym = \'installationcode\')'),
                            'installationcancelcode' => new Zend_Db_Expr('(select GROUP_CONCAT(codeacronym order by codeacronym SEPARATOR \', \') from servicecodesview where serviceid = s.id and attributeacronym = \'installationcancelcode\')'),
                            'solutioncode' => new Zend_Db_Expr('(select GROUP_CONCAT(codeacronym order by codeacronym SEPARATOR \', \') from servicecodesview where serviceid = s.id and attributeacronym = \'solutioncode\')'),
                            'cancellationcode' => new Zend_Db_Expr('(select GROUP_CONCAT(codeacronym order by codeacronym SEPARATOR \', \') from servicecodesview where serviceid = s.id and attributeacronym = \'cancellationcode\')'),
                            'modeminterchangecode' => new Zend_Db_Expr('(select GROUP_CONCAT(codeacronym order by codeacronym SEPARATOR \', \') from servicecodesview where serviceid = s.id and attributeacronym = \'modeminterchangecode\')'),
                            'decoderinterchangecode' => new Zend_Db_Expr('(select GROUP_CONCAT(codeacronym order by codeacronym SEPARATOR \', \') from servicecodesview where serviceid = s.id and attributeacronym = \'decoderinterchangecode\')')));
        return $select;
    }
    
    public function getSearchFields() {
        $fields = parent::getSearchFields();
        $fields[] = 'technician';
        $fields[] = 'status';
        $fields[] = 'clientnumber';
        $fields[] = 'clientstreet';
        $fields[] = 'clientcity';
        $fields[] = 'clientstreetno';
        $fields[] = 'clientapartment';
        $fields[] = 'calendar';
        $fields[] = 'servicetype';
        $fields[] = 'region';
        return $fields;
    }
    
    public function getAll($params = array(), $rows = null, $root = null) {
        if (!empty($params['planneddatefrom'])) {
            $this->setWhere($this->getAdapter()->quoteInto("DATE_FORMAT(CONCAT(planneddate, ' ', timefrom), '%Y-%m-%d %H:%M') >= ?", $params['planneddatefrom']));
        }
        if (!empty($params['planneddatetill'])) {
            $this->setWhere($this->getAdapter()->quoteInto("DATE_FORMAT(CONCAT(planneddate, ' ', timefrom), '%Y-%m-%d %H:%M') <= ?", $params['planneddatetill']));
        }
        if (!empty($params['client'])) {
            $this->setWhere($this->getAdapter()->quoteInto("UPPER(client) LIKE UPPER(?)", "%{$params['client']}%"));
        }
        if (!empty($params['clientaddress'])) {
            $this->setWhere($this->getAdapter()->quoteInto("UPPER(client) LIKE UPPER(?)", "{$params['clientaddress']}%"));
        }
        return parent::getAll($params, $rows, $root);
    }
    
    public function getCalendarList() {
        $select = $this->getAdapter()->select();
        $select = $select->from(array('s' => $this->_lazyLoading === true ? 'services' : 'servicesview'), array('acronym' => 'calendar'))->distinct()->order('calendar');
        return $rows = $select->query()->fetchAll();
    }
    
    public function getServicetypeList() {
        $select = $this->getAdapter()->select();
        $select = $select->from(array('s' => $this->_lazyLoading === true ? 'services' : 'servicesview'), array('acronym' => 'servicetype'))->distinct()->order('servicetype');
        return $rows = $select->query()->fetchAll();
    }
    
    public function getRegionsList() {
        $select = $this->getAdapter()->select();
        $select = $select->from(array('s' => $this->_lazyLoading === true ? 'services' : 'servicesview'), array('acronym' => 'region'))->distinct()->order('region');
        return $rows = $select->query()->fetchAll();
    }
    
}