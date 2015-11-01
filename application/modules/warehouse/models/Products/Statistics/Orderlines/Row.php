<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Products_Statistics_Orderlines_Row extends Application_Db_Table_Row {

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

}
