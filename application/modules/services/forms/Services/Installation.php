<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Services_Installation extends Application_Form {

    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';
    protected $_disallowedElements;
    
    protected $_installationCodesCount = 1;

    public function setInstallationCodesCount($installationCodesCount) {
        $this->_installationCodesCount = $installationCodesCount;
    }

    public function __construct($options = null) {
        $this->addPrefixPath('Application_Form', 'Application/Form');
        parent::__construct($options);
    }

    public function setDefaults(array $defaults) {
        parent::setDefaults($defaults);
        if (empty($defaults['id'])) {
            return $this;
            $dissallowedElements = array('datefinished',
                'installationcodeid',
                'installationcancelcodeid',
                'productid',
                'technicalcomments');
        } else {
            $dissallowedElements = array('number',
                'warehouseid',
                'clientid',
                'servicetypeid',
                'planneddate',
                'timefrom',
                'timetill',
                'technicianid',
                'statusid');
        }
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('service')->find($defaults['statusid']);
        
        /*switch ($status->acronym) {
            case 'new':
            case 'assigned':
                $dissallowedElements = array_merge($dissallowedElements, array('datefinished',
                    'installationcodeid[]',
                    'productid[]',
                    'technicalcomments',
                    'coordinatorcomments'));
                break;
            case 'finished':
            case 'closed':
                break;
        }*/
        if ($dissallowedElements) {
            foreach ($dissallowedElements as $name) {
                if ($this->getElement($name)) {
                    $this->removeElement($name);
                }
            }
        }
        return $this;
        //var_dump($defaults,$dictionary->getStatusList('service'));exit;
    }

    public function setProducts($config) {
        if (!$element = $this->getElement('productid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        $selected = array();
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent->getProduct()->name . ' (' . $parent->getProduct()->serial . ')');
        }
    }

    public function setProductsreturned($config) {
        if (!$element = $this->getElement('productreturnedid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        $selected = array();
        foreach ($config as $parent) {
            $element->addMultiOption(trim($parent), trim($parent));
        }
    }

    public function setWarehouses($config) {
        if (!$element = $this->getElement('warehouseid')) {
            return;
        }
        //$element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }

    public function setTypes($config) {
        //$config = $config->toArray();
        //$element = $this->getElement('typeid');
        //$element->addMultiOption(null, 'Wybierz opcję...');
        //foreach ($config as $parent) {
        //    $element->addMultiOption($parent['id'], $parent['name']);
        //}
    }

    public function setStatuses($config) {
        if (!$element = $this->getElement('statusid')) {
            return;
        }
        //$element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }

    public function setClients($config) {
        if (!$element = $this->getElement('clientid')) {
            return;
        }
        //$element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['city'] . ', ' . $parent['street'] . ' ' . $parent['streetno'] . '/' . $parent['apartmentno']);
        }
    }

    public function setServicetypes($config) {
        if (!$element = $this->getElement('servicetypeid')) {
            return;
        }
        //$element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name'] ? $parent['name'] : $parent['acronym']);
        }
    }

    public function setTechnicians($config) {
        if (!$element = $this->getElement('technicianid')) {
            return;
        }
        //$element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['firstname'] . ' ' . $parent['lastname']);
        }
    }

    public function setInstallationcodes($config) {
        for ($i = 0; $i <= $this->_installationCodesCount; $i++) {
            $element = $this->getElement('installationcodeid-' . $i);
            if (!$element) {
                return;
            }
            //$element->addMultiOption(null, 'Wybierz opcję...');
            foreach ($config as $parent) {
                $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
            }
        }
        
    }

    public function setInstallationcancelcodes($config) {
        if (!$element = $this->getElement('installationcancelcodeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setDefault($name, $value) {
        $name = (string) $name;
        switch ($name) {
            case 'productid':
                $selectedIds = array();
                foreach ($value as $row) {
                    $selectedIds[] = $row['productid'];
                    $attribs = $this->getElement($name)->getAttribs();
                    $options = $attribs['options'];
                    if (!isset($options[$row['productid']])) {
                        $this->getElement($name)->addMultiOption($row['name'], $row['name']);
                        $selectedIds[] = $row['name'];
                    }
                }
                $value = $selectedIds;
                break;
            case 'installationcodeid':
            case 'errorcodeid':
            case 'solutioncodeid':
            case 'cancellationcodeid':
            case 'installationcancelcodeid':
            case 'modeminterchangecodeid':
            case 'decoderinterchangecodeid':
                $selectedIds = array();
                foreach ($value as $row) {
                    $selectedIds[] = $row['codeid'];
                }
                $value = $selectedIds;
                break;
        }
        parent::setDefault($name, $value);
    }

    public function init() {
        parent::init();

        $this->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element');

        // ad an id element
        $this->addElement('hidden', 'id');
        $this->addElement('hidden', 'number');
        $this->addElement('hidden', 'typeid');
        
        /*$element = $this->createElement('checkbox', 'performed', array(
            'label' => 'Wykonane:',
            'class' => 'checkbox',
        ));*/
        /*$element = $this->createElement('radio', 'performed', array(
            'label' => 'Wykonane:',
            'class' => 'checkbox',
            'multiOptions'=>array('1' => 'tak', '0' => 'nie'),
            'label_class' => array('class' => 'label-inline'),
            'value' => null
        ));*/
        $element = $this->createElement('select', 'performed', array(
            'label' => 'Wykonane:',
            'multiOptions'=>array('' => 'nie ustalone', '0' => 'nie', '1' => 'tak'),
            'value' => '',
            'class' => 'form-control chosen-select'
        ));
        $this->addElement($element);

        /* $element = $this->createElement('select', 'typeid', array(
          'label'      => 'Typ:',
          //'required'   => true,
          'filters'    => array('StringTrim'),
          'validators' => array(
          //    array('lessThan', true, array('score')),
          ),
          'class' => 'form-control chosen-select',
          ));
          $this->addElement($element); */

        // Add an email element
        $element = $this->createElement('text', 'number', array(
            'label' => 'Numer:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'warehouseid', array(
            'label' => 'Magazyn:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'clientid', array(
            'label' => 'Klient:',
            'required' => true,
            'filters' => array('StringTrim'),
            'description' => '<a href="/services/clients/edit" data-target=".modal-body" class="popup-load btn btn-default">Utwórz</a>',
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
                //'decorators' => array('ViewHelper',
                //'Errors',
                //array('Description', array('escape' => false, 'tag' => 'span', 'class' => 'input-group-btn', 'placement' => 'append')),
                //array('Label', array('tag' => 'td', 'placement' => 'prepend')),
                //array(array('data' => 'HtmlTag'), array('tag' => 'div', 'class' => 'input-group')),
                //array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                //array('HtmlTag', array('tag' => 'div', 'class' => 'form-control'))
                //)
        ));
        $element->addDecorator('Description', array('escape' => false, 'tag' => 'span', 'class' => 'input-group-btn', 'placement' => 'append'));
        $element->addDecorator('HtmlTag', array('class' => 'input-group'));
        $this->addElement($element);

        $element = $this->createElement('select', 'servicetypeid', array(
            'label' => 'Typ:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        // Add an email element
        $element = $this->createElement('datePicker', 'planneddate', array(
            'label' => 'Data:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $element->addDecorator('Description', array('escape' => false, 'tag' => 'span', 'class' => 'input-group-btn', 'placement' => 'append'));
        $element->addDecorator('HtmlTag', array('class' => 'input-group'));
        $this->addElement($element);
        $element = $this->createElement('datePicker', 'timefrom', array(
            'label' => 'Od:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $element->addDecorator('Description', array('escape' => false, 'tag' => 'span', 'class' => 'input-group-btn', 'placement' => 'append'));
        $element->addDecorator('HtmlTag', array('class' => 'input-group'));
        $this->addElement($element);
        $element = $this->createElement('datePicker', 'timetill', array(
            'label' => 'Do:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $element->addDecorator('Description', array('escape' => false, 'tag' => 'span', 'class' => 'input-group-btn', 'placement' => 'append'));
        $element->addDecorator('HtmlTag', array('class' => 'input-group'));
        $this->addElement($element);

        $element = $this->createElement('select', 'statusid', array(
            'label' => 'Status:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'technicianid', array(
            'label' => 'Technik:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('datePicker', 'datefinished', array(
            'label' => 'Data zakończenia:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'installationcodeid-0', array(
                    'label' => 'Kod instalacji:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'installationcodeid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'style' => 'width: 250px;'))->setRegisterInArrayValidator(false);
        $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
        $this->addElement($element);
        
        for ($i = 1; $i <= $this->_installationCodesCount; $i++) {
            $element = $this->createElement('select', 'installationcodeid-' . $i, array(
                    //'label' => 'Kod instalacji:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'installationcodeid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'style' => 'width: 250px;'))->setRegisterInArrayValidator(false);
            $this->addElement($element);
            $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => ''));
            $this->addDisplayGroup(array('installationcodeid-' . $i), 
                    'service-' . $i,
                    array(
                        'decorators' => array(
                            'FormElements',
                            array('HtmlTag', array('class' => 'form-group inline'))
                        )
                    ));
        }

        $element = $this->createElement('select', 'installationcancelcodeid', array(
                    'label' => 'Kod odwołania:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select ',
                ))->setAttribs(array('placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        $element = $this->createElement('select', 'productid', array(
                    'label' => 'Produkty:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        $element = $this->createElement('select', 'productreturnedid', array(
                    'label' => 'Produkty odebrane:',
                    //'required'   => true,
                    //'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        $element = $this->createElement('textarea', 'technicalcomments', array(
                    'label' => 'Komentarz techniczny:',
                    //'required'   => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control',
                ))->setAttrib('ROWS', '4');
        $this->addElement($element);

        $element = $this->createElement('textarea', 'coordinatorcomments', array(
                    'label' => 'Komentarz koordynatora:',
                    //'required'   => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control',
                ))->setAttrib('ROWS', '4');
        $this->addElement($element);
    }

}
