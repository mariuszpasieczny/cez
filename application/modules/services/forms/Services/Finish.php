<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Services_Finish extends Application_Form
{
    
    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';
    
    public function __construct($options = null)
    {
        $this->addPrefixPath('Application_Form', 'Application/Form');
        parent::__construct($options);
    }
    
    public function setProducts($config) {
        if ($config) {
            $config = $config->toArray();
        }
        $element = $this->getElement('productid');
        //$element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name'] . ' (' . $parent['serial'] . ')');
        }
    }
    
    public function init()
    {
        parent::init();
        
        $this->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element');
        
        // ad an id element
        $this->addElement('hidden', 'id');
        
        $element = $this->createElement('select', 'errorcode', array(
            'label'      => 'Kod błędu:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('select', 'solutioncode', array(
            'label'      => 'Kod rozwiązania:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('datePicker', 'datefinished', array(
            'label'      => 'Data zakończenia:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('select', 'productid', array(
            'label'      => 'Produkty:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            //'validators' => array(
            //    array('lessThan', true, array('score')),
            //),
            'class' => 'form-control chosen-select',
        ))->setAttribs(array('multiple' => 'multiple', 'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);
        
        $element = $this->createElement('textarea', 'technicalcomments', array(
            'label'      => 'Komentarz techniczny:',
            //'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                //'NotEmpty',
            ),
            'class' => 'form-control',
        ))->setAttrib('ROWS', '4');
        $this->addElement($element);
    }
    
}