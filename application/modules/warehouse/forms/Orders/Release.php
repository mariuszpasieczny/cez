<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Orders_Release extends Application_Form {

    protected $_productsCount = 1;

    public function setProductsCount($productsCount) {
        $this->_productsCount = $productsCount;
    }

    public function setTechnicians($config) {
        $config = $config->toArray();
        $element = $this->getElement('technicianid');
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['firstname'] . ' ' . $parent['lastname']);
        }
    }

    public function setProducts($config) {
        foreach ($config as $i => $product) {
            $element = $this->getElement('id-' . $i);
            $element->setValue($product->id);
            $element->setCheckedValue($product->id);
            $element->setLabel($product->product . ' (' . $product->serial . ')');
            $element->setChecked(true);
        }
    }

    public function init() {
        parent::init();
        $this->setAttrib('class', 'form-inline form-load');
        $this->setDecorators(
            array(
                'FormElements',
                'Form',
                array('HtmlTag', array('tag' => 'dl', 'class' => 'overflow zend_form')),
            )
        );

        // ad an id element
        $this->addElement('hidden', 'id');

        for ($i = 0; $i < $this->_productsCount; $i++) {

            $element = $this->createElement('checkbox', 'id-' . $i, array(
                    //'belongsTo' => 'id', 
                    //'id' => 'id[' . $i . ']',
                    //'isArray' => true
                    //'class' => 'form-control'
                'disableHidden' => true
            ));
            $element->addDecorator('Label', array('tag' => 'label', 'placement' => 'append'));
            $element->addDecorator('HtmlTag', array('class' => 'form-group'));
            $this->addElement($element);

            $this->addDisplayGroup(array('id-' . $i), 'product-' . $i, array('style' => 'height: 10px;'));
        }

        $element = $this->createElement('select', 'technicianid', array(
            'label' => 'Technik:',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select'
        ));
        $element->addDecorator('Label', array('tag' => 'label', 'placement' => 'prepend', 'style' => 'margin-top: 25px;'));
        $this->addElement($element);

        $element = $this->createElement('checkbox', 'print', array(
            'Label' => 'Generuj potwierdzenie',
                //'belongsTo' => 'id', 
                //'id' => 'id[' . $i . ']',
                //'isArray' => true
                //'class' => 'form-control'
        ));
        $element->addDecorator('Label', array('tag' => 'label', 'placement' => 'prepend'));
        $element->addDecorator('HtmlTag', array('class' => 'form-group'));
        $this->addElement($element);
    }

}
