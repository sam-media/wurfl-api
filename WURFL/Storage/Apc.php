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
 * @copyright  ScientiaMobile, Inc.
 * @license     GNU Affero General Public License
 * @author   Fantayeneh Asres Gizaw
 */
/**
 * APC Storage class
 */
class WURFL_Storage_Apc extends WURFL_Storage_Base
{
    const EXTENSION_MODULE_NAME = 'apc';

    private $expiration;
    private $namespace;

    private $defaultParams = array(
        'namespace'  => 'wurfl',
        'expiration' => 0,
    );

    protected $is_volatile = true;

    public function __construct($params = array())
    {
        $currentParams = is_array($params) ? array_merge($this->defaultParams, $params) : $this->defaultParams;
        foreach ($currentParams as $key => $value) {
            $this->$key = $value;
        }
        $this->initialize();
    }

    public function initialize()
    {
        $this->ensureModuleExistence();
    }

    public function save($objectId, $object, $expiration = null)
    {
        $value = apc_store($this->encode($this->apcNameSpace(), $objectId), $object, (($expiration === null) ? $this->expire() : $expiration));
        if ($value === false) {
            throw new WURFL_Storage_Exception('Error saving variable in APC cache. Cache may be full.');
        }
    }

    public function load($objectId)
    {
        $value = apc_fetch($this->encode($this->apcNameSpace(), $objectId));

        return ($value !== false) ? $value : null;
    }

    public function remove($objectId)
    {
        apc_delete($this->encode($this->apcNameSpace(), $objectId));
    }

    /**
     * Removes all entry from the Persistence Provider
     */
    public function clear()
    {
        apc_clear_cache('user');
    }

    private function apcNameSpace()
    {
        return $this->namespace;
    }

    private function expire()
    {
        return $this->expiration;
    }

    /**
     * Ensures the existence of the the PHP Extension apc
     * @throws WURFL_Storage_Exception required extension is unavailable
     */
    private function ensureModuleExistence()
    {var_dump(extension_loaded(self::EXTENSION_MODULE_NAME), ini_get('apc.enable_cli'));
        if (!extension_loaded(self::EXTENSION_MODULE_NAME) || ini_get('apc.enable_cli') === false) {
            throw new WURFL_Storage_Exception('The PHP extension apc must be installed, loaded and enabled.');
        }
    }
}
