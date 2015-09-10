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
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Status');
    }

    public function getProduct() {
        return parent::findParentRow('Application_Model_Products_Table', 'Product');
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

}
