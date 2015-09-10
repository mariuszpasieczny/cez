<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Products_Import extends Application_Form
{
    
    public function setWarehouses($config) {
        $element = $this->getElement('warehouseid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }
    
    public function setUnits($config) {
        $element = $this->getElement('unitid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }
    
    public function init()
    {
        parent::init();
        
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setAttrib('target', 'file-upload');
        $this->setOptions(array('class' => 'file-upload'));
        
        $element = $this->createElement('select', 'warehouseid', array(
            'label'      => 'Warehouse:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('select', 'unitid', array(
            'label'      => 'Unit:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('file', 'import', array(
            'label'      => 'File:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control',
        ))->setDestination(APPLICATION_PATH . "/../data/temp")
            ->addValidator('Count', false, 1)
            ->addValidator('Size', false, 1024*1024*1024)
            ->addValidator('Extension', false, 'csv')
            ->setValueDisabled(true);
        $this->addElement($element);
    }
    
}