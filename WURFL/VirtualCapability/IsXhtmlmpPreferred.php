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
 
class WURFL_VirtualCapability_IsXhtmlmpPreferred extends WURFL_VirtualCapability {

	protected $required_capabilities = array(
		'xhtml_support_level',
		'preferred_markup',
	);

	protected function compute() {
		return ($this->device->xhtml_support_level > 0 && strpos($this->device->preferred_markup, 'html_web') !== 0);
	}
}