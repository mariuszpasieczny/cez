<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Products_Table extends Application_Db_Table
{
    protected $_name = 'products';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Products_Row';
    
    protected $_referenceMap = array(
        'Warehouse' => array(
            'columns' => 'warehouseid',
            'refTableClass' => 'Application_Model_Warehouses_Table',
            'refColumns' => 'id'
        ),
        'Unit' => array(
            'columns' => 'unitid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Status' => array(
            'columns' => 'statusid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        )
    );
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        $select = parent::select()
            ->setIntegrityCheck(false)
            ->from('productsview');
        return $select;
    }
    
    public function getAll($params = array(), $rows = null, $root = null) {
        if (!empty($params['qtyavailable'])) {
            $this->setWhere($this->getAdapter()->quoteInto("qtyavailable >= ?", $params['qtyavailable']));
            unset($params['qtyavailable']);
        }
        if (!empty($params['name'])) {
            $this->setWhere($this->getAdapter()->quoteInto("name LIKE ?", "%{$params['name']}%"));
            unset($params['name']);
        }
        if (!empty($params['serial'])) {
            if (strpos($params['serial'], ',') !== false) {
                $serials = explode(',', $params['serial']);
            }
            if (strpos($params['serial'], "\r\n") !== false) {
                $serials = explode("\r\n", $params['serial']);
            }
            $trim = function ($item) {return trim($item, "\r\n,");};
            $serials = array_map($trim, $serials);
            if (!empty($serials)) {
                if (sizeof($serials) > 30) {
                    //throw new Exception('Zbyt wiele numerów seryjnych');
                }
                $this->setWhere($this->getAdapter()->quoteInto("serial IN (?)", $serials));
            } else {
                $this->setWhere($this->getAdapter()->quoteInto("serial LIKE ?", "%{$params['serial']}%"));
            }
            unset($params['serial']);
        }
        return parent::getAll($params, $rows, $root);
    }
    
}