<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Dictionaries extends Application_Form {

    public function setParents($config) {
        if (!$element = $this->getElement('parentid')) {
            return;
        }
        //$config = $config->toArray();
        $element = $this->getElement('parentid');
        $element->addMultiOption(null, 'Please select...');
        $table = new Application_Model_Dictionaries_Table();
        //$errorCode = $table->getDictionaryList('service')->find('errorcode', 'acronym');
        //$solutionCode = $table->getDictionaryList('service')->find('solutioncode', 'acronym');
        foreach ($config as $parent) {
            $disabledIds[] = $parent['id'];
            $element->addMultiOption($parent['id'], $parent['name']);
            foreach ($parent->getChildren() as $item) {
                if ($item['system']) continue;
                $element->addMultiOption($item['id'], '---' . $item['name']);
            }
            $element->setAttrib('readonly', $disabledIds);
        }
    }
    
    public function setErrorcodes($config) {
        if (!$element = $this->getElement('errorcodeid')) {
            return;
        }
        $config = $config->toArray();
        $element = $this->getElement('errorcodeid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }
    
    public function setSolutioncodes($config) {
        if (!$element = $this->getElement('solutioncodeid')) {
            return;
        }
        $config = $config->toArray();
        $element = $this->getElement('solutioncodeid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }
    
    public function setRegions($config) {
        if (!$element = $this->getElement('regionid')) {
            return;
        }
        $config = $config->toArray();
        $element = $this->getElement('regionid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }
    
    public function setInstances($config) {
        if (!$element = $this->getElement('instanceid')) {
            return;
        }
        $config = $config->toArray();
        $element = $this->getElement('instanceid');
        $element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }
    
    public function setDefaults(array $defaults) {
        parent::setDefaults($defaults);
        $dictionary = new Application_Model_Dictionaries_Table();
        //$parent = $dictionary->getDictionaryList('service')->find('solutioncode', 'acronym');
        //if (!empty($defaults['parentid']) && $defaults['parentid'] !== $parent['id']) {
            //$this->removeElement('errorcodeid');
        //}
    }
    
    public function setDefault($name, $value) {
        $name = (string) $name;
        switch ($name) {
            case 'solutioncodeid':
                $selectedIds = array();
                foreach ($value as $row) {
                    $selectedIds[] = $row['entryid'];
                }
                $value = $selectedIds;
                break;
        }
        parent::setDefault($name, $value);
    }

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

        // Add an email element
        $element = $this->createElement('text', 'acronym', array(
            'label' => 'Skrót:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'parentid', array(
            'label' => 'Kategoria:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'regionid', array(
            'label' => 'Region:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'instanceid', array(
            'label' => 'Instancja:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'errorcodeid', array(
            'label' => 'Kod błędu:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'solutioncodeid', array(
            'label' => 'Kod rozwiązania:',
            //'required'   => true,
            //'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ))->setAttribs(array('placeholder' => 'Choose product', 'disabled' => 'disabled'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        // Add an email element
        $element = $this->createElement('text', 'price', array(
            'placeholder' => 'Cena:',
            //'required'   => true,
            'validators' => array(
            //'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        //$element->addFilter('PregReplace', array(
        //    'match' => '/,/',
        //    'replace' => '/./'
        //));
        $element->addDecorator('HtmlTag', array('class' => 'form-group'));
        $element->addDecorator('Label', array('tag' => ''));
        $this->addElement($element);

        // Add an email element
        $element = $this->createElement('text', 'datefrom', array(
            'placeholder' => 'Data od:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $element->addDecorator('HtmlTag', array('class' => 'form-group'));
        $element->addDecorator('Label', array('tag' => ''));
        $this->addElement($element);

        // Add an email element
        $element = $this->createElement('text', 'datetill', array(
            'placeholder' => 'Data do:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $element->addDecorator('HtmlTag', array('class' => 'form-group'));
        $element->addDecorator('Label', array('tag' => ''));
        $this->addElement($element);
        
        $this->addDisplayGroup(array('price','datefrom','datetill'), 'attributes');
    }

}
