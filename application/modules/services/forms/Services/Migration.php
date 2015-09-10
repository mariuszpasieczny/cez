<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Services_Migration extends Application_Form
{
    
    public function setTypes($config) {
        $config = $config->toArray();
        $element = $this->getElement('typeid');
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }
    
    public function setDefaults(array $defaults) {
        parent::setDefaults($defaults);
        //if (!empty($defaults['typeid'])) {
        //    $this->getElement('typeid')->setAttrib('readonly', 'readonly');
        //}
    }
    
    public function init()
    {
        $this->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element');
        
        parent::init();
        
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setAttrib('target', 'file-upload');
        $this->setOptions(array('class' => 'file-upload'));
        
        $this->addElement('hidden', 'typeid');
        
        $element = $this->createElement('checkbox', 'report', array(
            'label' => 'Generuj raport:',
            'class' => 'checkbox',
        ))->setChecked(true);
        $this->addElement($element);
        
        $element = $this->createElement('select', 'typeid', array(
            'label'      => 'Type:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);
        
        $element = $this->createElement('file', 'import', array(
            'label'      => 'Plik:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control',
        ))->setDestination(APPLICATION_PATH . "/../data/temp")
            ->addValidator('Count', false, 1)
            ->addValidator('Size', false, 1024*1024*1024)
            ->addValidator('Extension', false, 'xls')
            ->setValueDisabled(true);
        $this->addElement($element);
    }
    
}