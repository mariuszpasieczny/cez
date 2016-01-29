<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Orders_Lines_Row extends Application_Db_Table_Row {

    public function getTechnician() {
        return parent::findParentRow('Application_Model_Users_Table');
    }

    public function getStatus() {
        if ($this->client) {
            $dictionary = new Application_Model_Dictionaries_Table();
            return $dictionary->getStatusList('orders')->find('invoiced', 'acronym');
        }
        if ($this->qtyreturned) {
            $dictionary = new Application_Model_Dictionaries_Table();
            return $dictionary->getStatusList('orders')->find('returned', 'acronym');
        }
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Status');
    }

    public function getProduct() {
        $products = new Application_Model_Products_Table();
        //$products->setLazyLoading(false);
        //$products->setOrderBy(array('product ASC'));
        $products->setSchema($this->getTable()->getSchema());
        return $products->getAll(array('id' => $this->productid))->current();
        return parent::findParentRow('Application_Model_Products_Table', 'Product');
    }

    public function isDeleted() {
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('orders')->find('deleted', 'acronym');

        return $this->statusid == $status->id ? true : false;
    }

    public function isNew() {
        try {
            return !$this->technicianid && $this->statusacronym == 'new' ? true : false;
        } catch (Exception $e) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('orders')->find('new', 'acronym');

        return !$this->technicianid && $this->statusid == $status->id ? true : false;
    }

    public function isReleased() {
        try {
            return $this->technicianid && $this->statusacronym == 'released' ? true : false;
        } catch (Exception $e) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('orders')->find('released', 'acronym');

        return $this->technicianid && $this->statusid == $status->id ? true : false;
    }

    public function isInvoiced() {
        try {
            return $this->technicianid && $this->statusacronym == 'invoiced' ? true : false;
        } catch (Exception $e) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('orders')->find('invoiced', 'acronym');

        return $this->technicianid && $this->statusid == $status->id ? true : false;
    }

    public function getReleaseList() {
        $products = new Application_Model_Services_Products_Table();
        $products->setLazyLoading(false);
        return $products->getAll(array('productid' => $this->id));
    }

}
