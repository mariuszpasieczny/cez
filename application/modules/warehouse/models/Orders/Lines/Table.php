<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Orders_Lines_Table extends Application_Db_Table
{
    protected $_name = 'orderlines';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Orders_Lines_Row';
    protected $_rowsetClass = 'Application_Db_Table_Rowset';
    
    public function getSearchFields() {
        $fields = parent::getSearchFields();
        $fields[] = 'orderlines.statusid';
        $fields[] = 'warehouseid';
        $fields[] = 'product';
        $fields[] = 'serial';
        return $fields;
    }
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        $select = parent::select()
            ->setIntegrityCheck(false)
            ->from(($this->_schema ? ($this->_schema . '.') : '') . 'orderlinesview');
            //->join('products', 'orderlines.productid = products.id', array('name', 'serial', 'acronym'));
        //echo$select;exit;
        return $select;
    }
    
    public function getAll($params = array(), $rows = null, $root = null) {
        if (!empty($params['datefrom'])) {
            $this->setWhere($this->getAdapter()->quoteInto("DATE_FORMAT(releasedate, '%Y-%m-%d') >= ?", $params['datefrom']));
            unset($params['datefrom']);
        }
        if (!empty($params['datetill'])) {
            $this->setWhere($this->getAdapter()->quoteInto("DATE_FORMAT(releasedate, '%Y-%m-%d') <= ?", $params['datetill']));
            unset($params['datetill']);
        }
        if (!empty($params['product'])) {
            $this->setWhere($this->getAdapter()->quoteInto("product LIKE ?", "%{$params['product']}%"));
            unset($params['product']);
        }
        if (!empty($params['status'])) {
            $dictionary = new Application_Model_Dictionaries_Table();
            switch ($params['status']) {
                case $dictionary->getStatusList('orders')->find('invoiced', 'acronym')->id:
                    $this->setWhere("client IS NOT NULL");
                    break;
                case $dictionary->getStatusList('orders')->find('released', 'acronym')->id:
                    $this->setWhere("qtyreturned = 0 AND client IS NULL");
                    break;
                case $dictionary->getStatusList('orders')->find('returned', 'acronym')->id:
                    //$this->setWhere("NOT(qtyreturned = 0 AND client IS NULL)");
                    break;
            }
            unset($params['statusid']);
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
                $this->setWhere($this->getAdapter()->quoteInto("serial IN (?)", $serials));
            } else {
                $this->setWhere($this->getAdapter()->quoteInto("serial LIKE ?", "%{$params['serial']}%"));
            }
            unset($params['serial']);
        }
        return parent::getAll($params, $rows, $root);
    }
    
    protected $_referenceMap = array(
        'Order' => array(
            'columns' => 'orderid',
            'refTableClass' => 'Application_Model_Orders_Table',
            'refColumns' => 'id'
        ),
        'Status' => array(
            'columns' => 'statusid',
            'refTableClass' => 'Application_Model_Dictionaries_Table',
            'refColumns' => 'id'
        ),
        'Technician' => array(
            'columns' => 'technicianid',
            'refTableClass' => 'Application_Model_Users_Table',
            'refColumns' => 'id'
        ),
        'Product' => array(
            'columns' => 'productid',
            'refTableClass' => 'Application_Model_Products_Table',
            'refColumns' => 'id'
        )
    );
    
}