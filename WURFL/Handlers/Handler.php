<?php
/**
 * Copyright (c) 2015 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING.txt file distributed with this package.
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */

/**
 * WURFL_Handlers_Handler is the base class that combines the classification of
 * the user agents and the matching process.
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
abstract class WURFL_Handlers_Handler implements WURFL_Handlers_Filter, WURFL_Handlers_Matcher {
	
	/**
	 * The next User Agent Handler
	 * @var WURFL_Handlers_Handler
	 */
	protected $nextHandler;
	
	/**
	 * @var WURFL_Request_UserAgentNormalizer
	 */
	protected $userAgentNormalizer;
	
	/**
	 * @var string Prefix for this User Agent Handler
	 */
	protected $prefix;
	
	/**
	 * @var array Array of user agents with device IDs 
	 */
	protected $userAgentsWithDeviceID;
	
	/**
	 * @var WURFL_Storage_Base
	 */
	protected $persistenceProvider;
	
	/**
	 * @var WURFL_Logger_Interface
	 */
	protected $logger;
	/**
	 * @var WURFL_Logger_Interface
	 */
	protected $undetectedDeviceLogger;
	
	/**
	 * @var array Array of WURFL IDs that are hard-coded in this matcher
	 */
	public static $constantIDs = array();
	
	/**
	 * @param WURFL_Context $wurflContext
	 * @param WURFL_Request_UserAgentNormalizer_Interface $userAgentNormalizer
	 */
	public function __construct($wurflContext, $userAgentNormalizer = null) {
		
		if (is_null($userAgentNormalizer)) {
			$this->userAgentNormalizer = new WURFL_Request_UserAgentNormalizer_Null();
		} else {
			$this->userAgentNormalizer = $userAgentNormalizer;
		}
		$this->setupContext($wurflContext);
	}
	
	/**
	 * Sets the next Handler
	 *
	 * @param WURFL_Handlers_UserAgentHandler $handler
	 */
	public function setNextHandler($handler) {
		$this->nextHandler = $handler;
	}
	
	/**
	 * Alias for getPrefix()
	 * @return string Prefix
	 * @see getPrefix()
	 */
	public function getName() {
		return $this->getPrefix();
	}
	
	public function setupContext(WURFL_Context $wurflContext) {
		$this->logger = $wurflContext->logger;
		//$this->undtectedDeviceLogger = $wurflContext->undetectedDeviceLogger;
		$this->persistenceProvider = $wurflContext->persistenceProvider;
	}
	
	/**
	 * Returns true if this handler can handle the given $userAgent
	 * @param string $userAgent
	 * @return bool
	 */
	abstract function canHandle($userAgent);
	
	//********************************************************
	//
	//	 Classification of the User Agents
	//
	//********************************************************
	/**
	 * Classifies the given $userAgent and specified $deviceID
	 * @param string $userAgent
	 * @param string $deviceID
	 * @return null
	 */
	public function filter($userAgent, $deviceID) {
		if ($this->canHandle($userAgent)) {
			$this->updateUserAgentsWithDeviceIDMap($userAgent, $deviceID);
			return null;
		}
		if (isset($this->nextHandler)) {
			return $this->nextHandler->filter($userAgent, $deviceID);
		}
		return null;
	}
	
	/**
	 * Updates the map containing the classified user agents.  These are stored in the associative
	 * array userAgentsWithDeviceID like user_agent => deviceID.
	 * Before adding the user agent to the map it normalizes by using the normalizeUserAgent
	 * function.
	 * 
	 * @see normalizeUserAgent()
	 * @see userAgentsWithDeviceID
	 * @param string $userAgent
	 * @param string $deviceID
	 */
	final function updateUserAgentsWithDeviceIDMap($userAgent, $deviceID) {
		$this->userAgentsWithDeviceID[$this->normalizeUserAgent($userAgent)] = $deviceID;
	}
	
	/**
	 * Normalizes the given $userAgent using this handler's User Agent Normalizer.
	 * If you need to normalize the user agent you need to override the function in
	 * the specific user agent handler.
	 * 
	 * @see $userAgentNormalizer, WURFL_Request_UserAgentNormalizer
	 * @param string $userAgent
	 * @return string Normalized user agent
	 */
	public function normalizeUserAgent($userAgent) {
		return $this->userAgentNormalizer->normalize($userAgent);
	}
	
	//********************************************************
	//	Persisting The classified user agents
	//
	//********************************************************
	/**
	 * Saves the classified user agents in the persistence provider
	 */
	public function persistData() {
		// we sort the array first, useful for doing ris match
		if (!empty($this->userAgentsWithDeviceID)) {
			ksort($this->userAgentsWithDeviceID);
			$this->persistenceProvider->save($this->getPrefix(), $this->userAgentsWithDeviceID);
		}
	}
	
	/**
	 * Returns a list of User Agents with their Device IDs
	 * @return array User agents and device IDs
	 */
	public function getUserAgentsWithDeviceId() {
		if (!isset($this->userAgentsWithDeviceID)) {
			$this->userAgentsWithDeviceID = $this->persistenceProvider->load($this->getPrefix());
		}
		return $this->userAgentsWithDeviceID;
	}
	
	//********************************************************
	//	Matching
	//
	//********************************************************
	/**
	 * Finds the device id for the given request - if it is not found it 
	 * delegates to the next available handler
	 * 
	 * @param WURFL_Request_GenericRequest $request
	 * @return string WURFL Device ID for matching device
	 */
	public function match(WURFL_Request_GenericRequest $request) {
		if ($this->canHandle($request->userAgentNormalized)) {
			return $this->applyMatch($request);
		}
		
		if (isset($this->nextHandler)) {
			return $this->nextHandler->match($request);
		}
		
		return WURFL_Constants::GENERIC;
	}
	
	/**
	 * Template method to apply matching system to user agent
	 *
	 * @param WURFL_Request_GenericRequest $request
	 * @return string Device ID
	 */
	public function applyMatch(WURFL_Request_GenericRequest $request) {
		$class_name = get_class($this);
		$request->matchInfo->matcher = $class_name;
		$start_time = microtime(true);
		
		$userAgent = $this->normalizeUserAgent($request->userAgentNormalized);
		$request->matchInfo->normalized_user_agent = $userAgent;
		$this->logger->debug("START: Matching For  " . $userAgent);
		
		// Get The data associated with this current handler
		$this->userAgentsWithDeviceID = $this->persistenceProvider->load($this->getPrefix());
		if (!is_array($this->userAgentsWithDeviceID)) {
			$this->userAgentsWithDeviceID = array();
		}
		$deviceID = null;
		// Start with an Exact match
		$request->matchInfo->matcher_history .= "$class_name(exact),";
		$request->matchInfo->match_type = 'exact';
		$deviceID = $this->applyExactMatch($userAgent);
		
		// Try with the conclusive Match
		if ($this->isBlankOrGeneric($deviceID)) {
			$request->matchInfo->matcher_history .= "$class_name(conclusive),";
			$this->logger->debug("$this->prefix :Applying Conclusive Match for ua: $userAgent");
			$deviceID = $this->applyConclusiveMatch($userAgent);
		
			// Try with recovery match
			if ($this->isBlankOrGeneric($deviceID)) {
				// Log the ua and the ua profile
				//$this->logger->debug($request);
				$request->matchInfo->match_type = 'recovery';
				$request->matchInfo->matcher_history .= "$class_name(recovery),";
				$this->logger->debug("$this->prefix :Applying Recovery Match for ua: $userAgent");
				$deviceID = $this->applyRecoveryMatch($userAgent);
				
				// Try with catch all recovery Match
				if ($this->isBlankOrGeneric($deviceID)) {
					$request->matchInfo->match_type = 'recovery-catchall';
					$request->matchInfo->matcher_history .= "$class_name(recovery-catchall),";
					$this->logger->debug("$this->prefix :Applying Catch All Recovery Match for ua: $userAgent");
					$deviceID = $this->applyRecoveryCatchAllMatch($userAgent);
					
					// All attempts to match have failed
					if ($this->isBlankOrGeneric($deviceID)) {
						$request->matchInfo->match_type = 'none';
						if ($request->userAgentProfile) {
							$deviceID = WURFL_Constants::GENERIC_MOBILE;
						} else {
							$deviceID = WURFL_Constants::GENERIC;
						}
					}
				}
			}	
		}
		$this->logger->debug("END: Matching For  " . $userAgent);
		$request->matchInfo->lookup_time = microtime(true) - $start_time;
		return $deviceID;
	}
	/**
	 * Given $deviceID is blank or generic, indicating no match
	 * @param string $deviceID
	 * @return bool
	 */
	private function isBlankOrGeneric($deviceID) {
		return ($deviceID === null || strcmp($deviceID, "generic") === 0 || strlen(trim($deviceID)) == 0);
	}
	
	public function applyExactMatch($userAgent) {
		if (array_key_exists($userAgent, $this->userAgentsWithDeviceID)) {
			return $this->userAgentsWithDeviceID[$userAgent];
		}
		return WURFL_Constants::NO_MATCH;
	}
	
	/**
	 * Attempt to find a conclusive match for the given $userAgent
	 * @param string $userAgent
	 * @return string Matching WURFL deviceID
	 */
	public function applyConclusiveMatch($userAgent) {
		$match = $this->lookForMatchingUserAgent($userAgent);
		if (!empty($match)) {
			//die('<pre>'.htmlspecialchars(var_export($this->userAgentsWithDeviceID, true)).'</pre>');
			return $this->userAgentsWithDeviceID[$match];
		}
		return WURFL_Constants::NO_MATCH;
	}
	
	/**
	 * Find a matching WURFL device from the given $userAgent. Override this method to give an alternative way to do the matching
	 *
	 * @param string $userAgent
	 * @return string
	 */
	public function lookForMatchingUserAgent($userAgent) {
		$tolerance = WURFL_Handlers_Utils::firstSlash($userAgent);
		return WURFL_Handlers_Utils::risMatch(array_keys($this->userAgentsWithDeviceID), $userAgent, $tolerance);
	}
	
	public function getDeviceIDFromRIS($userAgent, $tolerance) {
        if ($tolerance === null) {
            return WURFL_Constants::NO_MATCH;
        }
		$match = WURFL_Handlers_Utils::risMatch(array_keys($this->userAgentsWithDeviceID), $userAgent, $tolerance);
		if (!empty($match)) {
			return $this->userAgentsWithDeviceID[$match];
		}
		return WURFL_Constants::NO_MATCH;
	}
	
	public function getDeviceIDFromLD($userAgent, $tolerance=null) {
		$match = WURFL_Handlers_Utils::ldMatch(array_keys($this->userAgentsWithDeviceID), $userAgent, $tolerance);
		if (!empty($match)) {
			return $this->userAgentsWithDeviceID[$match];
		}
		return WURFL_Constants::NO_MATCH;
	}
	
	/**
	 * Applies Recovery Match
	 * @param string $userAgent
	 * @return string $deviceID
	 */
	public function applyRecoveryMatch($userAgent) {}
	
	/**
	 * Applies Catch-All match
	 * @param string $userAgent
	 * @return string WURFL deviceID
	 */
	public function applyRecoveryCatchAllMatch($userAgent) {
		if (WURFL_Handlers_Utils::isDesktopBrowserHeavyDutyAnalysis($userAgent)) {
			return WURFL_Constants::GENERIC_WEB_BROWSER;
		}

        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'CoreMedia')) return 'apple_iphone_coremedia_ver1';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'Windows CE')) return 'generic_ms_mobile';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'UP.Browser/7.2')) return 'opwv_v72_generic';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'UP.Browser/7')) return 'opwv_v7_generic';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'UP.Browser/6.2')) return 'opwv_v62_generic';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'UP.Browser/6')) return 'opwv_v6_generic';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'UP.Browser/5')) return 'upgui_generic';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'UP.Browser/4')) return 'uptext_generic';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'UP.Browser/3')) return 'uptext_generic';
        // Series 60
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'Series60')) return 'nokia_generic_series60';
        // Access/Net Front
        if (WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent,array('NetFront/3.0', 'ACS-NF/3.0'))) return 'generic_netfront_ver3';
        if (WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent,array('NetFront/3.1', 'ACS-NF/3.1'))) return 'generic_netfront_ver3_1';
        if (WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent,array('NetFront/3.2', 'ACS-NF/3.2'))) return 'generic_netfront_ver3_2';
        if (WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent,array('NetFront/3.3', 'ACS-NF/3.3'))) return 'generic_netfront_ver3_3';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'NetFront/3.4')) return 'generic_netfront_ver3_4';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'NetFront/3.5')) return 'generic_netfront_ver3_5';
        if (WURFL_Handlers_Utils::checkIfContains($userAgent,'NetFront/4.0')) return 'generic_netfront_ver4_0';
        // Contains Mozilla/, but not at the beginning of the UA
        // ie: MOTORAZR V8/R601_G_80.41.17R Mozilla/4.0 (compatible; MSIE 6.0 Linux; MOTORAZR V88.50) Profile/MIDP-2.0 Configuration/CLDC-1.1 Opera 8.50[zh]
        if (strpos($userAgent, 'Mozilla/') > 0) return WURFL_Constants::GENERIC_XHTML;
        if (WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent,array('Obigo','AU-MIC/2','AU-MIC-','AU-OBIGO/', 'Teleca Q03B1'))) {
            return WURFL_Constants::GENERIC_XHTML;
        }
        // DoCoMo
        if (WURFL_Handlers_Utils::checkIfStartsWithAnyOf($userAgent, array('DoCoMo', 'KDDI'))) return 'docomo_generic_jap_ver1';

        if (WURFL_Handlers_Utils::isMobileBrowser($userAgent)) return WURFL_Constants::GENERIC_MOBILE;
        return WURFL_Constants::GENERIC;
	}
	
	/**
	 * Returns the prefix for this Handler, like BLACKBERRY_DEVICEIDS for the
	 * BlackBerry Handler.  The "BLACKBERRY_" portion comes from the individual
	 * Handler's $prefix property and "_DEVICEIDS" is added here.
	 * @return string
	 */
	public function getPrefix() {
		return $this->prefix . "_DEVICEIDS";
	}
	
	public function getNiceName() {
		$class_name = get_class($this);
		// WURFL_Handlers_AlcatelHandler
		preg_match('/^WURFL_Handlers_(.+)Handler$/', $class_name, $matches);
		return $matches[1];
	}
	
	/**
	 * Returns true if given $deviceId exists
	 * @param string $deviceId
	 * @return bool
	 */
	protected function isDeviceExist($deviceId) {
		$ids = array_values($this->userAgentsWithDeviceID);
		if (in_array($deviceId, $ids)) {
			return true;
		}
		return false;
	}
	
	public function __sleep() {
		return array(
				'nextHandler',
				'userAgentNormalizer',
				'prefix',
		);
	}
}
