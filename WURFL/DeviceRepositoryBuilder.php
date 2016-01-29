<?php
/**
 * Copyright (c) 2015 ScientiaMobile, Inc.
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
 * 
 */
/**
 * Builds a WURFL_DeviceRepository
 * @package	WURFL
 */
class WURFL_DeviceRepositoryBuilder {
	
	/**
	 * @var WURFL_Storage_Base
	 */
	private $persistenceProvider;
	/**
	 * @var WURFL_UserAgentHandlerChain
	 */
	private $userAgentHandlerChain;
	/**
	 * @var WURFL_Xml_DevicePatcher
	 */
	private $devicePatcher;
	/**
	 * Filename of lockfile to prevent concurrent DeviceRepository builds
	 * @var string
	 */
	private $lockFile;
	/**
	 * True if the repository builder is currently locked
	 * @var string
	 */
	private $isLocked = false;
	/**
	 * If a lock is in place for this long it is assumed to be orphaned and the lock is released
	 * @var int
	 */
	private $maxLockAge = 86400;
	
	/**
	 * @param WURFL_Storage_Base $persistenceProvider
	 * @param WURFL_UserAgentHandlerChain $userAgentHandlerChain
	 * @param WURFL_Xml_DevicePatcher $devicePatcher
	 */
	public function __construct($persistenceProvider, $userAgentHandlerChain, $devicePatcher) {
		$this->persistenceProvider = $persistenceProvider;
		$this->userAgentHandlerChain = $userAgentHandlerChain;
		$this->devicePatcher = $devicePatcher;
		$this->lockFile = WURFL_FileUtils::getTempDir().'/wurfl_builder.lock';
	}
	
	/**
	 * Builds DeviceRepository in PersistenceProvider from $wurflFile and $wurflPatches using $capabilityFilter 
	 * @param string $wurflFile Filename of wurfl.xml or other complete WURFL file
	 * @param array $wurflPatches Array of WURFL patch files
	 * @param array $capabilityFilter Array of capabilities to be included in the DeviceRepository
	 * @return WURFL_CustomDeviceRepository
	 */
	public function build($wurflFile, $wurflPatches = array(), $capabilityFilter = array()) {
		if (!$this->isRepositoryBuilt()) {
			// If acquireLock() is false, the WURFL is being reloaded in another thread
			if ($this->acquireLock()) {
				set_time_limit(600);
				$infoIterator = new WURFL_Xml_VersionIterator($wurflFile);
				$deviceIterator = new WURFL_Xml_DeviceIterator($wurflFile, $capabilityFilter);
				$patchIterators = $this->toPatchIterators($wurflPatches , $capabilityFilter);
				
				$this->buildRepository($infoIterator, $deviceIterator, $patchIterators);
				$this->setRepositoryBuilt();
				$this->releaseLock();
			}
			
		}
		
		$deviceClassificationNames = $this->deviceClassificationNames();
		return new WURFL_CustomDeviceRepository($this->persistenceProvider, $deviceClassificationNames);
	}
	
	public function __destruct() {
		$this->releaseLock();
	}
	
	/**
	 * Acquires a lock so only this thread reloads the WURFL data, returns false if it cannot be acquired
	 * @return boolean
	 */
	private function acquireLock() {
		 
		if (file_exists($this->lockFile)) {
			$stale_after = filemtime($this->lockFile) + $this->maxLockAge;
			if (time() > $stale_after) {
				// The lockfile is stale, delete it and reacquire a lock
				@rmdir($this->lockFile);
			} else {
				// The lockfile is valid, WURFL is probably being reloaded in another thread
				return false;
			}
		}
		
		// Using mkdir instead of touch since mkdir is atomic
		$this->isLocked = @mkdir($this->lockFile, 0775);
		return $this->isLocked;
	}
	
	/**
	 * Releases the lock if one was acquired
	 */
	private function releaseLock() {
		if (!$this->isLocked) {
			return;
		}
		
		@rmdir($this->lockFile);
		$this->isLocked = false;
	}
	
	/**
	 * Iterates over XML files and pulls relevent data
	 * @param WURFL_Xml_VersionIterator $wurflInfoIterator
	 * @param WURFL_Xml_DeviceIterator $deviceIterator
	 * @param array $patchDeviceIterators arrray of WURFL_Xml_DeviceIterator objects for patch files 
	 * @throws Exception
	 */
	private function buildRepository($wurflInfoIterator, $deviceIterator, $patchDeviceIterators = null) {
		$this->persistWurflInfo($wurflInfoIterator);
		$patchingDevices = $this->toListOfPatchingDevices($patchDeviceIterators);		
		try {
			$this->process($deviceIterator, $patchingDevices);
		} catch(Exception $exception) {
			$this->clean();
			throw new Exception("Problem Building WURFL Repository: " . $exception->getMessage(), 0, $exception);
		}
	}
	
	/**
	 * Returns an array of WURFL_Xml_DeviceIterator for the given $wurflPatches and $capabilityFilter
	 * @param array $wurflPatches Array of (string)filenames
	 * @param array $capabilityFilter Array of (string) WURFL capabilities
	 * @return array Array of WURFL_Xml_DeviceIterator objects
	 */
	private function toPatchIterators($wurflPatches, $capabilityFilter) {
		$patchIterators = array();
		if (is_array($wurflPatches)) {
			foreach ($wurflPatches as $wurflPatch) {
				$patchIterators[] = new WURFL_Xml_DeviceIterator($wurflPatch, $capabilityFilter);
			}
		}
		return $patchIterators;
	}
	
	/**
	 * @return bool true if device repository is already built (WURFL is loaded in persistence proivder)
	 */
	private function isRepositoryBuilt() {
		return $this->persistenceProvider->isWURFLLoaded();
	}
	
	/**
	 * Marks the WURFL as loaded in the persistence provider
	 * @see WURFL_Storage_Base::setWURFLLoaded()
	 */
	private function setRepositoryBuilt() {
		$this->persistenceProvider->setWURFLLoaded();
	}
	
	/**
	 * @return array Array of (string)User Agent Handler prefixes
	 * @see WURFL_Handlers_Handler::getPrefix()
	 */
	private function deviceClassificationNames() {
		$deviceClusterNames = array();
		foreach ($this->userAgentHandlerChain->getHandlers() as $userAgentHandler) {
			$deviceClusterNames[] = $userAgentHandler->getPrefix();
		}
		return $deviceClusterNames;
	}
	
	/**
	 * Clears the devices from the persistence provider
	 * @see WURFL_Storage_Base::clear()
	 */
	private function clean() {
		$this->persistenceProvider->clear();
	}
	
	/**
	 * Save Loaded WURFL info in the persistence provider 
	 * @param WURFL_Xml_VersionIterator $wurflInfoIterator
	 */
	private function persistWurflInfo($wurflInfoIterator) {
		foreach ($wurflInfoIterator as $info) {
			$this->persistenceProvider->save(WURFL_Xml_Info::PERSISTENCE_KEY, $info);
			return;
		}
	}
	
	/**
	 * Process device iterator
	 * @param WURFL_Xml_DeviceIterator $deviceIterator
	 * @param array $patchingDevices
	 */
	private function process($deviceIterator, $patchingDevices) {
		$usedPatchingDeviceIds = array();
		foreach ($deviceIterator as $device) {
			/* @var $device WURFL_Xml_ModelDevice */
			$toPatch = isset($patchingDevices [$device->id]);
			if ($toPatch) {
				$device = $this->patchDevice($device, $patchingDevices [$device->id]);
				$usedPatchingDeviceIds [$device->id] = $device->id;
			}
			$this->classifyAndPersistDevice($device);
		}
		$this->classifyAndPersistNewDevices(array_diff_key($patchingDevices, $usedPatchingDeviceIds));
		$this->persistClassifiedDevicesUserAgentMap();
	}
	
	/**
	 * Save all $newDevices in the persistence provider
	 * @param array $newDevices Array of WURFL_Device objects
	 */
	private function classifyAndPersistNewDevices($newDevices) {
		foreach ($newDevices as $newDevice) {
			$this->classifyAndPersistDevice($newDevice);
		}
	}

	/**
	 * Save given $device in the persistence provider.  This is called when loading the WURFL XML
	 * data, directly after reading the complete device node.
	 * @param WURFL_Xml_ModelDevice $device
	 * @see WURFL_UserAgentHandlerChain::filter(), WURFL_Storage_Base::save()
	 */
	private function classifyAndPersistDevice($device) {
		$this->userAgentHandlerChain->filter($device->userAgent, $device->id);
		$this->persistenceProvider->save($device->id, $device);
	}
	
	/**
	 * Save the User Agent Map in the UserAgentHandlerChain
	 * @see WURFL_UserAgentHandlerChain::persistData()
	 */
	private function persistClassifiedDevicesUserAgentMap() {
		$this->userAgentHandlerChain->persistData();
	}
	
	private function patchDevice($device, $patchingDevice) {
		return $this->devicePatcher->patch($device, $patchingDevice);
	}
	
	/**
	 * @param array $patchingDeviceIterators Array of WURFL_Xml_DeviceIterators
	 * @return array Merged array of current patch devices
	 */
	private function toListOfPatchingDevices($patchingDeviceIterators) {
		$currentPatchingDevices = array();
		if (is_null($patchingDeviceIterators)) {
			return $currentPatchingDevices;
		}
		foreach ($patchingDeviceIterators as $deviceIterator) {
			$newPatchingDevices = $this->toArray($deviceIterator);
			$this->patchDevices($currentPatchingDevices, $newPatchingDevices);
		}
		return $currentPatchingDevices;
	}
	
	/**
	 * Adds the given $newPatchingDevices to the $currentPatchingDevices array
	 * @param array $currentPatchingDevices REFERENCE to array of current devices to be patches
	 * @param array $newPatchingDevices Array of new devices to be patched in
	 */
	private function patchDevices(&$currentPatchingDevices, $newPatchingDevices) {
		foreach ($newPatchingDevices as $deviceId => $newPatchingDevice) {
			if (isset($currentPatchingDevices[$deviceId])) {
				$currentPatchingDevices[$deviceId] = $this->patchDevice($currentPatchingDevices[$deviceId], $newPatchingDevice);
			} else {
				$currentPatchingDevices[$deviceId] = $newPatchingDevice;
			}
		}
	}
	
	/**
	 * Returns an array of devices in the form "WURFL_Device::id => WURFL_Device"
	 * @param WURFL_Xml_DeviceIterator $deviceIterator
	 * @return array
	 */
	private function toArray($deviceIterator) {
		$patchingDevices = array();
		foreach ($deviceIterator as $device) {
			$patchingDevices[$device->id] = $device;
		}
		return $patchingDevices;
	}

}
