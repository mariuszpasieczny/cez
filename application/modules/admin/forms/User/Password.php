<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_User_Password extends Application_Form
{
    
    public function init()
    {
        parent::init();
        
        // ad an id element
        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'score');
 
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
        ));
    }
}