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
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */

/**
 * OperaHandlder
 *
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
class WURFL_Handlers_OperaMiniHandler extends WURFL_Handlers_Handler
{
    protected $prefix = "OPERA_MINI";
    
    public static $constantIDs = array(
        'Opera Mini/1' => 'generic_opera_mini_version1',
        'Opera Mini/2' => 'generic_opera_mini_version2',
        'Opera Mini/3' => 'generic_opera_mini_version3',
        'Opera Mini/4' => 'generic_opera_mini_version4',
        'Opera Mini/5' => 'generic_opera_mini_version5',
        'Opera Mini/6' => 'generic_opera_mini_version6',
        'Opera Mini/7' => 'generic_opera_mini_version7',
    );

    public function canHandle($userAgent)
    {
        if (WURFL_Handlers_Utils::isDesktopBrowser($userAgent)) {
            return false;
        }
        return WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent, array('Opera Mini', 'OperaMini', 'Opera Mobi', 'OperaMobi'));
    }
    
    public function applyConclusiveMatch($userAgent)
    {
        $model = self::getOperaModel($userAgent, false);

        if ($model !== null) {
            $prefix = $model . WURFL_Constants::RIS_DELIMITER;
            $userAgent = $prefix . $userAgent;

            return $this->getDeviceIDFromRIS($userAgent, strlen($prefix));
        }

        $opera_mini_idx = WURFL_Handlers_Utils::indexOfOrLength($userAgent, 'Opera Mini');

        if ($opera_mini_idx !== false) {
            // Match up to the first '.' after 'Opera Mini'
            $tolerance = strpos($userAgent, '.', $opera_mini_idx);
            if ($tolerance !== false) {
                // +1 to match just after the '.'
                return $this->getDeviceIDFromRIS($userAgent, $tolerance + 1);
            }
        }
        $tolerance = WURFL_Handlers_Utils::firstSlash($userAgent);
        return $this->getDeviceIDFromRIS($userAgent, $tolerance);
    }
    
    public function applyRecoveryMatch($userAgent)
    {
        foreach (self::$constantIDs as $keyword => $device_id) {
            if (WURFL_Handlers_Utils::checkIfContains($userAgent, $keyword)) {
                return $device_id;
            }
        }
        if (WURFL_Handlers_Utils::checkIfContains($userAgent, 'Opera Mobi')) {
            return 'generic_opera_mini_version4';
        }
        return 'generic_opera_mini_version1';
    }

    /**
    * Get the model name from the provided user agent or null if it cannot be determined
    * @param string $ua
    * @param bool $use_default
    * @return false|string
    */
    public static function getOperaModel($ua, $use_default = true)
    {
        if (preg_match('#^Opera/[\d\.]+ .+?\d{3}X\d{3} (.+)$#', $ua, $matches)) {
            return $matches[1];
        }
    }
}
