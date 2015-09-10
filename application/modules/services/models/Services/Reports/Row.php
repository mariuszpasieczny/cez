<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Reports_Row extends Application_Db_Table_Row {

    public function getWarehouse() {
        if (empty($this->warehouseid)) {
            return null;
        }
        return parent::findParentRow('Application_Model_Warehouses_Table');
    }

    public function getType() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Type');
    }

    public function getStatus() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Status');
    }

    public function getTechnician() {
        if (!$this->_technician) {
            if (empty($this->technicianid)) {
                return null;
            }
            $this->_technician = parent::findParentRow('Application_Model_Users_Table');
        }
        return $this->_technician;
    }

    public function isNew() {
        try {
            return $this->statusacronym == 'new' ? true : false;
        } catch (Exception $ex) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('reports')->find('new', 'acronym');

        return $this->statusid == $status->id ? true : false;
    }

}
