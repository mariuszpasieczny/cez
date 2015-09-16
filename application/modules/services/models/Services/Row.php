<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Services_Row extends Application_Db_Table_Row {

    protected $_client = array();
    protected $_technician = array();
    protected $_servicetype;
    protected $_calendar;
    protected $_productsreleased;
    protected $_productsreturned;

    public function __get($columnName) {
        try {
            return parent::__get($columnName);
        } catch (Exception $ex) {
            switch ($columnName) {
                case 'clienthomephone':
                    return $this->getClient()->homephone;
                case 'clientworkphone':
                    return $this->getClient()->workphone;
                case 'clientcellphone':
                    return $this->getClient()->cellphone;
                default:
                    $method = 'get' . ucfirst($columnName);
                    if (!method_exists($this, $method)) {
                        throw new Exception($ex->getMessage());
                    }
                    return $this->{$method}();
            }
        }
    }

    public function __call($method, array $args) {
        if (strpos($method, 'get') === 0) {
            $columnName = '_' . strtolower(substr($method, 3, strlen($method)));
            return $this->{$columnName};
        }
        if (strpos($method, 'set') === 0) {
            $columnName = '_' . strtolower(substr($method, 3, strlen($method)));
            return $this->{$columnName}($arguments);
        }
        return parent::__call($method, $arguments);
    }

    public function getProductsreleased() {
        if (!$this->_productsreleased) {
            $products = array();
            foreach ($this->getProducts() as $product) {
                $products[] = $product->serial ? $product->serial : $product->name;
            }
            $this->_productsreleased = @join(',', array_filter($products));
        }
        return $this->_productsreleased;
    }

    public function getProductsreturned() {
        if (!$this->_productsreturned) {
            $products = array();
            foreach ($this->getReturns() as $product) {
                $products[] = $product->serial ? $product->serial : $product->name;
            }
            $this->_productsreturned = @join(',', array_filter($products));
        }
        return $this->_productsreturned;
    }

    public function getProducts() {
        //return $this->findDependentRowset('Application_Model_Services_Products_Table', 'Service');
        $products = new Application_Model_Services_Products_Table();
        $products->setLazyLoading(false);
        return $products->getAll(array('serviceid' => $this->id));
    }

    public function getReturns() {
        //return $this->findDependentRowset('Application_Model_Services_Products_Table', 'Service');
        $products = new Application_Model_Services_Returns_Table();
        $products->setLazyLoading(false);
        return $products->getAll(array('serviceid' => $this->id));
    }

    public function getCodes() {
        //return $this->findDependentRowset('Application_Model_Services_Codes_Table', 'Service');
        $codes = new Application_Model_Services_Codes_Table();
        $codes->setLazyLoading(false);
        return $codes->getAll(array('serviceid' => $this->id));
    }

    public function getWarehouse() {
        if (empty($this->warehouseid)) {
            return null;
        }
        return parent::findParentRow('Application_Model_Warehouses_Table');
    }

    public function getType() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Type');
    }

    public function getCalendar() {
        if (!$this->_calendar && !empty($this->calendarid)) {
            $this->_calendar = parent::findParentRow('Application_Model_Dictionaries_Table', 'Calendar');
        }
        return $this->_calendar;
    }

    public function getServiceType() {
        if (!$this->_servicetype) {
            $this->_servicetype = parent::findParentRow('Application_Model_Dictionaries_Table', 'ServiceType');
        }
        return $this->_servicetype;
    }

    public function getStatus() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Status');
    }

    public function getClient() {
        if (!$this->_client && !empty($this->clientid)) {
            $this->_client = parent::findParentRow('Application_Model_Clients_Table');
        }
        return $this->_client;
    }

    public function getTechnician() {
        if (!$this->_technician && !empty($this->technicianid)) {
            $this->_technician = parent::findParentRow('Application_Model_Users_Table');
        }
        return $this->_technician;
    }

    public function getDeadline($format = null) {
        $dateFrom = new Zend_Date($this->planneddate . ' ' . $this->timefrom);
        $dateTill = new Zend_Date($this->planneddate . ' ' . $this->timetill);
        return $dateFrom->get('yyyy-MM-dd') . ' ' . $this->getPlannedtime();
    }

    public function getPlanneddate() {
        $date = new Zend_Date($this->planneddate);
        return $date->get('yyyy-MM-dd');
    }

    public function getPlannedtime() {
        $dateFrom = new Zend_Date($this->timefrom);
        $dateTill = new Zend_Date($this->timetill);
        //return $dateFrom->get('HH:mm') . '-' . $dateTill->get('HH:mm');
        return date('H:i', strtotime($this->timefrom)) . '-' . date('H:i', strtotime($this->timetill));
    }

    public function getTimefrom() {
        $date = new Zend_Date($this->timefrom);
        //return $date->get('HH:mm');
        return date('H:i', strtotime($this->timefrom));
    }

    public function getTimetill() {
        $date = new Zend_Date($this->timetill);
        //return $date->get('HH:mm');
        return date('H:i', strtotime($this->timetill));
    }

    public function isAssigned() {
        if (!$this->technicianid) {
            return false;
        }
        try {
            if ($this->statusacronym == 'assigned') {
                return true;
            }
            if ($this->statusacronym == 'reassigned') {
                return true;
            }
        } catch (Exception $e) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('service')->find('assigned', 'acronym');
        if ($this->statusid == $status->id) {
            return true;
        }
        $status = $dictionary->getStatusList('service')->find('reassigned', 'acronym');
        if ($this->statusid == $status->id) {
            return true;
        }
        return false;
    }

    public function isUnperformed() {
        if (!$this->technicianid) {
            return false;
        }
        try {
            if ($this->statusacronym == 'unperformed') {
                return true;
            }
        } catch (Exception $ex) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('service')->find('unperformed', 'acronym');
        if ($status && $this->statusid == $status->id) {
            return true;
        }
        $status = $dictionary->getStatusList('service')->find('finished', 'acronym');
        if ($this->statusid == $status->id && $this->performed == 0) {
            return true;
        }
        return false;
    }

    public function isFinished() {
        if (!$this->technicianid) {
            //return false;
        }
        try {
            if ($this->statusacronym == 'finished') {
                return true;
            }
            if ($this->statusacronym == 'performed') {
                //return true;
            }
            if ($this->statusacronym == 'unperformed') {
                //return true;
            }
        } catch (Exception $e) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('service')->find('finished', 'acronym');
        if ($this->statusid == $status->id) {
            return true;
        }
        $status = $dictionary->getStatusList('service')->find('performed', 'acronym');
        if ($status && $this->statusid == $status->id) {
            //return true;
        }
        $status = $dictionary->getStatusList('service')->find('unperformed', 'acronym');
        if ($status && $this->statusid == $status->id) {
            //return true;
        }
        return false;
    }

    public function isClosed() {
        if (!$this->technicianid) {
            //return false;
        }
        try {
            if ($this->statusacronym == 'closed') {
                return true;
            }
        } catch (Exception $ex) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('service')->find('closed', 'acronym');
        if ($this->statusid == $status->id) {
            return true;
        }
        return false;
    }

    public function isNew() {
        if ($this->technicianid) {
            //return false;
        }
        try {
            if ($this->statusacronym == 'new') {
                return true;
            }
        } catch (Exception $ex) {
            
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('service')->find('new', 'acronym');
        if ($this->statusid == $status->id) {
            return true;
        }
        $status = $dictionary->getStatusList('service')->find('withdrawn', 'acronym');
        if ($this->statusid == $status->id) {
            return true;
        }
        return false;
    }

}
