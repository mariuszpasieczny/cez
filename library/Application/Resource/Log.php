<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/ResourceAbstract.php';


/**
 * Resource for initializing logger
 *
 * @uses       Zend_Application_Resource_ResourceAbstract
 * @category   Zend
 * @package    Zend_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Application_Resource_Log extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Zend_Log
     */
    protected $_log;

    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Log
     */
    public function init()
    {
        return $this->getLog();
    }

    /**
     * Attach logger
     *
     * @param  Zend_Log $log
     * @return Zend_Application_Resource_Log
     */
    public function setLog(Zend_Log $log)
    {
        $this->_log = $log;
        return $this;
    }

    /**
     * Retrieve logger object
     *
     * @return Zend_Log
     */
    public function getLog()
    {
        if (null === $this->_log) {
            $config = $this->getOptions();
            if ($config instanceof Zend_Config) {
                $config = $config->toArray();
            }

            if (!is_array($config) || empty($config)) {
                /** @see Zend_Log_Exception */
                require_once 'Zend/Log/Exception.php';
                throw new Zend_Log_Exception('Configuration must be an array or instance of Zend_Config');
            }

            if (array_key_exists('className', $config)) {
                $class = $config['className'];
                unset($config['className']);
            } else {
                $class = __CLASS__;
            }

            $log = new $class;

            if (!$log instanceof Zend_Log) {
                /** @see Zend_Log_Exception */
                require_once 'Zend/Log/Exception.php';
                throw new Zend_Log_Exception('Passed className does not belong to a descendant of Zend_Log');
            }

            if (array_key_exists('timestampFormat', $config)) {
                if (null != $config['timestampFormat'] && '' != $config['timestampFormat']) {
                    $log->setTimestampFormat($config['timestampFormat']);
                }
                unset($config['timestampFormat']);
            }

            if (!is_array(current($config))) {
                $log->addWriter(current($config));
            } else {
                foreach($config as $params) {
                    switch ($params['writerName']) {
                        case 'Stream':
                            $writer = new Zend_Log_Writer_Stream($params['writerParams']['stream'], $params['writerParams']['mode']);
                            break;
                        default:
                            require_once 'Zend/Log/Exception.php';
                            throw new Zend_Log_Exception('Passed writerName does not belong to a descendant of Zend_Log_Writer');
                    }
                    $log->addWriter($writer);
                    if (isset($params['filterName'])) {
                        $log->{'add'.$params['filterName']}($params['filterParams']['priority']);
                    }
                }
            }
            $this->setLog($log);
        }
        return $this->_log;
    }
}
