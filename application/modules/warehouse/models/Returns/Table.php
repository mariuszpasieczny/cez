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
    
}