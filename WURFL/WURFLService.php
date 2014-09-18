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
 * @package	WURFL
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */

/**
 * WURFL Service
 * @package	WURFL
 */
class WURFL_WURFLService {
	
	/**
	 * @var WURFL_DeviceRepository
	 */
	private $_deviceRepository;
	/**
	 * @var WURFL_UserAgentHandlerChain
	 */
	private $_userAgentHandlerChain;
	/**
	 * @var WURFL_Storage
	 */
	private $_cacheProvider;
	
	public function __construct(WURFL_DeviceRepository $deviceRepository, WURFL_UserAgentHandlerChain $userAgentHandlerChain, WURFL_Storage $cacheProvider) {
		$this->_deviceRepository = $deviceRepository;
		$this->_userAgentHandlerChain = $userAgentHandlerChain;
		$this->_cacheProvider = $cacheProvider;
	}
	
	/**
	 * Returns the version info about the loaded WURFL
	 * @return WURFL_Xml_Info WURFL Version info
	 * @see WURFL_DeviceRepository::getWURFLInfo()
	 */
	public function getWURFLInfo() {
		return $this->_deviceRepository->getWURFLInfo();
	}
	
	/**
	 * Returns the Device for the given WURFL_Request_GenericRequest
	 *
	 * @param WURFL_Request_GenericRequest $request
	 * @return WURFL_CustomDevice
	 */
	public function getDeviceForRequest(WURFL_Request_GenericRequest $request) {
		$deviceId = $this->deviceIdForRequest($request);
		return $this->getWrappedDevice($deviceId, $request);
	
	}
	
	/**
	 * Retun a WURFL_Xml_ModelDevice for the given device id
	 *
	 * @param string $deviceID
	 * @param WURFL_Request_GenericRequest $request
	 * @return WURFL_Xml_ModelDevice
	 */
	public function getDevice($deviceID, $request=null) {
		return $this->getWrappedDevice($deviceID, $request);
	}
	
	/**
	 * Returns all devices ID present in WURFL
	 *
	 * @return array of strings
	 */
	public function getAllDevicesID() {
		return $this->_deviceRepository->getAllDevicesID();
	}
	
	/**
	 * Returns an array of all the fall back devices starting from
	 * the given device
	 *
	 * @param string $deviceID
	 * @return array
	 */
	public function getDeviceHierarchy($deviceID) {
		return $this->_deviceRepository->getDeviceHierarchy($deviceID);
	}
	
	public function getListOfGroups() {
		return $this->_deviceRepository->getListOfGroups();
	}
	
	
	public function getCapabilitiesNameForGroup($groupId) {
		return $this->_deviceRepository->getCapabilitiesNameForGroup($groupId);
	}
	
	// ******************** private functions *****************************
	

	/**
	 * Returns the device id for the device that matches the $request
	 * @param WURFL_Request_GenericRequest $request WURFL Request object
	 * @return string WURFL device id
	 */
	private function deviceIdForRequest($request) {
		$deviceId = $this->_cacheProvider->load($request->id);
		if (empty($deviceId)) {
			$deviceId = $this->_userAgentHandlerChain->match($request);
			// save it in cache
			$this->_cacheProvider->save($request->id, $deviceId);
		} else {
			$request->matchInfo->from_cache = true;
			$request->matchInfo->lookup_time = 0.0;
		}
		return $deviceId;
	}
	
	/**
	 * Wraps the model device with WURFL_Xml_ModelDevice.  This function takes the
	 * Device ID and returns the WURFL_CustomDevice with all capabilities.
	 *
	 * @param string $deviceID
	 * @param WURFL_Request_GenericRequest|null $request
	 * @return WURFL_CustomDevice
	 */
	private function getWrappedDevice($deviceID, $request = null) {
		$device = $this->_cacheProvider->load('DEV_'.$deviceID);
		if (empty($device)) {
			$modelDevices = $this->_deviceRepository->getDeviceHierarchy($deviceID);
			$device = new WURFL_CustomDevice($modelDevices, $request);
			$this->_cacheProvider->save('DEV_'.$deviceID, $device);
		}
		return $device;
	}
}

