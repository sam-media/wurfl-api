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
 * @package	WURFL_Request
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @author	 Fantayeneh Asres Gizaw
 * @version	$id$
 */
/**
 * Creates a Generic WURFL Request from the raw HTTP Request
 * @package	WURFL_Request
 */
class WURFL_Request_GenericRequestFactory {

	/**
	 * Creates Generic Request from the given HTTP Request (normally $_SERVER)
	 * @param array $request HTTP Request
     * @param bool $override_sideloaded_browser_ua
	 * @return WURFL_Request_GenericRequest
	 */
	public function createRequest($request, $override_sideloaded_browser_ua = true) {
		$userAgent = WURFL_WURFLUtils::getUserAgent($request, $override_sideloaded_browser_ua);
		$userAgentProfile = WURFL_WURFLUtils::getUserAgentProfile($request);
		$isXhtmlDevice = WURFL_WURFLUtils::isXhtmlRequester($request);

		return new WURFL_Request_GenericRequest($request, $userAgent, $userAgentProfile, $isXhtmlDevice);
	}

	/**
	 * Create a Generic Request from the given $userAgent
	 * @param string $userAgent
	 * @return WURFL_Request_GenericRequest
	 */
	public function createRequestForUserAgent($userAgent) {
		$request = array('HTTP_USER_AGENT' => $userAgent);
		return new WURFL_Request_GenericRequest($request, $userAgent, null, false);
	}


}


