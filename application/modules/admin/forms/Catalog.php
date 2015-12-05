<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Catalog extends Application_Form {

    public function init() {
        parent::init();

        // ad an id element
        $this->addElement('hidden', 'id');
        $this->setAttrib('class', 'form-inline form-load');

        // Add an email element
        $element = $this->createElement('text', 'name', array(
            'label' => 'Nazwa:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);
    }

}
