<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class Application_Form_Services_Service extends Application_Form {

    protected $_defaultDisplayGroupClass = 'Application_Form_DisplayGroup';
    protected $_productsCount = 1;

    public function setProductsCount($productsCount) {
        $this->_productsCount = $productsCount;
    }

    public function __construct($options = null) {
        $this->addPrefixPath('Application_Form', 'Application/Form');
        parent::__construct($options);
    }

    public function setDefaults(array $defaults) {
        parent::setDefaults($defaults);
        $dictionary = new Application_Model_Dictionaries_Table();
        $status = $dictionary->getStatusList('service')->find($defaults['statusid']);
        if (empty($defaults['id'])) {
            return $this;
            $dissallowedElements = array('datefinished',
                'productid',
                'productreturnedid',
                'cancellationcodeid',
                'solutioncodeid',
                'modeminterchangecodeid',
                'decoderinterchangecodeid',
                'technicalcomments');
        } else {
            $dissallowedElements = array('number',
                'warehouseid',
                'clientid',
                'servicetypeid',
                'planneddate',
                'timefrom',
                'timetill',
                'regionid',
                'laborcodeid',
                'complaintcodeid',
                'areaid',
                'calendarid',
                'comments',
                'slots',
                'products',
                'serials',
                'macnumbers',
                'technicianid',
                'statusid');
        }
        /*switch ($status->acronym) {
            case 'new':
            case 'assigned':
                $dissallowedElements = array('installationcodeid[]',
                    'productid[]',
                    'technicalcomments',
                    'coordinatorcomments');
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
        for ($i = 0; $i <= $this->_productsCount; $i++) {
            $element = $this->getElement('productid-' . $i);
            if (!$element) {
                return;
            }
            //$element->addMultiOption(null, 'Wybierz opcję...');
            foreach ($config as $parent) {
                $desc = '';
                if ($product = $parent->getProduct()) {
                    $desc = $product->name . ' (';
                    if ($serial = $product->serial) {
                        $desc .= $product->serial . ', ';
                    }
                    $desc .= $parent->qtyavailable . ' ' . $product->getUnit()->acronym . ')';
                } else {
                    $desc .= 'brak danych';
                }
                $element->addMultiOption($parent['id'], $desc);
            }
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
        $element->addMultiOption(null, 'Wybierz opcję...');
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
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name']);
        }
    }

    public function setClients($config) {
        if (!$element = $this->getElement('clientid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['city'] . ', ' . $parent['street'] . ' ' . $parent['streetno'] . '/' . $parent['apartmentno']);
        }
    }

    public function setServicetypes($config) {
        if (!$element = $this->getElement('servicetypeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name'] ? $parent['name'] : $parent['acronym']);
        }
    }

    public function setTechnicians($config) {
        if (!$element = $this->getElement('technicianid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['firstname'] . ' ' . $parent['lastname']);
        }
    }

    public function setRegions($config) {
        if (!$element = $this->getElement('regionid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name'] ? $parent['name'] : $parent['acronym']);
        }
    }

    public function setLaborcodes($config) {
        if (!$element = $this->getElement('laborcodeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name'] ? $parent['name'] : $parent['acronym']);
        }
    }

    public function setComplaintcodes($config) {
        if (!$element = $this->getElement('complaintcodeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name'] ? $parent['name'] : $parent['acronym']);
        }
    }

    public function setAreas($config) {
        if (!$element = $this->getElement('areaid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name'] ? $parent['name'] : $parent['acronym']);
        }
    }

    public function setCalendars($config) {
        if (!$element = $this->getElement('calendarid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['name'] ? $parent['name'] : $parent['acronym']);
        }
    }

    public function setErrorcodes($config) {
        if (!$element = $this->getElement('errorcodeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setSolutioncodes($config) {
        if (!$element = $this->getElement('solutioncodeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            if (empty($parent['errorcodeacronym'])) {
                continue;
            }
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['errorcodename'] . ' / ' . $parent['name']
                ,array('data-errorcodeid' => $parent['errorcodeid'], 'data-errorcode' => $parent['errorcodeacronym'], 
                    'data-solutioncodeid' => $parent['id'], 'data-solutioncode' => $parent['acronym'])
            );
        }
    }

    public function setCancellationcodes($config) {
        if (!$element = $this->getElement('cancellationcodeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setModeminterchangecodes($config) {
        if (!$element = $this->getElement('modeminterchangecodeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setDecoderinterchangecodes($config) {
        if (!$element = $this->getElement('decoderinterchangecodeid')) {
            return;
        }
        $element->addMultiOption(null, 'Wybierz opcję...');
        foreach ($config as $parent) {
            $element->addMultiOption($parent['id'], $parent['acronym'] . ' - ' . $parent['name']);
        }
    }

    public function setDefault($name, $value) {
        $name = (string) $name;
        if (strpos($name, 'productid') !== false) {
            $selectedIds = array();
            $selectedIds[] = $value['productid'];
            $attribs = $this->getElement($name)->getAttribs();
            $options = $attribs['options'];
            if (!isset($options[$value['productid']])) {
                $this->getElement($name)->addMultiOption($value['productid'] < 0 ? $value['name'] : $value['productid'], $value['name']/* . ' (' . $value['serial'] . ')'*/);
                $selectedIds[] = $value['productid'] < 0 ? $value['name'] : $value['productid'];
            }
            preg_match("/\d+/", $name, $found);
            $this->getElement('quantity-' . $found[0])->setValue($value['quantity']);
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
          'label'      => 'Typ zgłoszenia:',
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

        $element = $this->createElement('select', 'laborcodeid', array(
            'label' => 'Kod pracy:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'complaintcodeid', array(
            'label' => 'Kod skargi:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'areaid', array(
            'label' => 'Rejon:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('select', 'calendarid', array(
            'label' => 'Kalendarz:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //    array('lessThan', true, array('score')),
            ),
            'class' => 'form-control chosen-select',
        ));
        $this->addElement($element);

        $element = $this->createElement('textarea', 'comments', array(
                    'label' => 'Komentarz:',
                    //'required'   => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control',
                ))->setAttrib('ROWS', '4');
        $this->addElement($element);

        $element = $this->createElement('textarea', 'slots', array(
                    'label' => 'Gniazda:',
                    //'required'   => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control',
                ))->setAttrib('ROWS', '2');
        $this->addElement($element);

        $element = $this->createElement('textarea', 'products', array(
                    'label' => 'Posiadane produkty:',
                    //'required'   => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control',
                ))->setAttrib('ROWS', '4');
        $this->addElement($element);

        $element = $this->createElement('textarea', 'serials', array(
                    'label' => 'Numery seryjne:',
                    //'required'   => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control',
                ))->setAttrib('ROWS', '2');
        $this->addElement($element);

        $element = $this->createElement('textarea', 'macnumbers', array(
                    'label' => 'Numery MAC:',
                    //'required'   => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                    //'NotEmpty',
                    ),
                    'class' => 'form-control',
                ))->setAttrib('ROWS', '2');
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

        $element = $this->createElement('datePicker', 'datefinished', array(
            'label' => 'Godzina zakończenia:',
            //'required'   => true,
            'filters' => array('StringTrim'),
            'validators' => array(
            //'NotEmpty',
            ),
            'class' => 'form-control',
        ));
        $this->addElement($element);
        
        $element = new Application_Form_Element_SelectAttribs('solutioncodeid', array(
            'label' => 'Kod rozwiązania:',
            'class' => 'form-control chosen-select',
        ));
        $element->setAttribs(array('placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);
        
        $element = $this->createElement('select', 'productid-0', array(
                    'label' => 'Produkty:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'productid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'style' => 'max-width: 65%;'))->setRegisterInArrayValidator(false);
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
            $element = $this->createElement('select', 'productid-' . $i, array(
                    //'label' => 'Produkty:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'belongsTo' => 'productid',
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('multiple' => 'multiple', 'style' => 'max-width: 65%;'))->setRegisterInArrayValidator(false);
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

        $element = $this->createElement('select', 'cancellationcodeid', array(
                    'label' => 'Kod odwołania:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
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

        /* $element = $this->createElement('select', 'solutioncodeid', array(
          'label'      => 'Kod rozwiązania:',
          //'required'   => true,
          //'filters'    => array('StringTrim'),
          //'validators' => array(
          //    array('lessThan', true, array('score')),
          //),
          'class' => 'form-control chosen-select',
          )) */

        $element = $this->createElement('select', 'modeminterchangecodeid', array(
                    'label' => 'Kod wymiany modemu:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
        $this->addElement($element);

        $element = $this->createElement('select', 'decoderinterchangecodeid', array(
                    'label' => 'Kod wymiany dekodera:',
                    //'required'   => true,
                    //'filters'    => array('StringTrim'),
                    //'validators' => array(
                    //    array('lessThan', true, array('score')),
                    //),
                    'class' => 'form-control chosen-select',
                ))->setAttribs(array('placeholder' => 'Choose product'))->setRegisterInArrayValidator(false);
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
                    'label' => 'Komenatrz koordynatora:',
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
