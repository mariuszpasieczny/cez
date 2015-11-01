<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Products_Table extends Application_Db_Table
{
    protected $_name = 'serviceproducts';// table name
    protected $_primary = 'id'; // primary column name
    protected $_rowClass = 'Application_Model_Services_Products_Row';
    
    protected $_referenceMap = array(
        'Service' => array(
            'columns' => 'serviceid',
            'refTableClass' => 'Application_Model_Services_Table',
            'refColumns' => 'id'
        ),
        'Product' => array(
            'columns' => 'productid',
            'refTableClass' => 'Application_Model_Orders_Lines_Table',
            'refColumns' => 'id'
        )
    );
    
    public function select() {
        if ($this->_lazyLoading === true) {
            return parent::select();
        }
        return parent::select()->setIntegrityCheck(false)->from('serviceproductsview');
    }
    
}