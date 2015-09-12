<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Products_Row extends Application_Db_Table_Row {

    public function getWarehouse() {
        return parent::findParentRow('Application_Model_Warehouses_Table');
    }

    public function getUnit() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Unit');
    }

    public function getStatus() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Status');
    }

    public function getReleaseList() {
        $products = new Application_Model_Orders_Lines_Table();
        $products->setLazyLoading(false);
        $dictionaries = new Application_Model_Dictionaries_Table();
        $statusInvoiced = $dictionaries->getStatusList('orders')->find('invoiced', 'acronym');
        $statusReleased = $dictionaries->getStatusList('orders')->find('released', 'acronym');
        $products->setWhere($this->getTable()->getAdapter()->quoteInto('statusid IN (?)', array($statusInvoiced->id, $statusReleased->id)));
        return $products->getAll(array('productid' => $this->id));
    }
    
    public function isNew() {
        if ($this->getStatus()->acronym != 'new') {
            return false;
        }
        
        return true;
    }
    
    public function isAvailable() {
        if ($this->getStatus()->acronym != 'instock') {
            return false;
        }
        if (!$this->qtyavailable) {
            return false;
        }
        
        return true;
    }

}
