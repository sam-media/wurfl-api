<?php

class WURFL_Utils_CLI
{
    /**
     * @var WURFL_Utils_CLI_Argument_Collection
     */
    protected $arguments;
    /**
     * @var WURFL_WURFLManager
     */
    protected $wurfl;

    protected static $enable_console_logger = false;

    public function __construct($wurfl = null)
    {
        error_reporting(E_ALL);
        $this->arguments = WURFL_Utils_CLI_ArgumentFactory::createArgumentCollection();
        $this->requireAdditionalClasses();
        if ($wurfl !== null) {
            $this->wurfl = $wurfl;
        } else {
            $this->createWurflClass();
        }
    }

    public function processArguments()
    {
        if ($this->arguments->isEmpty()) {
            $this->arguments->add(new WURFL_Utils_CLI_Argument('help'));
        }
        foreach ($this->arguments as $arg) {
            $action = 'action' . ucfirst($arg->command);
            if (!method_exists($this, $action)) {
                continue;
            }
            $this->$action($arg);
        }
    }

    protected function actionBucketListOverwrite(WURFL_Utils_CLI_Argument $arg)
    {
        self::$enable_console_logger = true;
        WURFL_FileUtils::rmdirContents(RESOURCES_DIR . '/storage/persistence');
        $this->createWurflClass();
    }

    protected function actionBucketList(WURFL_Utils_CLI_Argument $arg)
    {
        if ($arg->value) {
            $devices = array($arg->value);
        } else {
            $devices = $this->wurfl->getAllDevicesID();
            // Sort by device_id
            sort($devices);
        }

        foreach ($devices as $device_id) {
            $device      = $this->wurfl->getDevice($device_id);
            $ua_original = $device->userAgent;
            unset($device);
            $device_enriched = $this->wurfl->getDeviceForUserAgent($ua_original);
            $ua_normalized   = $device_enriched->getMatchInfo()->normalized_user_agent;
            $bucket_name     = self::bucketNameFromMatcherClass($device_enriched->getMatchInfo()->matcher);
            unset($device_enriched);
            echo implode("\t", array($bucket_name, $device_id, $ua_normalized, $ua_original)) . PHP_EOL;
        }
    }

    protected function actionBucketListFromHandlers(WURFL_Utils_CLI_Argument $arg)
    {
        $wurfl_service = new ReflectionProperty('WURFL_WURFLManager', '_wurflService');
        $wurfl_service->setAccessible(true);
        $service        = $wurfl_service->getValue($this->wurfl);
        $wurfl_ua_chain = new ReflectionProperty('WURFL_WURFLService', '_userAgentHandlerChain');
        $wurfl_ua_chain->setAccessible(true);
        $ua_chain = $wurfl_ua_chain->getValue($service);

        $ordered_buckets = array();

        foreach ($ua_chain->getHandlers() as $userAgentHandler) {
            $ordered_buckets[self:: formatBucketName($userAgentHandler->getName())] = $userAgentHandler;
        }

        $bucket_list = array();

        foreach ($ordered_buckets as $bucket => $userAgentHandler) {
            /**
             * @see WURFL_Handlers_Handler::getUserAgentsWithDeviceId()
             */
            $ua_device_list = $userAgentHandler->getUserAgentsWithDeviceId();

            if ($ua_device_list) {
                $sorted = array_flip($ua_device_list);

                foreach ($sorted as $device_id => $ua_normalized) {
                    $device                   = $this->wurfl->getDevice($device_id);
                    $bucket_list[$device->id] = array($bucket, $ua_normalized, $device->userAgent);
                }
            }
        }

        // Sort by device_id
        ksort($bucket_list);
        echo 'PHP API v' . WURFL_Constants::API_VERSION . ' for ' . $this->wurfl->getWURFLInfo()->version . PHP_EOL;
        echo "Bucket\tDeviceId\tNormalizedUserAgent\tOriginaUserAgent" . PHP_EOL;
        foreach ($bucket_list as $device_id => $item) {
            echo $item[0] . "\t";
            echo $device_id . "\t";
            echo $item[1] . "\t";
            echo $item[2] . "\n";
        }
    }

    public static function bucketNameFromMatcherClass($matcher_class)
    {
        preg_match('/^WURFL_Handlers_(.+)Handler$/', $matcher_class, $matches);
        $name = $matches[1];

        switch ($name) {
            case 'Sonyericsson':
                $name = 'SonyEricsson';
                break;
            case 'Catchallris':
                $name = 'CatchAllRis';
                break;
            case 'Botcrawlertranscoder':
                $name = 'Bot';
                break;
            case 'Catchallmozilla':
                $name = 'CatchAllMozilla';
                break;
            case 'Operamini':
                $name = 'OperaMini';
                break;
            case 'BotCrawlerTranscoder':
                $name = 'Bot';
                break;
            case 'KDDI':
                $name = 'Kddi';
                break;
        }

        return $name;
    }

    public static function formatBucketName($handler_name)
    {
        $name = str_replace('_DEVICEIDS', '', $handler_name);
        $name = ucfirst(strtolower($name));
        switch ($name) {
            case 'Benq':
                $name = 'BenQ';
                break;
            case 'Blackberry':
                $name = 'BlackBerry';
                break;
            case 'Bot_crawler_transcoder':
                $name = 'Bot';
                break;
            case 'Catch_all_mozilla':
                $name = 'CatchAllMozilla';
                break;
            case 'Catch_all_ris':
                $name = 'CatchAllRis';
                break;
            case 'Desktopapplication':
                $name = 'DesktopApplication';
                break;
            case 'Docomo':
                $name = 'DoCoMo';
                break;
            case 'Fenneconandroid':
                $name = 'FennecOnAndroid';
                break;
            case 'Firefoxos':
                $name = 'FirefoxOS';
                break;
            case 'Htc':
                $name = 'HTC';
                break;
            case 'Htcmac':
                $name = 'HTCMac';
                break;
            case 'Javamidlet':
                $name = 'JavaMidlet';
                break;
            case 'Lg':
                $name = 'LG';
                break;
            case 'Lguplus':
                $name = 'LGUPLUS';
                break;
            case 'Msie':
                $name = 'MSIE';
                break;
            case 'Nokiaovibrowser':
                $name = 'NokiaOviBrowser';
                break;
            case 'Netfrontonandroid':
                $name = 'NetFrontOnAndroid';
                break;
            case 'Opera_mini':
                $name = 'OperaMini';
                break;
            case 'Operaminionandroid':
                $name = 'OperaMiniOnAndroid';
                break;
            case 'Operamobiortabletonandroid':
                $name = 'OperaMobiOrTabletOnAndroid';
                break;
            case 'Smarttv':
                $name = 'SmartTV';
                break;
            case 'Sony_ericsson':
                $name = 'SonyEricsson';
                break;
            case 'Spv':
                $name = 'SPV';
                break;
            case 'Ucweb7onandroid':
                $name = 'Ucweb7OnAndroid';
                break;
            case 'Ucwebu2':
                $name = 'UcwebU2';
                break;
            case 'Ucwebu3':
                $name = 'UcwebU3';
                break;
            case 'Webos':
                $name = 'WebOS';
                break;
            case 'Windowsphone':
                $name = 'WindowsPhone';
                break;
            case 'Windowsrt':
                $name = 'WindowsRT';
                break;
        }

        return $name;
    }

    protected function actionCentralTest(WURFL_Utils_CLI_Argument $arg)
    {
        $test_type = $arg->value;
        require_once dirname(__FILE__) . '/../../tests/CentralTest/CentralTestManager.php';

        if (preg_match('#(single/.*)$#', $test_type, $matches)) {
            $test_list = CentralTestManager::loadSingleTest($matches[1]);
        } else {
            $test_list = CentralTestManager::loadBatchTest($test_type);
        }

        $centralTest = new CentralTestManager($this->wurfl, $test_list);
        //TODO: Add introspector support
        if ($this->arguments->introspector) {
            if ($this->arguments->username && $this->arguments->password) {
                $centralTest->useIntrospector($this->arguments->introspector->value, $this->arguments->username->value, $this->arguments->password->value);
            } else {
                $centralTest->useIntrospector($this->arguments->introspector->value);
            }
        }
        $centralTest->show_success = false;
        $centralTest->run();
    }

    protected function actionCentralTestFiltered(WURFL_Utils_CLI_Argument $arg)
    {
        $test_type = $arg->value;
        require_once dirname(__FILE__) . '/../../tests/CentralTest/CentralTestManager.php';

        if (preg_match('#(single/.*)$#', $test_type, $matches)) {
            $test_list = CentralTestManager::loadSingleTest($matches[1]);
        } else {
            $test_list = CentralTestManager::loadBatchTest($test_type);
        }

        $required_caps = CentralTestManager::getRequiredCapsFromTestList($test_list);

        $wurfl = $this->createWurflClassFiltered($required_caps);

        $centralTest = new CentralTestManager($wurfl, $test_list);
        //TODO: Add introspector support
        if ($this->arguments->introspector) {
            if ($this->arguments->username && $this->arguments->password) {
                $centralTest->useIntrospector($this->arguments->introspector->value, $this->arguments->username->value, $this->arguments->password->value);
            } else {
                $centralTest->useIntrospector($this->arguments->introspector->value);
            }
        }
        $centralTest->show_success = false;
        $centralTest->run();
    }

    protected function actionTestDeviceId(WURFL_Utils_CLI_Argument $arg)
    {
        $device = $this->wurfl->getDevice($arg->value);
        $device = $this->wurfl->getDeviceForUserAgent($device->userAgent);
        echo 'Device ID: ' . $device->id . PHP_EOL;
        echo 'UA: ' . $device->userAgent . PHP_EOL;
        echo 'Fallback: ' . $device->fallBack . PHP_EOL;
        echo 'Match Info: ' . PHP_EOL;
        var_export($device->getMatchInfo());
        echo PHP_EOL;
        echo 'Virtual Capabilities:' . PHP_EOL;
        var_export($device->getAllVirtualCapabilities());
    }

    protected function actionTestUserAgent(WURFL_Utils_CLI_Argument $arg)
    {
        $device = $this->wurfl->getDeviceForUserAgent($arg->value);
        echo 'Device ID: ' . $device->id . PHP_EOL;
        echo 'UA: ' . $device->userAgent . PHP_EOL;
        echo 'Fallback: ' . $device->fallBack . PHP_EOL;
        echo 'Match Info: ' . PHP_EOL;
        var_export($device->getMatchInfo());
        echo PHP_EOL;
        echo 'Virtual Capabilities:' . PHP_EOL;
        var_export($device->getAllVirtualCapabilities());
    }

    protected function actionHelp(WURFL_Utils_CLI_Argument $arg)
    {
        $api_version   = WURFL_Constants::API_VERSION;
        $wurfl_version = $this->wurfl->getWURFLInfo()->version;
        $last_updated  = $this->wurfl->getWURFLInfo()->lastUpdated;
        $usage         = <<<EOL

ScientiaMobile WURFL PHP API $api_version
Command Line Interface
Loaded WURFL: $wurfl_version
Last Updated: $last_updated
---------------------------------------
Usage: php wurfl_cli.php [OPTIONS]

Option                          Meaning
 --help                         Show this message
 --bucketList                   Show the bucket list in tsv format
 --testUserAgent=<user_agent>   Run WURFL against the specified user_agent
 --centralTest=<unit|regression|all|single/<test_name>>
                           Run tests from the ScientiaMobile Central
                             testing repository.

EOL;
        echo $usage;
    }

    protected function requireAdditionalClasses()
    {
        if (!$this->arguments->require) {
            return;
        }
        require_once $this->arguments->require->value;
    }

    protected function createWurflClass()
    {
        if ($this->arguments->altClass) {
            $class_name = $this->arguments->altClass->value;
            if (class_exists($class_name, false) && is_subclass_of($class_name, 'WURFL_WURFLManager')) {
                $this->wurfl = new $class_name();
            } else {
                throw new WURFL_WURFLCLIInvalidArgumentException("Error: $class_name must extend WURFL_WURFLManager.");
            }
        } else {
            $persistenceDir = RESOURCES_DIR . '/storage/persistence';
            $cacheDir       = RESOURCES_DIR . '/storage/cache';

            // Create WURFL Configuration
            $wurflConfig = new WURFL_Configuration_InMemoryConfig();

            // Set location of the WURFL File
            $wurflConfig->wurflFile(WURFL_DB_DIR . '/wurfl.zip');

            // Set the match mode for the API ('performance' or 'accuracy')
            $wurflConfig->matchMode('accuracy');

            // Setup WURFL Persistence
            $wurflConfig->persistence('file', array('dir' => $persistenceDir));

            // Setup Caching
            $wurflConfig->cache('null');

            $wurflConfig->allowReload(true);

            if (self::$enable_console_logger) {
                $wurflConfig->setLogger(new WURFL_Logger_ConsoleLogger());
            }

            // Create a WURFL Manager Factory from the WURFL Configuration
            $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

            // Create a WURFL Manager

            $this->wurfl = $wurflManagerFactory->create();
        }
    }

    protected function createWurflClassMemory()
    {

        // Create WURFL Configuration
        $wurflConfig = new WURFL_Configuration_InMemoryConfig();

        // Set location of the WURFL File
        $wurflConfig->wurflFile(WURFL_DB_DIR . '/wurfl.zip');

        // Set the match mode for the API ('performance' or 'accuracy')
        $wurflConfig->matchMode('accuracy');

        // Setup WURFL Persistence
        $wurflConfig->persistence('memory');

        // Setup Caching
        $wurflConfig->cache('null');

        // Create a WURFL Manager Factory from the WURFL Configuration
        $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

        // Create a WURFL Manager

        return $wurflManagerFactory->create();
    }

    protected function createWurflClassFiltered($required_caps)
    {
        if (empty($required_caps)) {
            throw new WURFL_WURFLCLIInvalidArgumentException('Capability filter cannot be an empty array');
        }

        $persistenceDir = RESOURCES_DIR . '/storage/persistence_filtered';
        $cacheDir       = RESOURCES_DIR . '/storage/cache_filtered';

        // Create WURFL Configuration
        $wurflConfig = new WURFL_Configuration_InMemoryConfig();

        // Set location of the WURFL File
        $wurflConfig->wurflFile(WURFL_DB_DIR . '/wurfl.zip');

        // Set the match mode for the API ('performance' or 'accuracy')
        $wurflConfig->matchMode('accuracy');

        // Setup WURFL Persistence
        $wurflConfig->persistence('file', array('dir' => $persistenceDir));

        // Setup Caching
        $wurflConfig->cache('null');

        $wurflConfig->allowReload(true);

        $wurflConfig->capabilityFilter($required_caps);

        // Create a WURFL Manager Factory from the WURFL Configuration
        $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);

        // Create a WURFL Manager

        return $wurflManagerFactory->create();
    }
}
class WURFL_Utils_CLI_ArgumentFactory
{
    /**
     * @return WURFL_Utils_CLI_Argument_Collection
     */
    public static function createArgumentCollection()
    {
        $argv = $_SERVER['argv'];
        array_shift($argv);
        $collection = new WURFL_Utils_CLI_Argument_Collection();
        foreach ($argv as $raw_arg) {
            $collection->add(self::createArgument($raw_arg));
        }

        return $collection;
    }
    /**
     * @param  string                           $text Raw argument from ARGV
     * @throws WURFLCLIInvalidArgumentException
     * @return WURFL_Utils_CLI_Argument
     */
    public static function createArgument($text)
    {
        if (preg_match('/^(?:-+)?([^=]+)=(.*)$/', $text, $matches)) {
            return new WURFL_Utils_CLI_Argument($matches[1], $matches[2]);
        } elseif (preg_match('/^(?:-+)?(.*)$/', $text, $matches)) {
            return new WURFL_Utils_CLI_Argument($matches[1]);
        } else {
            throw new WURFL_WURFLCLIInvalidArgumentException("Invalid argument: $text");
        }
    }
}
class WURFL_Utils_CLI_Argument
{
    public $command;
    public $value;
    public function __construct($command, $value = null)
    {
        $this->command = $command;
        $this->value   = $value;
    }
}
class WURFL_Utils_CLI_Argument_Collection implements Iterator
{
    private $arguments = array();
    private $position  = 0;
    public function __get($key)
    {
        foreach ($this->arguments as $arg) {
            if ($arg->command === $key) {
                return $arg;
            }
        }

        return null;
    }
    public function exists($key)
    {
        return ($this->__get($key) !== null);
    }
    public function count()
    {
        return count($this->arguments);
    }
    public function isEmpty()
    {
        return ($this->count() === 0);
    }
    public function add(WURFL_Utils_CLI_Argument $arg)
    {
        $this->arguments[] = $arg;
    }
    public function rewind()
    {
        $this->position = 0;
    }
    public function current()
    {
        return $this->arguments[$this->position];
    }
    public function key()
    {
        return $this->position;
    }
    public function next()
    {
        ++$this->position;
    }
    public function valid()
    {
        return isset($this->arguments[$this->position]);
    }
}
