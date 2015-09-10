<?php

require_once 'Zend/Cache/Exception.php';

require_once 'Zend/Cache.php';

class Zend_Cache_Manager {

    const PAGECACHE = 'page';
    const PAGETAGCACHE = 'pagetag';

    protected $_caches = array();
    protected $_optionTemplates = array(
        // Null Cache (Enforce Null/Empty Values)
        'skeleton' => array(
            'frontend' => array(
                'name' => null,
                'options' => array(),
            ),
            'backend' => array(
                'name' => null,
                'options' => array(),
            ),
        ),
        // Simple Common Default
        'default' => array(
            'frontend' => array(
                'name' => 'Core',
                'options' => array(
                    'automatic_serialization' => true,
                ),
            ),
            'backend' => array(
                'name' => 'File',
                'options' => array(
                    'cache_dir' => '../cache',
                ),
            ),
        ),
        // Static Page HTML Cache
        'page' => array(
            'frontend' => array(
                'name' => 'Capture',
                'options' => array(
                    'ignore_user_abort' => true,
                ),
            ),
            'backend' => array(
                'name' => 'Static',
                'options' => array(
                    'public_dir' => '../public',
                ),
            ),
        ),
        // Tag Cache
        'pagetag' => array(
            'frontend' => array(
                'name' => 'Core',
                'options' => array(
                    'automatic_serialization' => true,
                    'lifetime' => null
                ),
            ),
            'backend' => array(
                'name' => 'File',
                'options' => array(
                    'cache_dir' => '../cache',
                    'cache_file_umask' => 0644
                ),
            ),
        ),
    );

    public function setCache($name, Zend_Cache_Core $cache) {
        $this->_caches[$name] = $cache;
        return $this;
    }

    public function hasCache($name) {
        if (isset($this->_caches[$name]) || $this->hasCacheTemplate($name)
        ) {
            return true;
        }
        return false;
    }

    public function getCache($name) {
        if (isset($this->_caches[$name])) {
            return $this->_caches[$name];
        }
        if (isset($this->_optionTemplates[$name])) {
            if ($name == self::PAGECACHE && (!isset($this->_optionTemplates[$name]['backend']['options']['tag_cache']) || !$this->_optionTemplates[$name]['backend']['options']['tag_cache'] instanceof Zend_Cache_Core)
            ) {
                $this->_optionTemplates[$name]['backend']['options']['tag_cache'] = $this->getCache(self::PAGETAGCACHE);
            }
            $this->_caches[$name] = Zend_Cache::factory(
                            $this->_optionTemplates[$name]['frontend']['name'], $this->_optionTemplates[$name]['backend']['name'], isset($this->_optionTemplates[$name]['frontend']['options']) ? $this->_optionTemplates[$name]['frontend']['options'] : array(), isset($this->_optionTemplates[$name]['backend']['options']) ? $this->_optionTemplates[$name]['backend']['options'] : array(), isset($this->_optionTemplates[$name]['frontend']['customFrontendNaming']) ? $this->_optionTemplates[$name]['frontend']['customFrontendNaming'] : false, isset($this->_optionTemplates[$name]['backend']['customBackendNaming']) ? $this->_optionTemplates[$name]['backend']['customBackendNaming'] : false, isset($this->_optionTemplates[$name]['frontendBackendAutoload']) ? $this->_optionTemplates[$name]['frontendBackendAutoload'] : false
            );
            return $this->_caches[$name];
        }
    }

    public function setCacheTemplate($name, $options) {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            require_once 'Zend/Cache/Exception.php';
            throw new Zend_Cache_Exception('Options passed must be in'
            . ' an associative array or instance of Zend_Config');
        }
        $this->_optionTemplates[$name] = $options;
        return $this;
    }

    public function hasCacheTemplate($name) {
        if (isset($this->_optionTemplates[$name])) {
            return true;
        }
        return false;
    }

    public function getCacheTemplate($name) {
        if (isset($this->_optionTemplates[$name])) {
            return $this->_optionTemplates[$name];
        }
    }

    public function setTemplateOptions($name, $options) {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        } elseif (!is_array($options)) {
            require_once 'Zend/Cache/Exception.php';
            throw new Zend_Cache_Exception('Options passed must be in'
            . ' an associative array or instance of Zend_Config');
        }
        if (!isset($this->_optionTemplates[$name])) {
            throw new Zend_Cache_Exception('A cache configuration template'
            . 'does not exist with the name "' . $name . '"');
        }
        $this->_optionTemplates[$name] = $this->_mergeOptions($this->_optionTemplates[$name], $options);
        return $this;
    }

    protected function _mergeOptions(array $current, array $options) {
        if (isset($options['frontend']['name'])) {
            $current['frontend']['name'] = $options['frontend']['name'];
        }
        if (isset($options['backend']['name'])) {
            $current['backend']['name'] = $options['backend']['name'];
        }
        if (isset($options['frontend']['options'])) {
            foreach ($options['frontend']['options'] as $key => $value) {
                $current['frontend']['options'][$key] = $value;
            }
        }
        if (isset($options['backend']['options'])) {
            foreach ($options['backend']['options'] as $key => $value) {
                $current['backend']['options'][$key] = $value;
            }
        }
        return $current;
    }

}
