<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Services_Reports_Send extends Application_Form {

    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';

    public function __construct($options = null) {
        $this->addPrefixPath('Application_Form', 'Application/Form');
        //$this->setAction('/services/reports/generate/format/xls');
        parent::__construct($options);
    }

    public function init() {
        parent::init();

        $this->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element');

        // ad an id element
        //$this->addElement('hidden', 'id');
        $this->addElement('hidden', 'typeid');

        $element = $this->createElement('text', 'recipient', array(
            'label' => 'Do:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);

        $element = $this->createElement('text', 'subject', array(
            'label' => 'Temat:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);

        $element = $this->createElement('textarea', 'content', array(
            'label' => 'Treść:',
            //'required'   => true,
            //'filters' => array('StringTrim'),
            'validators' => array(
            //'NotEmpty',
            ),
            'class' => 'form-control',
        ))->setAttrib('ROWS', '4');
        $this->addElement($element);

        $element = $this->createElement('file', 'filename', array(
            'label' => 'Raport:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //'NotEmpty',
            ),
            'class' => 'form-control',
        ))->setAttrib('ROWS', '4');
        //$this->addElement($element);
        
        $this->getElement('submit')->setLabel('Wyślij');
    }

}
