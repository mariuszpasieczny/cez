<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

// application/forms/User.php
 
class Application_Form_User extends Application_Form
{
    
    public function setRoles($config) {
        $element = $this->getElement('role');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $role) {
            $option = $element->addMultiOption($role, $role);
        }
    }
    
    public function setRegions($config) {
        if (!$element = $this->getElement('region'))
                return;
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $region) {
            $element->addMultiOption($region, $region);
        }
    }
    
    /*public function setDefaults(array $defaults) {
        parent::setDefaults($defaults);
        $dictionary = new Application_Model_Dictionaries_Table();
        $parent = $dictionary->getStatusList('users')->find('active', 'acronym');
        $this->getElement('active')->setChecked($defaults['statusid'] == $parent['id'] ? true : false);
    }*/
    
    public function init()
    {
        parent::init();
        
        // ad an id element
        $this->addElement('hidden', 'id');
 
        // Add an email element
        $this->addElement('text', 'symbol', array(
            'label'      => 'Symbol:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'firstname', array(
            'label'      => 'Imię:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'lastname', array(
            'label'      => 'Nazwisko:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Adres e-mail:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            ),
            'class' => 'form-control',
        ));
 
        // Add an email element
        $this->addElement('text', 'phoneno', array(
            'label'      => 'Phone no:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                //'EmailAddress',
            ),
            'class' => 'form-control',
        ));
        
        /*$this->addElement('checkbox', 'active', array(
            'label'      => 'Aktywny:',
        ));
        
        $this->addElement('checkbox', 'changepassword', array(
            'label'      => 'Zmień hasło:',
        ));
 
        // Add a password element
        $element = $this->createElement('password', 'password', array(
            'label'      => 'Hasło:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
                //array('stringLength', false, array(6, 20)),
                array('identical', true, array('verifypassword'))
            ),
            'class' => 'form-control',
        ))->addValidator('StringLength', false, array(6));
        $this->addElement($element);
        
        // add a verify password element
        $this->addElement('password', 'verifypassword', array(
            'label'      => 'Powtórz hasło:',
            //'required'   => true,
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));*/
        
        $element = $this->createElement('select', 'role', array(
            'label'      => 'Rola:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('select', 'region', array(
            'label'      => 'Region:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
    }
}