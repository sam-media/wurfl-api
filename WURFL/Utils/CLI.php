<?php

class WURFL_Utils_CLI {
	
	/**
	 * @var WURFL_Utils_CLI_Argument_Collection
	 */
	protected $arguments;
	/**
	 * @var $wurflManager WURFL_WURFLManager
	 */
	protected $wurfl;
	
	public function __construct($wurfl=null) {
		error_reporting(E_ALL);
		$this->arguments = WURFL_Utils_CLI_ArgumentFactory::createArgumentCollection();
		$this->requireAdditionalClasses();
		if ($wurfl !== null) {
			$this->wurfl = $wurfl;
		} else {
			$this->createWurflClass();
		}
	}
	
	public function processArguments() {
		if ($this->arguments->isEmpty()) {
			$this->arguments->add(new WURFL_Utils_CLI_Argument('help'));
		}
		foreach ($this->arguments as $arg) {
			$action = 'action'.ucfirst($arg->command);
			if (!method_exists($this, $action)) {
				continue;
			}
			$this->$action($arg);
		}
	}

	protected function actionCentralTest(WURFL_Utils_CLI_Argument $arg) {
		$test_type = $arg->value;
		require_once dirname(__FILE__) . '/../../tests/CentralTest/CentralTestManager.php';
		$centralTest = new CentralTestManager($this->wurfl);
        //TODO: Add introspector support
		if ($this->arguments->introspector) {
			if ($this->arguments->username && $this->arguments->password) {
				$centralTest->useIntrospector($this->arguments->introspector->value, $this->arguments->username->value, $this->arguments->password->value);
			} else {
				$centralTest->useIntrospector($this->arguments->introspector->value);
			}
		}
		$centralTest->show_success = false;
		if (preg_match('#(single/.*)$#', $test_type, $matches)) {
			$centralTest->runSingleTest($matches[1]);
		} else {
			$centralTest->runBatchTest($test_type);
		}
	}
	

	protected function actionHelp(WURFL_Utils_CLI_Argument $arg) {
		$api_version = WURFL_Constants::API_VERSION;
		$wurfl_version = $this->wurfl->getWURFLInfo()->version;
		$last_updated = $this->wurfl->getWURFLInfo()->lastUpdated;
		$usage =<<<EOL

ScientiaMobile WURFL PHP API $api_version
Command Line Interface
Loaded WURFL: $wurfl_version
Last Updated: $last_updated
---------------------------------------
Usage: php wurfl_cli.php [OPTIONS]

Option                     Meaning
 --help                    Show this message
 --centralTest=<unit|regression|all|single/<test_name>>
                           Run tests from the ScientiaMobile Central
                             testing repository.

EOL;
		echo $usage;
	}
	
	protected function requireAdditionalClasses() {
		if (!$this->arguments->require) return;
		require_once $this->arguments->require->value;
	}
	
	protected function createWurflClass() {
		if ($this->arguments->altClass) {
			$class_name = $this->arguments->altClass->value;
			if (class_exists($class_name, false) && is_subclass_of($class_name, 'WURFL_WURFLManager')) {
				$this->wurfl = new $class_name();
			} else {
				throw new WURFL_WURFLCLIInvalidArgumentException("Error: $class_name must extend WURFL_WURFLManager.");
			}
		} else {

            $persistenceDir = RESOURCES_DIR.'/storage/persistence';
            $cacheDir = RESOURCES_DIR.'/storage/cache';

            // Create WURFL Configuration
            $wurflConfig = new WURFL_Configuration_InMemoryConfig();

            // Set location of the WURFL File
            $wurflConfig->wurflFile(RESOURCES_DIR . '/wurfl.zip');

            // Set the match mode for the API ('performance' or 'accuracy')
            $wurflConfig->matchMode('performance');

            // Setup WURFL Persistence
            $wurflConfig->persistence('file', array('dir' => $persistenceDir));

            // Setup Caching
            $wurflConfig->cache('null');

            $wurflConfig->allowReload(true);

            // Create a WURFL Manager Factory from the WURFL Configuration
            $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

            // Create a WURFL Manager

			$this->wurfl = $wurflManagerFactory->create();
		}
	}

}
class WURFL_Utils_CLI_ArgumentFactory {
	/**
	 * @return WURFL_Utils_CLI_Argument_Collection
	 */
	public static function createArgumentCollection() {
		$argv = $_SERVER['argv'];
		array_shift($argv);
		$collection = new WURFL_Utils_CLI_Argument_Collection();
		foreach ($argv as $raw_arg) {
			$collection->add(self::createArgument($raw_arg));
		}
		return $collection;
	}
	/**
	 * @param string $text Raw argument from ARGV
	 * @return WURFL_Utils_CLI_Argument
	 * @throws WURFLCLIInvalidArgumentException
	 */
	public static function createArgument($text) {
		if (preg_match('/^(?:-+)?([^=]+)=(.*)$/', $text, $matches)) {
			return new WURFL_Utils_CLI_Argument($matches[1], $matches[2]);
		} else if(preg_match('/^(?:-+)?(.*)$/', $text, $matches)) {
			return new WURFL_Utils_CLI_Argument($matches[1]);
		} else {
			throw new WURFL_WURFLCLIInvalidArgumentException("Invalid argument: $text");
		}
	}
}
class WURFL_Utils_CLI_Argument {
	public $command;
	public $value;
	public function __construct($command, $value=null) {
		$this->command = $command;
		$this->value = $value;
	}
}
class WURFL_Utils_CLI_Argument_Collection implements Iterator {
	private $arguments = array();
	private $position = 0;
	public function __get($key) {
		foreach ($this->arguments as $arg) {
			if ($arg->command == $key) return $arg;
		}
		return null;
	}
	public function exists($key) {
		return ($this->__get($key) !== null);
	}
	public function count() { return count($this->arguments); }
	public function isEmpty() { return ($this->count() === 0); }
	public function add(WURFL_Utils_CLI_Argument $arg) { $this->arguments[] = $arg; }
	public function rewind() { $this->position = 0; }
	public function current() { return $this->arguments[$this->position]; }
	public function key() { return $this->position; }
	public function next() { ++$this->position; }
	public function valid() { return isset($this->arguments[$this->position]); }
}
