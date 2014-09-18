<?php
/**
 * Copyright (c) 2014 ScientiaMobile, Inc.
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
 * CatchAllUserAgentHandler
 *
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */

class WURFL_Handlers_CatchAllHandler extends WURFL_Handlers_Handler {
	
	protected $prefix = "CATCH_ALL";
	const MOZILLA_TOLERANCE = 5;
	
	const MOZILLA5 = "CATCH_ALL_MOZILLA5";
	const MOZILLA4 = "CATCH_ALL_MOZILLA4";
	
	private $mozilla4UserAgentsWithDeviceID = array();
	private $mozilla5UserAgentsWithDeviceID = array();

	/**
	 * Final Interceptor: Intercept
	 * Everything that has not been trapped by a previous handler
	 *
	 * @param string $userAgent
	 * @return boolean always true
	 */
	public function canHandle($userAgent) {
		return true;
	}
	
	/**
	 * If UA starts with Mozilla, apply LD with tollerance 5.
	 * If UA does not start with Mozilla, apply RIS on FS
	 *
	 * @param string $userAgent
	 * @return string
	 */
	public function applyConclusiveMatch($userAgent) {
		$deviceId = WURFL_Constants::GENERIC;
		if (WURFL_Handlers_Utils::checkIfStartsWith($userAgent, 'Mozilla')) {
			$deviceId = $this->applyMozillaConclusiveMatch($userAgent);
		} else {
			$tolerance = WURFL_Handlers_Utils::firstSlash($userAgent);
			$deviceId = $this->getDeviceIDFromRIS($userAgent, $tolerance);
		}
		
		return $deviceId;
	}
	
	public function applyExactMatch($userAgent) {
		$this->ensureAuxDataLoaded();
		if (array_key_exists($userAgent, $this->userAgentsWithDeviceID)) {
			return $this->userAgentsWithDeviceID[$userAgent];
		}
		if (array_key_exists($userAgent, $this->mozilla4UserAgentsWithDeviceID)) {
			return $this->mozilla4UserAgentsWithDeviceID[$userAgent];
		}
		if (array_key_exists($userAgent, $this->mozilla5UserAgentsWithDeviceID)) {
			return $this->mozilla5UserAgentsWithDeviceID[$userAgent];
		}
		return WURFL_Constants::NO_MATCH;
	}
	
	private function ensureAuxDataLoaded() {
		if (empty($this->mozilla4UserAgentsWithDeviceID)) $this->mozilla4UserAgentsWithDeviceID = $this->persistenceProvider->load(self::MOZILLA4);
		if (empty($this->mozilla5UserAgentsWithDeviceID)) $this->mozilla5UserAgentsWithDeviceID = $this->persistenceProvider->load(self::MOZILLA5);
	}
	
	private function applyMozillaConclusiveMatch($userAgent) {
		$this->ensureAuxDataLoaded();
		if ($this->isMozilla5($userAgent)) {
			return $this->applyMozilla5ConclusiveMatch($userAgent);
		}
		
		if ($this->isMozilla4($userAgent)) {
			return $this->applyMozilla4ConclusiveMatch($userAgent);
		}
		
		$this->logger->log("Applying Catch All Conclusive Match for ua: $userAgent");
		$match = WURFL_Handlers_Utils::ldMatch(array_keys($this->userAgentsWithDeviceID), $userAgent, self::MOZILLA_TOLERANCE);
		return $this->userAgentsWithDeviceID [$match];
	
	}
	
	private function applyMozilla5ConclusiveMatch($userAgent) {
		$this->logger->log("Applying Catch All Conclusive Match Mozilla 5 (LD with threshold of )for ua: $userAgent");
		if (!array_key_exists($userAgent, $this->mozilla5UserAgentsWithDeviceID)) {
			$match = WURFL_Handlers_Utils::ldMatch(array_keys($this->mozilla5UserAgentsWithDeviceID), $userAgent, self::MOZILLA_TOLERANCE);
		}
		if (!empty($match)) {
			return $this->mozilla5UserAgentsWithDeviceID [$match];
		}
		return WURFL_Constants::NO_MATCH;
	}
	
	private function applyMozilla4ConclusiveMatch($userAgent) {
		$this->logger->log("Applying Catch All Conclusive Match Mozilla 4 for ua: $userAgent");
		if (! array_key_exists($userAgent, $this->mozilla4UserAgentsWithDeviceID)) {
			$match = WURFL_Handlers_Utils::ldMatch(array_keys($this->mozilla4UserAgentsWithDeviceID), $userAgent, self::MOZILLA_TOLERANCE);
		}
		if (!empty($match)) {
			return $this->mozilla4UserAgentsWithDeviceID [$match];
		}
		return WURFL_Constants::NO_MATCH;
	}
	
	public function filter($userAgent, $deviceID) {
		if ($this->isMozilla4($userAgent)) {
			$this->mozilla4UserAgentsWithDeviceID[$this->normalizeUserAgent($userAgent)] = $deviceID;
		}
		if ($this->isMozilla5($userAgent)) {
			$this->mozilla5UserAgentsWithDeviceID[$this->normalizeUserAgent($userAgent)] = $deviceID;
		}
		parent::filter($userAgent, $deviceID);
	}
	
	public function persistData() {
		ksort($this->mozilla4UserAgentsWithDeviceID);
		ksort($this->mozilla5UserAgentsWithDeviceID);
		$this->persistenceProvider->save(self::MOZILLA4, $this->mozilla4UserAgentsWithDeviceID);
		$this->persistenceProvider->save(self::MOZILLA5, $this->mozilla5UserAgentsWithDeviceID);
		parent::persistData ();
	}
	
	private function loadMozillaData() {
		$this->mozilla4UserAgentsWithDeviceID = $this->persistenceProvider->find(WURFL_Handlers_CatchAllHandler::MOZILLA4);
		$this->mozilla5UserAgentsWithDeviceID = $this->persistenceProvider->find(WURFL_Handlers_CatchAllHandler::MOZILLA5);
	}
	
	private function isMozilla5($userAgent) {
		return WURFL_Handlers_Utils::checkIfStartsWith($userAgent, "Mozilla/5");
	}
	
	private function isMozilla4($userAgent) {
		return WURFL_Handlers_Utils::checkIfStartsWith($userAgent, "Mozilla/4");
	}
	
	private function isMozilla($userAgent) {
		return WURFL_Handlers_Utils::checkIfStartsWith($userAgent, "Mozilla");
	}
}