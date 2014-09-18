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
 * @package	WURFL_Request_UserAgentNormalizer_Specific
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
/**
 * User Agent Normalizer
 * @package	WURFL_Request_UserAgentNormalizer_Specific
 */
class WURFL_Request_UserAgentNormalizer_Specific_Apple implements WURFL_Request_UserAgentNormalizer_Interface {
	public function normalize($userAgent) {
		// Normalize Skype SDK UAs
		if (preg_match('#^iOSClientSDK/\d+\.+[0-9\.]+ +?\((Mozilla.+)\)$#', $userAgent, $matches)) {
			return $matches[1];
		}
		return $userAgent;
	}
}