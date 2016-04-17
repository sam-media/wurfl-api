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
 */
/**
 * WURFL File Logger
 */
class WURFL_Logger_ConsoleLogger implements WURFL_Logger_Interface
{
    /**
     * @var string DEBUG Log level
     */
    const DEBUG = 'DEBUG';
    /**
     * @var string INFO Log level
     */
    const INFO = 'INFO';

    public function log($message, $type = '')
    {
        echo $message . PHP_EOL;
    }

    public function info($message)
    {
        $this->log($message, self::INFO);
    }

    public function debug($message)
    {
        $this->log($message, self::DEBUG);
    }

    public function printMessage($message)
    {
        echo $message;
    }
}
