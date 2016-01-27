<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
define('APPLICATION_ENVIRONMENT', 'development');

/**
 * Setup for includes
 */
set_include_path(
        APPLICATION_PATH . '/../library' . PATH_SEPARATOR .
        get_include_path());


/**
 * Zend Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();

/**
 * Register my Namespaces for the Autoloader
 */
$autoloader->registerNamespace('Application_');

/**
 * Setup the CLI Commands
 * ../application/cli.php --add
 * ../application/cli.php --scan
 * ..
 */
try {
    $opts = new Zend_Console_Getopt(
            array(
        'help|h' => 'Displays usage information',
        'env|e-s' => 'defines application environment (defaults to "development")',
        'verbose|v' => 'VERBOSE',
        'update|u' => '(optional) updates application',
        'force|f' => '(optional) force install (continue on errrors)',
        'reinstall|r' => '(optional) reinstall (drop tables)',
        'login|l=s' => '(required) username',
        'password|p=s' => '(required) password',
        'output|o=s' => '(required) output log',
            )
    );

    $opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
    exit($e->getMessage() . "\n\n" . $e->getUsageMessage());
}

if (isset($opts->help)) {
    echo $opts->getUsageMessage();
    exit;
}

// initialize values based on presence or absence of CLI options
$env = $opts->getOption('e');
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (null === $env) ? 'development' : $env);

$login = $opts->getOption('login');
$password = $opts->getOption('password');

try {
    $filename = APPLICATION_PATH . '/configs/htpasswd';
    $realm = 'superadmin';
    $adapter = new Zend_Auth_Adapter_Digest($filename, $realm, $login, $password);
    $result = $adapter->authenticate();
    if (!$result->isValid()) {
        throw new Exception(join("\n", $result->getMessages()), $result->getCode());
    }
    // initialize Zend_Application
    $application = new Zend_Application(
            APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini'
    );
    // bootstrap and retrive the frontController resource
    $bootstrap = $application->getBootstrap();
    $dbAdapter = $bootstrap->bootstrap('db')->getResource('db');
    //require_once APPLICATION_PATH . '/../library/Zend/Config/Json.php';
    //$handle = fopen(APPLICATION_PATH . '/configs/database.sql', 'w+');

    $installer = new Installer($dbAdapter, $opts->getOptions());
    if ($log = $opts->getOption('output')) {
        $installer->setLog($log);
    }
    //$dbConfig = new Zend_Config_Json(
    //        APPLICATION_PATH . '/configs/database.json'
    //);
    //$installer->createStructure($dbConfig);
    $dataConfig = new Zend_Config_Json(
            APPLICATION_PATH . '/configs/data.json'
    );
    $installer->createData($dataConfig);
    //fclose($handle);
} catch (Exception $ex) {
    echo "{$ex->getCode()}: {$ex->getMessage()}\n";
}

exit;

class Installer {

    protected $_db;
    protected $_options;
    protected $_log;

    public function __construct($db, $options) {
        $this->_db = $db;
        $this->_options = $options;
        $this->_log = new Zend_Log();
        $this->_log->addWriter(new Zend_Log_Writer_Null);
        if ($this->getOption('verbose')) {
            $this->_log->addWriter(new Zend_Log_Writer_Stream('php://output'));
        }
    }

    public function getOption($value) {
        return in_array($value, $this->_options);
    }

    public function getLog() {
        return $this->_log;
    }

    public function setLog($log) {
        $this->_log->addWriter(new Zend_Log_Writer_Stream(APPLICATION_PATH . '/' . $log, 'w+'));
    }

    public function execute($sql) {
        try {
            $this->_log->info($sql);
            $this->_db->query($sql);
        } catch (Exception $ex) {
            if (!$this->getOption('force')) {
                throw new Exception($ex->getMessage(), $ex->getCode());
            }
            $this->_log->err($ex->getMessage());
        }
    }

    public function createStructure($spec) {
        foreach ($spec as $tableName => $data) {
            if ($this->getOption('reinstall')) {
                $sql = "DROP TABLE IF EXISTS $tableName;\n";
                $this->execute($sql);
            }
            $tables = $this->_db->listTables();
            if (in_array(strtolower($tableName), $tables)) {
                $columns = $this->_db->describeTable($tableName);
                foreach ($data->columns as $columnName => $column) {
                    if (in_array(strtolower($columnName), array_keys($columns))) {
                        continue;
                    }
                    $sql = $this->addColumn($tableName, $column);
                    $this->execute($sql);
                }
                continue;
            }
            $this->createTable($tableName, $data);
        }
    }

    public function updateStructure($spec) {
        foreach ($spec as $tableName => $data) {
            $this->updateTable($tableName, $data);
        }
    }

    public function createData($spec) {
        foreach ($spec as $tableName => $data) {
            if ($this->getOption('reinstall')) {
                $sql = "TRUNCATE TABLE $tableName;\n";
                $this->execute($sql);
            }
            $this->insertData($tableName, $data, 0);
        }
    }

    public function insertData($tableName, $data, $parentid) {
        $select = "SELECT * FROM $tableName WHERE parentid = '$parentid'";
        $params = array('parentid' => $parentid);
        foreach ($data as $key => $value) {
            if (!is_string($value)) {
                continue;
            }
            $select .= " AND $key = '$value'";
            $params[$key] = $value;
        }
        $select .= ";\n";
        //$this->_log->info($select);
        if (!($entry = $this->_db->query($select)->fetch())) {
            $insert = "INSERT INTO $tableName";
            if (!empty($params)) {
                $insert .= " (" . join(", ", array_keys($params)) . ") VALUES ('" . join("', '", $params) . "');\n";
            }
            $this->execute($insert);
            $entry = $this->_db->query($select)->fetch();
        }
        if (is_object($data)) {
            foreach ($data as $index => $row) {
                if (is_object($row)) {
                    $this->insertData($tableName, $row, $entry['id']);
                }
            }
        }
    }

    public function createTable($tableName, $spec) {
        $sql .= "CREATE TABLE $tableName (\n";
        $i = 0;
        foreach ($spec as $columName => $data) {
            if (in_array($columName, array('engine', 'charset', 'primaryKey'))) {
                $$columName = $data;
                continue;
            }
            if ($i > 0) {
                $sql .= ", ";
            }
            if (is_object($data)) {
                $j = 0;
                foreach ($data as $key => $value) {
                    if ($j > 0) {
                        $sql .= ",\n";
                    }
                    $sql .= $this->column($value);
                    $j++;
                    $i++;
                }
            }
        }
        $sql .= ",\nPRIMARY KEY (`$primaryKey`)) ENGINE='{$engine}' DEFAULT CHARSET='{$charset}';\n";
        $this->execute($sql);
    }

    public function updateTable($tableName, $spec) {
        foreach ($spec as $columName => $data) {
            if (is_object($data)) {
                foreach ($data as $key => $value) {
                    $sql = $this->addColumn($tableName, $value);
                    $this->execute($sql);
                }
            }
        }
    }

    public function addColumn($tableName, $spec) {
        $sql = "ALTER TABLE $tableName ADD COLUMN ";
        $sql .= $this->column($spec);
        $sql .= ";\n";
        return $sql;
    }

    public function column($spec) {
        $sql .= "`{$spec->name}` {$spec->type}" . ($spec->length ? "({$spec->length})" : "");
        $sql .= $spec->notNull ? " NOT NULL" : " DEFAULT NULL";
        $sql .= $spec->autoIncrement ? " AUTO_INCREMENT" : "";
        $sql .= $spec->defaultValue == 'CURRENT_TIMESTAMP' ? " DEFAULT {$spec->defaultValue}" : ($spec->defaultValue ? " DEFAULT '{$spec->defaultValue}'" : "");
        $sql .= $spec->comment ? " COMMENT  '{$spec->comment}'" : "";
        return $sql;
    }

}
