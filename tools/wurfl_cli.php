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
 * @package    WURFL
 * @copyright  ScientiaMobile, Inc.
 * @author     Steve Kamerman <steve AT scientiamobile.com>
 * @author     Luca Corbo <luca.corbo AT scientiamobile.com>
 * @license    GNU Affero General Public License
 * @version    $id$
 */
/**
 * Include required files
 */

define("WURFL_DIR", dirname(__FILE__) . '/../WURFL/');
define("WURFL_DB_DIR", dirname(__FILE__) . '/../examples/resources/');
define("RESOURCES_DIR", WURFL_DIR . "../tests/resources/");

require_once WURFL_DIR.'/Application.php';

ini_set('display_errors', 'on');
ini_set('memory_limit', '512M');
error_reporting(E_ALL);

$cli = new WURFL_Utils_CLI();
$cli->processArguments();
