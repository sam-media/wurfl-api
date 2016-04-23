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
 * @license    GNU Affero General Public License
 * @author     Fantayeneh Asres Gizaw
 */

/**
 * WURFL Storage
 */
class WURFL_Storage_Redis extends WURFL_Storage_Base
{
    const EXTENSION_MODULE_NAME = 'redis';

    private $defaultParams = array(
        'host'      => '127.0.0.1',
        'port'      => '6379',
        'hash_name' => 'WURFL_DATA',
        'redis'     => null,
        'database'  => 0,
        'client'    => 'phpredis',
    );

    private $database;
    private $host;
    private $port;
    private $redis;
    private $hashName;
    private $client;

    protected $supports_secondary_caching = true;

    public function __construct($params)
    {
        $currentParams = is_array($params) ? array_merge($this->defaultParams, $params) : $this->defaultParams;
        $this->initialize($currentParams);
    }

    private function checkRedisInstance($redis)
    {
        if (extension_loaded(self::EXTENSION_MODULE_NAME) && ($redis instanceof Redis)) {
            return true;
        }
        if (class_exists('\Predis\Client') && ($redis instanceof \Predis\Client)) {
            return true;
        }
        throw new WURFL_Storage_Exception(
            'Connection object is not a Redis or a Predis\Client instance'
        );
    }

    private function checkClient($client)
    {
        if (!in_array($client, array('phpredis', 'predis'))) {
            throw new WURFL_Storage_Exception(
                'Redis client must be phpredis or predis'
            );
        }

        return $client;
    }

    private function buildRedisObject($client, $host, $port, $database = 0)
    {
        if ($client === 'phpredis') {
            $redis = new Redis();
            $redis->connect($host, $port);
        } elseif ($client === 'predis') {
            $redis = new Predis\Client(
                array('scheme' => 'tcp', 'host' => $host, 'port' => $port)
            );
            $redis->connect();
        }
        if ($database) {
            $redis->select($database);
        }

        return $redis;
    }

    public function initialize($params)
    {
        $this->host     = $params['host'];
        $this->port     = $params['port'];
        $this->hashName = $params['hash_name'];
        $this->database = $params['database'];
        $this->client   = $this->checkClient($params['client']);
        if ((null !== $params['redis']) && $this->checkRedisInstance($params['redis'])) {
            // when using this parameter, the Redis object has to be connected
            // and with the correct database selected
            $this->redis = $params['redis'];
        } else {
            $this->redis = $this->buildRedisObject(
                $this->client,
                $this->host,
                $this->port,
                $this->database
            );
        }
    }

    public function load($key)
    {
        $value = $this->redis->hget($this->hashName, $key);

        $returnValue = null;
        if ($value !== false) {
            $returnValue = unserialize($value);
            if ($returnValue instanceof StorageObject) {
                $returnValue = $returnValue->value();
            }
        }

        return $returnValue;
    }

    public function save($key, $value, $expiration = null)
    {
        if (!is_object($value)) {
            $value = new StorageObject($value, 0);
        }

        return (bool) $this->redis->hset($this->hashName, $key, serialize($value));
    }

    public function clear()
    {
        return (bool) $this->redis->del($this->hashName);
    }
}
