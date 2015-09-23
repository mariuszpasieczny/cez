<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Returns_Table extends Application_Db_Table
{
    protected $_name = 'servicereturns';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Returns_Row';
    protected $_rowsetClass = 'Application_Db_Table_Rowset';
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        $select = parent::select()
            ->setIntegrityCheck(false)
            ->from('servicereturnsview');
            //->join('products', 'orderlines.productid = products.id', array('name', 'serial', 'acronym'));
        //echo$select;exit;
        return $select;
    }
    
    protected $_referenceMap = array(
        'Unit' => array(
            'columns' => 'unitid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Service' => array(
            'columns' => 'serviceid',
            'refTableClass' => 'Application_Model_Services_Table',
            'refColumns' => 'id'
        )
    );
    
    public function getSearchFields() {
        $fields = parent::getSearchFields();
        $fields[] = 'technicianid';
        $fields[] = 'technician';
        $fields[] = 'status';
        $fields[] = 'clientnumber';
        $fields[] = 'client';
        $fields[] = 'service';
        return $fields;
    }
    
    public function getAll($params = array(), $rows = null, $root = null) {
        if (!empty($params['name'])) {
            if (strpos($params['name'], ',') !== false) {
                $names = explode(',', $params['name']);
            }
            if (strpos($params['name'], "\r\n") !== false) {
                $names = explode("\r\n", $params['name']);
            }
            $trim = function ($item) {return trim($item, "\r\n,");};
            $names = array_map($trim, $names);
            if (!empty($names)) {
                if (sizeof($names) > 30) {
                    //throw new Exception('Zbyt wiele numerów seryjnych');
                }
                $this->setWhere($this->getAdapter()->quoteInto("name IN (?)", $names));
            } else {
                $this->setWhere($this->getAdapter()->quoteInto("name LIKE ?", "%{$params['name']}%"));
            }
            unset($params['name']);
        }
        return parent::getAll($params, $rows, $root);
    }
    
}