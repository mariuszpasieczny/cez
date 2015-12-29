<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Services_Service_Finish extends Application_Form {

    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';
    protected $_productsReturnedCount = 1;

    public function setProductsReturnedCount($productsReturnedCount) {
        $this->_productsReturnedCount = $productsReturnedCount;
    }

    protected $_productsCount = 1;

    public function setProductsCount($productsCount) {
        $this->_productsCount = $productsCount;
    }

    public function __construct($options = null) {
        $this->addPrefixPath('Application_Form', 'Application/Form');
        parent::__construct($options);
    }

    public function setProducts($config) {
        for ($i = 0; $i <= $this->_productsCount; $i++) {
            $element = $this->getElement('productid-' . $i);
            if (!$element) {
                return;
            }
            //$element->addMultiOption(null, 'Wybierz opcję...');
            foreach ($config as $product) {
                $desc = '';
                if (!empty($product->serial)) {
                    $desc = $product->serial . ', ';
                }
                $desc .= max(0, $product->qtyavailable) . ' ' . $product->unitacronym;
                $element->addMultiOption($product->id, 
                        $desc ? $product->product . ' (' . $desc . ')' : $desc, 
                        array('data-serial' => $product->serial));
            }
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        $selected = array();
        foreach ($config as $product) {
            $desc = '';
            if (!empty($product->serial)) {
                $desc = $product->serial . ', ';
            }
            $desc .= max(0, $product->qtyavailable) . ' ' . $product->unitacronym;
            $element->addMultiOption($product->id, 
                    $desc ? $product->product . ' (' . $desc . ')' : $desc, 
                    array('data-serial' => $product->serial));
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

    public function setErrorcodes($config) {
        $element = $this->getElement('errorcodeid');
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setSolutioncodes($config) {
        $element = $this->getElement('solutioncodeid');
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            if (!$parent['errorcodeacronym']) {
                continue;
            }
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['errorcodename'] . ' / ' . $parent['name']
                ,array('data-errorcodeid' => $parent['errorcodeid'], 'data-errorcode' => $parent['errorcodeacronym'], 
                    'data-solutioncodeid' => $parent['id'], 'data-solutioncode' => $parent['acronym'])
            );
        }
    }

    public function setCancellationcodes($config) {
        $element = $this->getElement('cancellationcodeid');
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setModeminterchangecodes($config) {
        $element = $this->getElement('modeminterchangecodeid');
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setDecoderinterchangecodes($config) {
        $element = $this->getElement('decoderinterchangecodeid');
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setDemagecodes($config) {
        for ($i = 0; $i <= $this->_productsReturnedCount; $i++) {
            $element = $this->getElement('demagecodeid-' . $i);
            if (!$element) {
                return;
            }
            $element->addMultiOption(null, 'Kod uszkodzenia');
            foreach ($config as $parent) {
                $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
            }
        }
    }

    public function setCatalog($config) {
        for ($i = 0; $i <= $this->_productsReturnedCount; $i++) {
            $element = $this->getElement('catalogid-' . $i);
            if (!$element) {
                return;
            }
            $element->addMultiOption(null, 'Model');
            foreach ($config as $parent) {
                $element->addMultiOption($parent['id'], $parent['name']);
            }
        }
    }

    public function setDefault($name, $value) {
        $name = (string) $name;
        if (strpos($name, 'productreturnedid') !== false) {
            $selectedIds = array();
            if (!$value['name']) {
                $value['name'] = current($value);
            }
            $selectedIds[] = $value['name'];
            $attribs = $this->getElement($name)->getAttribs();
            $options = $attribs['options'];
            if (!isset($options[$value['name']])) {
                $this->getElement($name)->addMultiOption($value['name'], $value['name']);
                $selectedIds[] = $value['name'];
            }
            preg_match("/\d+/", $name, $found);
            $this->getElement('demaged-' . $found[0])->setValue($value['demaged']);
            $this->getElement('demagecodeid-' . $found[0])->setValue($value['demagecodeid']);
            $this->getElement('catalogid-' . $found[0])->setValue($value['catalogid']);
            if ($value['statusacronym'] && $value['statusacronym'] != 'new') {
                $this->getElement('demaged-' . $found[0])->setAttrib('disabled', 'disabled')->setAttrib('readonly', 'readonly');
                $this->getElement('productreturnedid-' . $found[0])->setAttrib('disabled', 'disabled')->setAttrib('readonly', 'readonly');
                $this->getElement('demagecodeid-' . $found[0])->setAttrib('disabled', 'disabled')->setAttrib('readonly', 'readonly');
                $this->getElement('catalogid-' . $found[0])->setAttrib('disabled', 'disabled')->setAttrib('readonly', 'readonly');
            }
            $value = array_unique($selectedIds);
        }
	if (strpos($name, 'productid') !== false) {
            $selectedIds = array();
            if (!$value['productid']) {
                $value['productid'] = current($value);
            }
            if (!$value['name']) {
                $value['name'] = current($value);
            }
            $selectedIds[] = $value['productid'];
            $attribs = $this->getElement($name)->getAttribs();
            $options = $attribs['options'];
            if (!isset($options[$value['productid']])) {
                $this->getElement($name)->addMultiOption($value['productid'] < 0 ? $value['name'] : $value['productid'], $value['name'] . ' (' . $value['serial'] . ')');
                $selectedIds[] = $value['productid'] < 0 ? $value['name'] : $value['productid'];
            }
            preg_match("/\d+/", $name, $found);
            $this->getElement('quantity-' . $found[0])->setValue($value['quantity']);
            $value = array_unique($selectedIds);
        }
        switch ($name) {
            case 'productid':
                $selectedIds = array();
                foreach ($value as $row) {
                    $selectedIds[] = $row['productid'];
                    $attribs = $this->getElement($name)->getAttribs();
                    $options = $attribs['options'];
                    if (!isset($options[$row['productid']])) {
                        $this->getElement($name)->addMultiOption($row['productid'] < 0 ? $row['name'] : $row['productid'], $row['name'] . ' (' . $row['serial'] . ')');
                        $selectedIds[] = $row['productid'] < 0 ? $row['name'] : $row['productid'];
                    }
                }
                $value = $selectedIds;
                break;
            case 'installationcodeid':
            case 'errorcodeid':
            case 'solutioncodeid':
            case 'cancellationcodeid':
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

    public function init() {
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
        $element->setValue('');
        $this->addElement($element);
        $this->setDefault('performed', '');
        
        $element = $this->createElement('datePicker', 'datefinished', array(
            'label'      => 'Godzina zakończenia:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'cancellationcodeid', array(
                    'label' => 'Kod odwołania:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array(/*'multiple' => 'multiple', */'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        $element = $this->createElement('select', 'errorcodeid', array(
                    'label' => 'Kod błędu:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        /* $element = /*$this->createElement('select', 'solutioncodeid', array(
          'label'      => 'Kod rozwiązania:',
          //'required'   => true,
          //'filters'    => array('StringTrim'),
          //'validators' => array(
          //    array('lessThan', true, array('score')),
          //),
          'class' => 'form-control chosen-select',
          )) */
        $element = new Application_Form_Element_SelectAttribs('solutioncodeid', array(
            'label' => 'Kod rozwiązania:',
            'class' => 'form-control chosen-select',
        ));
        $element->setAttribs(array('placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        $element = $this->createElement('select', 'modeminterchangecodeid', array(
                    'label' => 'Kod wymiany modemu:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array(/*'multiple' => 'multiple', */'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        $element = $this->createElement('select', 'decoderinterchangecodeid', array(
                    'label' => 'Kod wymiany dekodera:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array(/*'multiple' => 'multiple', */'placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);
        
        $element = new Application_Form_Element_SelectAttribs('productid-0', array(
                    'label' => 'Sprzęt wydany:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'productid',
                    'class' => 'form-control chosen-select',
                ));
        $element->setAttribs(array('multiple' => 'multiple', 'style' => 'max-width: 75%;'))->setRegisterInArrayValidator(false);
        $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
        $this->addElement($element);
        $element = $this->createElement('text', 'quantity-0', array(
            'belongsTo' => 'quantity',
            'class' => 'form-group input-sm',
        ))->setAttribs(array('style' => 'width: 50px;'));
        $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
        $element->addDecorator('Label', array('tag' => ''));
        $this->addElement($element);
        
        for ($i = 1; $i <= $this->_productsCount; $i++) {
            $element = new Application_Form_Element_SelectAttribs('productid-' . $i, array(
                    //'label' => 'Produkty:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'productid',
                    'class' => 'form-control chosen-select',
                ));
            $element->setAttribs(array('multiple' => 'multiple', 'style' => 'max-width: 75%;'))->setRegisterInArrayValidator(false);
            $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => ''));
            $this->addElement($element);
            $element = $this->createElement('text', 'quantity-' . $i, array(
                'belongsTo' => 'quantity',
                'class' => 'form-group input-sm',
            ))->setAttribs(array('style' => 'width: 50px;'));
            $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => ''));
            $this->addElement($element);
        }

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
        
        $element = $this->createElement('hidden','return',array('label' => 'Sprzęt odebrany:'));
        $element->addDecorator('Label', array('tag' => 'span', 'placement' => 'prepend'));
        $this->addElement($element);
        $element = $this->createElement('select', 'catalogid-0', array(
                    'belongsTo' => 'catalogid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('placeholder' => 'Nazwa katalogowa', 'style' => 'max-width: 25%;'))->setRegisterInArrayValidator(false);
        $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
        $element->removeDecorator('Label');
        $this->addElement($element);
        $element = $this->createElement('select', 'productreturnedid-0', array(
                    //'label' => 'Sprzęt odebrany:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'productreturnedid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'style' => 'max-width: 25%;'))->setRegisterInArrayValidator(false);
        $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
        $element->removeDecorator('Label');
        $this->addElement($element);
        $element = $this->createElement('checkbox', 'demaged-0', array(
            'label' => 'Uszkodzony:',
            'belongsTo' => 'demaged',
            'class' => 'form-group input-small',
        ))->setAttribs(array('style' => 'width: ;'));
        $element->addDecorator('HtmlTag', array('tag' => 'span', 'class' => 'form-group inline'));
        $element->addDecorator('Label', array('tag' => 'span', 'placement' => 'prepend'));
        $this->addElement($element);
        $element = $this->createElement('select', 'demagecodeid-0', array(
                    'label' => 'Kod:',
                        //'required'   => true,
                        //'filters'    => array('StringTrim'),
                        //'validators' => array(
                        //    array('lessThan', true, array('score')),
                        //),
                    'belongsTo' => 'demagecodeid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('placeholder' => 'Kod uszkodzenia', 'style' => 'max-width: 25%;'))->setRegisterInArrayValidator(false);
        $element->addDecorator('HtmlTag', array('tag' => 'span', 'class' => 'form-group inline'));
        $element->addDecorator('Label', array('tag' => 'span', 'placement' => 'prepend'));
        $element->removeDecorator('Label');
        $this->addElement($element);
        $this->addDisplayGroup(array('catalogid-0', 'productreturnedid-0', 'demaged-0', 'demagecodeid-0'), 'return-0')->setAttribs(array('id' => 'return-0'));

        for ($i = 1; $i <= $this->_productsReturnedCount; $i++) {
            $element = $this->createElement('select', 'catalogid-' . $i, array(
                        'belongsTo' => 'catalogid',
                        'class' => 'form-control chosen-select',
                    ))->setAttribs(array('placeholder' => 'Nazwa katalogowa', 'style' => 'max-width: 25%;'))->setRegisterInArrayValidator(false);
            $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
            $element->removeDecorator('Label');
            $this->addElement($element);
            $element = $this->createElement('select', 'productreturnedid-' . $i, array(
                    //'label' => 'Produkty:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'productreturnedid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'style' => 'max-width: 25%;'))->setRegisterInArrayValidator(false);
            $element->addDecorator('HtmlTag', array('tag' => 'dd', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => ''));
            $this->addElement($element);
            $element = $this->createElement('checkbox', 'demaged-' . $i, array(
                'label' => 'Uszkodzony:',
                'belongsTo' => 'demaged',
                'class' => 'form-group input-small',
            ))->setAttribs(array('style' => 'width: ;'));
            $element->addDecorator('HtmlTag', array('tag' => 'span', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => 'span', 'placement' => 'prepend'));
            $this->addElement($element);
            $element = $this->createElement('select', 'demagecodeid-' . $i, array(
                        'label' => 'Kod:',
                        //'required'   => true,
                        //'filters'    => array('StringTrim'),
                        //'validators' => array(
                        //    array('lessThan', true, array('score')),
                        //),
                        'belongsTo' => 'demagecodeid',
                        'class' => 'form-control chosen-select',
                    ))->setAttribs(array('placeholder' => 'Kod uszkodzenia', 'style' => 'max-width: 25%;'))->setRegisterInArrayValidator(false);
            $element->addDecorator('HtmlTag', array('tag' => 'span', 'class' => 'form-group inline'));
            $element->addDecorator('Label', array('tag' => 'span', 'placement' => 'prepend'));
            $element->removeDecorator('Label');
            $this->addElement($element);
            $this->addDisplayGroup(array('catalogid-' . $i, 'productreturnedid-' . $i, 'demaged-' . $i, 'demagecodeid-' . $i), 'return-' . $i)->setAttribs(array('id' => 'return-' . $i));
        }

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
        $this->setOptions(array('class' => 'form-load'));
    }

}
