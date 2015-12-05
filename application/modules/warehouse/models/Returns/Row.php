<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Returns_Row extends Application_Db_Table_Row {

    public function getUnit() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Unit');
    }
    
    public function getService() {
        return parent::findParentRow('Application_Model_Services_Table', 'Service');
    }
    
    public function getTechnician() {
        try {
            return $this->getService()->getTechcnician();
        } catch (Exception $e) {
            return parent::findParentRow('Application_Model_Users_Table', 'Technician');
        }
    }
    
    public function getCatalog() {
        return parent::findParentRow('Application_Model_Catalog_Table', 'Catalog');
    }

    public function isNew() {
        try {
            return $this->statusacronym == 'new' ? true : false;
        } catch (Exception $e) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('returns')->find('new', 'acronym');

        return $this->statusid == $status->id ? true : false;
    }

    public function isConfirmed() {
        try {
            return $this->statusacronym == 'accepted' ? true : false;
        } catch (Exception $e) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('returns')->find('accepted', 'acronym');

        return $this->statusid == $status->id ? true : false;
    }

}
