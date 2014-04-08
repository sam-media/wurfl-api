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
 * Generic WURFL Request object containing User Agent, UAProf and xhtml device data; its id
 * property is the MD5 hash of the user agent
 * @package	WURFL_Request
 * 
 * @property string $userAgent
 * @property string $userAgentProfile
 * @property boolean $xhtmlDevice true if the device is known to be XHTML-MP compatible
 * @property string $id Unique ID used for caching: MD5($userAgent)
 * @property WURFL_Request_MatchInfo $matchInfo Information about the match (available after matching)
 */
class WURFL_Request_GenericRequest {
	
	private $_request;
	private $_userAgent;
	private $_userAgentProfile;
	private $_xhtmlDevice;
	private $_id;
	private $_matchInfo;
	
	/**
	 * @param array $request Original HTTP headers
	 * @param string $userAgent
	 * @param string $userAgentProfile
	 * @param string $xhtmlDevice
	 */
	public function __construct(array $request, $userAgent, $userAgentProfile=null, $xhtmlDevice=null) {
		$this->_request = $request;
		$this->_userAgent = $userAgent;
		$this->_userAgentProfile = $userAgentProfile;
		$this->_xhtmlDevice = $xhtmlDevice;
		$this->_id = md5($userAgent);
		$this->_matchInfo = new WURFL_Request_MatchInfo();
	}
	
	public function __get($name) {
		$name = '_'.$name;
		return $this->$name;
	}
	
	/**
	 * Get the original HTTP header value from the request
	 * @param string $name
	 * @return string
	 */
	public function getOriginalHeader($name) {
		return array_key_exists($name, $this->_request)? $this->_request[$name]: null;
	}
	
	public function originalHeaderExists($name) {
		return array_key_exists($name, $this->_request);
	}
}

