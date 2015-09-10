<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Model_Dictionaries_Row extends Application_Db_Table_Row
{
    
    protected $_attributes;
    
    public function __toString() {
        return $this->name ? $this->name : $this->acronym;
    }
    
    public function getChildren($params = null) {
        //return $this->findDependentRowset('Application_Model_Dictionaries_Table', 'Children');
        $codes = new Application_Model_Dictionaries_Table();
        //$codes->setFrom('dictionaryattributesview');
        $codes->setLazyLoading(false);
        if (!empty($params['active'])) {
            $codes->setWhere('SYSDATE() BETWEEN IF(datefrom IS NULL, SYSDATE(), datefrom) AND IF(datetill IS NULL, SYSDATE(), datetill)');
        }
        $codes->setOrderBy('acronym');
        $rowset = $codes->getAll(array('parentid' => $this->id));//var_dump($rowset->toArray());
        return $rowset;
    }
    
    public function getAttributes($params = null, $table = 'Dictionary') {
        if (empty($this->_attributes[md5(serialize(func_get_args()))])) {
            $rowset = parent::findDependentRowset('Application_Model_Dictionaries_Attributes_Table', $table);
            if (empty($params) || !$rowset->count()) {
                $this->_attributes[md5(serialize(func_get_args()))] = $rowset;
            } else {
                $this->_attributes[md5(serialize(func_get_args()))] = $rowset->filter($params);
            }
        }
        return $this->_attributes[md5(serialize(func_get_args()))];
    }
    
    public function getErrorcode() {
        $table = $this->getTable();
        $table->setLazyLoading(false);
        $attributes = $table->getAttributeList();
        $attributeId = $attributes->find('errorcodeid', 'acronym')->id;
        $rowset = $this->getAttributes(array('attributeid' => $attributeId));
        if ($rowset->count()) $row = $rowset->current()->toArray();
        $table = new Application_Model_Dictionaries_Attributes_Table();
        if (empty($row)) {
            $cols = $table->info();
            $row = array_combine($cols['cols'], array_fill(0, sizeof($cols['cols']), null));
            $row['attributeid'] = $attributeId;
        }
        return new Application_Model_Dictionaries_Attributes_Solution(array(
            'table' => $table,
            'data' => $row,
            'readOnly' => false,
            'stored' => true
        ));
    }
    
    public function getSolutioncodes() {
        return parent::findDependentRowset('Application_Model_Dictionaries_Attributes_Table', 'Error');
    }
    
    public function getSolutioncode() {
        $table = $this->getTable();
        $table->setLazyLoading(false);
        $attributes = $table->getAttributeList();
        $attributeId = $attributes->find('errorcodeid', 'acronym')->id;
        $rowset = $this->getAttributes(array('attributeid' => $attributeId), 'Solution');
        if ($rowset->count()) $row = $rowset->current()->toArray();
        $table = new Application_Model_Dictionaries_Attributes_Table();
        if (empty($row)) {
            $cols = $table->info();
            $row = array_combine($cols['cols'], array_fill(0, sizeof($cols['cols']), null));
            $row['attributeid'] = $attributeId;
        }
        return new Application_Model_Dictionaries_Attributes_Solution(array(
            'table' => $table,
            'data' => $row,
            'readOnly' => false,
            'stored' => true
        ));
    }
    
    public function getErrorcodes() {
        return parent::findDependentRowset('Application_Model_Dictionaries_Attributes_Table', 'Solution');
    }
    
    public function getPrice() {
        $table = $this->getTable();
        $table->setLazyLoading(false);
        $attributes = $table->getAttributeList();
        $attributeId = $attributes->find('price', 'acronym')->id;
        $rowset = $this->getAttributes(array('attributeid' => $attributeId));
        if ($rowset->count()) $row = $rowset->current()->toArray();
        $table = new Application_Model_Dictionaries_Attributes_Table();
        if (empty($row)) {
            $cols = $table->info();
            $row = array_combine($cols['cols'], array_fill(0, sizeof($cols['cols']), null));
            $row['attributeid'] = $attributeId;
        }
        return new Application_Model_Dictionaries_Attributes_Solution(array(
            'table' => $table,
            'data' => $row,
            'readOnly' => false,
            'stored' => true
        ));
    }
    
    public function getDatefrom() {
        $table = $this->getTable();
        $table->setLazyLoading(false);
        $attributes = $table->getAttributeList();
        $attributeId = $attributes->find('datefrom', 'acronym')->id;
        $rowset = $this->getAttributes(array('attributeid' => $attributeId));
        if ($rowset->count()) $row = $rowset->current()->toArray();
        $table = new Application_Model_Dictionaries_Attributes_Table();
        if (empty($row)) {
            $cols = $table->info();
            $row = array_combine($cols['cols'], array_fill(0, sizeof($cols['cols']), null));
            $row['attributeid'] = $attributeId;
        }
        return new Application_Model_Dictionaries_Attributes_Solution(array(
            'table' => $table,
            'data' => $row,
            'readOnly' => false,
            'stored' => true
        ));
    }
    
    public function getDatetill() {
        $table = $this->getTable();
        $table->setLazyLoading(false);
        $attributes = $table->getAttributeList();
        $attributeId = $attributes->find('datetill', 'acronym')->id;
        $rowset = $this->getAttributes(array('attributeid' => $attributeId));
        if ($rowset->count()) $row = $rowset->current()->toArray();
        $table = new Application_Model_Dictionaries_Attributes_Table();
        if (empty($row)) {
            $cols = $table->info();
            $row = array_combine($cols['cols'], array_fill(0, sizeof($cols['cols']), null));
            $row['attributeid'] = $attributeId;
        }
        return new Application_Model_Dictionaries_Attributes_Solution(array(
            'table' => $table,
            'data' => $row,
            'readOnly' => false,
            'stored' => true
        ));
    }

    public function getStatus() {
        return parent::findParentRow('Application_Model_Dictionaries_Table', 'Status');
    }
    
}