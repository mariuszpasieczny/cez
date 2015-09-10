<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Dictionaries_Import extends Application_Form
{
    
    public function setParents($config) {
        //$config = $config->toArray();
        $element = $this->getElement('parentid');
        $element->addMultiOption(0, 'Please select...');
        $table = new Application_Model_Dictionaries_Table();
        $errorCode = $table->getDictionaryList('service')->find('errorcode', 'acronym');
        $solutionCode = $table->getDictionaryList('service')->find('solutioncode', 'acronym');
        foreach ($config as $parent) {
            $disabledIds[] = $parent['id'];
            $element->addMultiOption($parent['id'], $parent['name']);
            foreach ($parent->getChildren() as $item) {
                $element->addMultiOption($item['id'], '---' . $item['name']);
            }
            //$element->setAttrib('readonly', $disabledIds);
        }
    }
    
    public function init()
    {
        parent::init();
        
        $this->setAttrib('enctype', 'multipart/form-data');
        $this->setAttrib('target', 'file-upload');
        $this->setOptions(array('class' => 'file-upload'));
        
        $element = $this->createElement('select', 'parentid', array(
            'label'      => 'Kategoria:',
            //'required'   => true,
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
            ->addValidator('Extension', false, array('xlsx','csv','json'))
            ->setValueDisabled(true);
        $this->addElement($element);
    }
    
}