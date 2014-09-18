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
 * @package	WURFL
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
/**
 * WURFL Custom Device - this is the core class that is used by developers to access the
 * properties and capabilities of a mobile device
 * 
 * Examples:
 * <code>
 * // Create a WURFL Manager and detect device first
 * $wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
 * $wurflManager = $wurflManagerFactory->create();
 * $device = $wurflManager->getDeviceForHttpRequest($_SERVER);
 * 
 * // Example 1: Get display resolution from device
 * $width = $device->getCapability('resolution_width');
 * $height = $device->getCapability('resolution_height');
 * echo "Resolution: $width x $height <br/>";
 * 
 * // Example 2: Get the WURFL ID of the device
 * $wurflID = $device->id;
 * </code>
 * 
 * @property-read string $id WURFL Device ID
 * @property-read string $userAgent User Agent
 * @property-read string $fallBack Fallback Device ID
 * @property-read bool $actualDeviceRoot true if device is an actual root device
 * @package WURFL
 */
class WURFL_CustomDevice {
	
	/**
	 * @var array Array of WURFL_Xml_ModelDevice objects
	 */
	private $modelDevices;
	
	/**
	 * @var WURFL_Request_GenericRequest
	 */
	private $request;
	
	/**
	 * @var WURFL_VirtualCapabilityProvider
	 */
	private $virtualCapabilityProvider;
	
	/**
	 * @param array $modelDevices Array of WURFL_Xml_ModelDevice objects
	 * @param WURFL_Request_GenericRequest $request
	 * @throws InvalidArgumentException if $modelDevices is not an array of at least one WURFL_Xml_ModelDevice
	 */
	public function __construct(Array $modelDevices, $request = null) {
		if (! is_array ( $modelDevices ) || count ( $modelDevices ) < 1) {
			throw new InvalidArgumentException ( "modelDevices must be an array of at least one ModelDevice." );
		}
		$this->modelDevices = $modelDevices;
		if ($request === null) {
			// This might happen if a device is looked up by its ID directly, without providing a user agent
			$requestFactory = new WURFL_Request_GenericRequestFactory();
			$request = $requestFactory->createRequestForUserAgent($this->userAgent);
		}
		$this->request = $request;
		$this->virtualCapabilityProvider = new WURFL_VirtualCapabilityProvider($this, $request);
	}
	
	/**
	 * Magic Method
	 *
	 * @param string $name
	 * @return string
	 */
	public function __get($name) {
		switch ($name) {
			case "id":
			case "userAgent":
			case "fallBack":
			case "actualDeviceRoot":
				return $this->modelDevices[0]->$name;
				break;
			default :
				if ($this->virtualCapabilityProvider->exists($name)) {
					return $this->virtualCapabilityProvider->get($name);
				}
				return $this->getCapability($name);
				break;
		}
	}
	
	/**
	 * Device is a specific or actual WURFL device as defined by its capabilities
	 * @return bool
	 */
	public function isSpecific() {
		foreach ($this->modelDevices as $modelDevice) {
			if ($modelDevice->specific === true || $modelDevice->actualDeviceRoot === true) {
				return true;
			}
		}
		return false;
	}
	
	/**
	 * Returns the value of a given capability name
	 * for the current device
	 * 
	 * @param string $capabilityName must be a valid capability name
	 * @return string Capability value
	 * @throws InvalidArgumentException The $capabilityName is is not defined in the loaded WURFL.
	 * @see WURFL_Xml_ModelDevice::getCapability()
	 */
	public function getCapability($capabilityName) {
		if (empty($capabilityName)) {
			throw new InvalidArgumentException("capability name must not be empty");
		}
		if(!$this->getRootDevice()->isCapabilityDefined($capabilityName)) {
			throw new InvalidArgumentException("no capability named [$capabilityName] is present in wurfl.");	
		}
		foreach ($this->modelDevices as $modelDevice) {
			/* @var WURFL_Xml_ModelDevice $modelDevice */
			$capabilityValue = $modelDevice->getCapability($capabilityName);
			if ($capabilityValue != null) {
				return $capabilityValue;
			}
		}
		return "";
	}
	
	/**
	 * Returns the nearest actual device root in the fall back tree.  If this device is a device root itself, 
	 * it is returned.  Some devices have no device roots in their fall back tree, like generic_android, since
	 * no devices above it (itself included) are real devices (actual device roots).
	 * 
	 * @return WURFL_Xml_ModelDevice
	 */
	public function getActualDeviceRootAncestor() {
		if ($this->actualDeviceRoot) return $this;
		foreach ($this->modelDevices as $modelDevice) {
			/* @var WURFL_Xml_ModelDevice $modelDevice */
			if ($modelDevice->actualDeviceRoot) return $modelDevice;
		}
		return null;
	}
	
	/**
	 * Returns the match info for this device
	 * @return WURFL_Request_MatchInfo
	 */
	public function getMatchInfo() {
		return ($this->request instanceof WURFL_Request_GenericRequest)? $this->request->matchInfo: null;
	}
	
	/**
	 * Returns an array with all the fall back devices, from the matched device to the root device ('generic')
	 * @return array
	 */
	public function getFallBackDevices() {
		return $this->modelDevices;
	}
	
	/**
	 * Returns the top-most device.  This is the "generic" device.
	 * @return WURFL_Xml_ModelDevice
	 */
	public function getRootDevice() {
		return $this->modelDevices[count($this->modelDevices)-1];
	}
	
	/**
	 * Returns capabilities and their values for the current device 
	 * @return array Device capabilities array
	 * @see WURFL_Xml_ModelDevice::getCapabilities()
	 */
	public function getAllCapabilities() {
		$capabilities = array ();
		foreach (array_reverse($this->modelDevices) as $modelDevice) {
			$capabilities = array_merge($capabilities, $modelDevice->getCapabilities());
		}
		return $capabilities;
	}
	
	public function getVirtualCapability($name) {
		return $this->virtualCapabilityProvider->get($name);
	}
	
	public function getAllVirtualCapabilities() {
		return $this->virtualCapabilityProvider->getAll();
	}
}