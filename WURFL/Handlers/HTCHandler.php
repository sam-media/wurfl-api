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
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */

/**
 * HTCUserAgentHandler
 * 
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
class WURFL_Handlers_HTCHandler extends WURFL_Handlers_Handler {
	
	protected $prefix = "HTC";
	
	public static $constantIDs = array(
		'generic_ms_mobile',
	);
	
	public function canHandle($userAgent) {
		if (WURFL_Handlers_Utils::isDesktopBrowser($userAgent)) return false;
		return WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent, array('HTC', 'XV6875'));
	}
	
	public function applyConclusiveMatch($userAgent) {
		if (preg_match('#^.*?HTC.+?[/ ;]#', $userAgent, $matches)) {
			// The length of the complete match (from the beginning) is the tolerance
			$tolerance = strlen($matches[0]);
		} else {
			$tolerance = strlen($userAgent);
		}
	
		return $this->getDeviceIDFromRIS($userAgent, $tolerance);
	}
	
	public function applyRecoveryMatch($userAgent) {
		if (WURFL_Handlers_Utils::checkIfContains($userAgent, 'Windows CE;')) {
			return 'generic_ms_mobile';
		}
		return WURFL_Constants::NO_MATCH;
	}
}
