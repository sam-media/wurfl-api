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
 * @package	WURFL_VirtualCapability
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
/**
 * Virtual capability helper
 * @package	WURFL_VirtualCapability
 */
 
class WURFL_VirtualCapability_IsRobot extends WURFL_VirtualCapability {

	protected $required_capabilities = array();

	protected function compute() {
        $ua = $this->request->userAgent;

		// Control cap, "controlcap_is_robot" is checked before this function is called
        if ($this->request->originalHeaderExists("HTTP_ACCEPT_ENCODING")
            && WURFL_Handlers_Utils::checkIfContains($ua, "Trident/")
            && !WURFL_Handlers_Utils::checkIfContains($this->request->getOriginalHeader("HTTP_ACCEPT_ENCODING"), "deflate")) {
            return true;
        }
		// Check against standard bot list
		return WURFL_Handlers_Utils::isRobot($this->request->getOriginalHeader('HTTP_USER_AGENT'));
	}
}