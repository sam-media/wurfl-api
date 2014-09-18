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
 * @package	WURFL_Request_UserAgentNormalizer_Generic
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @author	 Fantayeneh Asres Gizaw
 * @version	$id$
 */
/**
 * User Agent Normalizer - removes serial numbers from user agent
 * @package	WURFL_Request_UserAgentNormalizer_Generic
 */
class WURFL_Request_UserAgentNormalizer_Generic_SerialNumbers implements WURFL_Request_UserAgentNormalizer_Interface {
	
	public function normalize($userAgent) {
		$userAgent = preg_replace('/\/SN[\dX]+/', '/SNXXXXXXXXXXXXXXX', $userAgent);
		$userAgent = preg_replace('/\[(ST|TF|NT)[\dX]+\]/', 'TFXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX', $userAgent);
		return $userAgent;
	}

}

