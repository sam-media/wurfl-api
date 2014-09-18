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
 * DesktopApplicationHandler
 * 
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */

class WURFL_Handlers_DesktopApplicationHandler extends WURFL_Handlers_Handler {
	
	public static $constantIDs = array(
		'generic_desktop_application',
		'mozilla_thunderbird',
		'ms_outlook',
		'ms_outlook_subua14',
		'ms_outlook_subua15',
		'ms_office',
		'ms_office_subua12',
		'ms_office_subua14',
		'ms_office_subua15',
	);
	
	public function canHandle($userAgent) {
		if (WURFL_Handlers_Utils::isMobileBrowser($userAgent)) return false;
		return (WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent, array('Thunderbird', 'Microsoft Outlook', 'MSOffice')));
	}
		
	public function applyConclusiveMatch($userAgent) {
		if (WURFL_Handlers_Utils::checkIfContains($userAgent, 'Thunderbird')) {
			$idx = strpos($userAgent, '.');
			if ($idx !== false) {
				return $this->getDeviceIDFromRIS($userAgent, $idx + 1);
			}
		}
		
		// Check for Outlook before Office
		if (preg_match('#Microsoft Outlook ([0-9]+)\.#', $userAgent, $matches)) {
			$deviceID = 'ms_outlook_subua'.$matches[1];
			if (in_array($deviceID, self::$constantIDs)) {
				return $deviceID;
			}
		
		} else if (preg_match('#MSOffice ([0-9]+)\b#', $userAgent, $matches)) {
			$deviceID = 'ms_office_subua'.$matches[1];
			if (in_array($deviceID, self::$constantIDs)) {
				return $deviceID;
			}
		}
		
		return WURFL_Constants::NO_MATCH;
		
		
		
	}
	
	public function applyRecoveryMatch($userAgent) {
		if (WURFL_Handlers_Utils::checkIfContains($userAgent, 'Thunderbird')) {
			return 'mozilla_thunderbird';
		} else if (WURFL_Handlers_Utils::checkIfContains($userAgent, 'Microsoft Outlook')) {
			return 'ms_outlook';
		} else if (WURFL_Handlers_Utils::checkIfContains($userAgent, 'MSOffice')) {
			return 'ms_office';
		}
		return WURFL_Constants::GENERIC_WEB_BROWSER;
	}
	
}
