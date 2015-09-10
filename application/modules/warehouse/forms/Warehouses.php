<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Warehouses extends Application_Form
{
    
    public function setParents($config) {
        if ($config) {
            $config = $config->toArray();
        }
        $element = $this->getElement('parentid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }
    
    public function setTypes($config) {
        if ($config) {
            $config = $config->toArray();
        }
        $element = $this->getElement('typeid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }
    
    public function setAreas($config) {
        if ($config) {
            $config = $config->toArray();
        }
        $element = $this->getElement('areaid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }
    
    public function init()
    {
        parent::init();
        
        // ad an id element
        $this->addElement('hidden', 'id');
 
        // Add an email element
        $this->addElement('text', 'name', array(
            'label'      => 'Warehouse name:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        
        $element = $this->createElement('select', 'parentid', array(
            'label'      => 'Parent warehouse:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('select', 'areaid', array(
            'label'      => 'Area:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('select', 'typeid', array(
            'label'      => 'Type:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
    }
    
}