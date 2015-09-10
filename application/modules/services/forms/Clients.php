<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Clients extends Application_Form
{
    
    public function init()
    {
        parent::init();
        
        // ad an id element
        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'number');
 
        // Add an email element
        $this->addElement('text', 'city', array(
            'label'      => 'Type city:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'street', array(
            'label'      => 'Type street:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'number', array(
            'label'      => 'Type number:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'apartment', array(
            'label'      => 'Type apartment:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'postcode', array(
            'label'      => 'Type postal code:',
            'required'   => true,
            'filters'    => array('Digits'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
    }
    
}