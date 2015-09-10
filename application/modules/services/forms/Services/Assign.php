<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Services_Assign extends Application_Form
{
    
    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';
    
    protected $_servicesCount = 1;

    public function setServicesCount($servicesCount) {
        $this->_servicesCount = $servicesCount;
    }

    public function setServices($config) {
        foreach ($config as $i => $service) {
            $element = $this->getElement('id-' . $i);
            $element->setValue($service->id);
            //$element->setCheckedValue($product->id);
            $element->setLabel($service->number 
                . ' (' . $service->clientid ? $service->client : ''
                . ')'
                . ($service->technicianid ? ' ' . $service->getTechnician() : ''));
            //$element->setChecked(true);
            //$element->setAttrib('disabled', 'disabled');
        }
    }
    
    public function __construct($options = null)
    {
        $this->addPrefixPath('Application_Form', 'Application/Form');
        parent::__construct($options);
    }
    
    public function setTechnicians($config) {
        $config = $config->toArray();
        $element = $this->getElement('technicianid');
        if (!$element) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['lastname'] . ' ' . $parent['firstname']);
        }
    }
    
    public function init()
    {
        parent::init();
        
        $this->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element');
        
        // ad an id element
        for ($i = 0; $i < $this->_servicesCount; $i++) {
            
            $this->addElement('hidden', 'id-' . $i, array(
                //'belongsTo' => 'id', 
                //'id' => 'id[' . $i . ']',
                //'isArray' => true
            ));

            $this->addDisplayGroup(array('id-' . $i), 'service-' . $i);
        }
        
        $element = $this->createElement('select', 'technicianid', array(
            'label'      => 'Technik:',
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