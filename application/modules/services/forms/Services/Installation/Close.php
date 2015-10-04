<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Services_Installation_Close extends Application_Form
{
    
    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';
    
    protected $_installationCodesCount = 1;
    protected $_productsReturnedCount = 1;

    public function setProductsReturnedCount($productsReturnedCount) {
        $this->_productsReturnedCount = $productsReturnedCount;
    }

    public function setInstallationCodesCount($installationCodesCount) {
        $this->_installationCodesCount = $installationCodesCount;
    }
    
    public function __construct($options = null)
    {
        $this->addPrefixPath('Application_Form', 'Application/Form');
        parent::__construct($options);
    }
    
    public function setProducts($config) {
        if (!$config) {
            return;
        }
        $element = $this->getElement('productid');
        //$element->addMultiOption(null, 'Please select...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent->getProduct() ? ($parent->getProduct()->name . ' (' . $parent->getProduct()->serial . ')') : '');
        }
    }

    public function setProductsreturned($config) {
        for ($i = 0; $i <= $this->_productsReturnedCount; $i++) {
            $element = $this->getElement('productreturnedid-' . $i);
            if (!$element) {
                return;
            }
            //$element->addMultiOption(null, 'Wybierz opcję...');
            foreach ($config as $parent) {
                $element->addMultiOption(trim($parent), trim($parent));
            }
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
        if (strpos($name, 'productreturnedid') !== false) {
            $selectedIds = array();
            $selectedIds[] = $value['name'];
            $attribs = $this->getElement($name)->getAttribs();
            $options = $attribs['options'];
            if (!isset($options[$value->name])) {
                $this->getElement($name)->addMultiOption($value->name, $value->name);
                $selectedIds[] = $value->name;
            }
            preg_match("/\d+/", $name, $found);
            $this->getElement('demaged-' . $found[0])->setValue($value->demaged);
            if (!$value->isNew()) {
                $this->getElement('demaged-' . $found[0])->setAttrib('disabled', 'disabled');
                $this->getElement('productreturnedid-' . $found[0])->setAttrib('disabled', 'disabled');
            }
            $value = $selectedIds;
        }
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
            case 'productreturnedid':
                $selectedIds = array();
                foreach ($value as $row) {
                    $selectedIds[] = trim($row);
                }
                $value = $selectedIds;
                break;
        }
        parent::setDefault($name, $value);
    }
    
    public function init()
    {
        parent::init();
        
        $this->addPrefixPath('ZendX_JQuery_Form_Element', 'ZendX/JQuery/Form/Element', 'element');
        
        // ad an id element
        $this->addElement('hidden', 'id');
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
            'required'   => true,
            'class' => 'form-control chosen-select'
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
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array(/*'multiple' => 'multiple', */'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);
        
        $element = $this->createElement('select', 'productid', array(
            'label'      => 'Sprzęt wydany:',
            //'required'   => true,
            //'filters'    => array('StringTrim'),
            //'validators' => array(
            //    array('lessThan', true, array('score')),
            //),
            'class' => 'form-control chosen-select',
        ))->setAttribs(array('multiple' => 'multiple', 'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        /*$element = $this->createElement('select', 'productreturnedid', array(
                    'label' => 'Produkty odebrane:',
                    //'required'   => true,
                    //'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);*/
        
        $element = $this->createElement('select', 'productreturnedid-0', array(
                    'label' => 'Produkty odebrane:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'productreturnedid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'style' => 'max-width: 65%;'))->setRegisterInArrayValidator(false);
        $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
        $this->addElement($element);
        $element = $this->createElement('checkbox', 'demaged-0', array(
            'label' => 'uszkodzony',
            'belongsTo' => 'demaged',
            'class' => 'form-group input-small',
        ))->setAttribs(array('style' => 'width: 50px;'));
        $element->addDecorator('HtmlTag', array('tag' => 'span', 'class' => 'form-group inline'));
        $element->addDecorator('Label', array('tag' => 'span', 'placement' => 'append'));
        $this->addElement($element);
        
        for ($i = 1; $i <= $this->_productsReturnedCount; $i++) {
            $element = $this->createElement('select', 'productreturnedid-' . $i, array(
                    //'label' => 'Produkty:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'productreturnedid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'style' => 'max-width: 65%;'))->setRegisterInArrayValidator(false);
            $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => ''));
            $this->addElement($element);
            $element = $this->createElement('checkbox', 'demaged-' . $i, array(
                'label' => 'uszkodzony',
                'belongsTo' => 'demaged',
                'class' => 'form-group input-small',
            ))->setAttribs(array('style' => 'width: 50px;'));
            $element->addDecorator('HtmlTag', array('tag' => 'span', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => 'span', 'placement' => 'append'));
            $this->addElement($element);
        }
        
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
        
        $element = $this->createElement('textarea', 'coordinatorcomments', array(
            'label'      => 'Komentarz koordynatora:',
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