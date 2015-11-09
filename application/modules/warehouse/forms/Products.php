<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Products extends Application_Form
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
                
        // ad an id element
        $this->addElement('hidden', 'id');
        
        $element = $this->createElement('select', 'warehouseid', array(
            'label'      => 'Magazyn:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
 
        // Add an email element
        $this->addElement('text', 'name', array(
            'label'      => 'Nazwa:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        /*$this->addElement('text', 'acronym', array(
            'label'      => 'Type acronym:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));*/
 
        // Add an email element
        $this->addElement('text', 'description', array(
            'label'      => 'Opis:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'serial', array(
            'label'      => 'SN:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'pairedcard', array(
            'label'      => 'Sprzęt sparowany:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                //'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'quantity', array(
            'label'      => 'Ilość:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('select', 'unitid', array(
            'label'      => 'Jednostka:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
 
        // Add an email element
        $this->addElement('text', 'price', array(
            'label'      => 'Cena jednostkowa:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
    }
    
}