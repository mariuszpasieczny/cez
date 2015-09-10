<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Service extends Application_Model_Services_Row
{
    protected $_region;
    protected $_system;
    protected $_blockadecode;
    protected $_laborcode;
    protected $_complaintcode;
    protected $_area;
    protected $_productsreleased;

    public function getSystem() {
        if (!$this->_system && !empty($this->systemid)) {
            $this->_system = parent::findParentRow('Application_Model_Dictionaries_Table', 'System');
        }
        return $this->_system;
    }

    public function getRegion() {
        if (!$this->_region && !empty($this->regionid)) {
            $this->_region = parent::findParentRow('Application_Model_Dictionaries_Table', 'Region');
        }
        return $this->_region;
    }

    public function getLaborcode() {
        if (!$this->_laborcode && !empty($this->laborcodeid)) {
            $this->_laborcode = parent::findParentRow('Application_Model_Dictionaries_Table', 'Laborcode');
        }
        return $this->_laborcode;
    }

    public function getBlockadecode() {
        if (!$this->_blockadecode && !empty($this->blockadecodeid)) {
            $this->_blockadecode = parent::findParentRow('Application_Model_Dictionaries_Table', 'Blockadecode');
        }
        return $this->_blockadecode;
    }

    public function getComplaintcode() {
        if (!$this->_complaintcode && !empty($this->complaintcodeid)) {
            $this->_complaintcode = parent::findParentRow('Application_Model_Dictionaries_Table', 'Complaintcode');
        }
        return $this->_complaintcode;
    }

    public function getArea() {
        if (!$this->_area && !empty($this->areaid)) {
            $this->_area = parent::findParentRow('Application_Model_Dictionaries_Table', 'Area');
        }
        return $this->_area;
    }
    
    public function isHorizon() {
        return strpos($this->equipment, 'HORIZON') !== false;
    }
    
    public function isDCI() {
        return strpos($this->equipment, 'DCI') !== false;
    }
    
}