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
 *
 * @category   WURFL
 * @copyright  ScientiaMobile, Inc.
 * @license     GNU Affero General Public License
 */

/**
 * UcwebU3UserAgentHandler
 *
 *
 * @category   WURFL
 * @copyright  ScientiaMobile, Inc.
 * @license     GNU Affero General Public License
 */
class WURFL_Handlers_UcwebU3Handler extends WURFL_Handlers_Handler
{
    protected $prefix = 'UCWEBU3';

    public static $constantIDs = array(
        'generic_ucweb',

        'generic_ucweb_android_ver1',
        'generic_ucweb_android_ver2',
        'generic_ucweb_android_ver3',
        'generic_ucweb_android_ver4',
        'generic_ucweb_android_ver5',

        'apple_iphone_ver1_subuaucweb',
        'apple_iphone_ver2_subuaucweb',
        'apple_iphone_ver3_subuaucweb',
        'apple_iphone_ver4_subuaucweb',
        'apple_iphone_ver5_subuaucweb',
        'apple_iphone_ver6_subuaucweb',
        'apple_iphone_ver7_subuaucweb',
        'apple_iphone_ver8_subuaucweb',
        'apple_iphone_ver9_subuaucweb',

        'apple_ipad_ver1_subuaucweb',
        'apple_ipad_ver1_sub4_subuaucweb',
        'apple_ipad_ver1_sub5_subuaucweb',
        'apple_ipad_ver1_sub6_subuaucweb',
        'apple_ipad_ver1_sub7_subuaucweb',
        'apple_ipad_ver1_sub8_subuaucweb',
        'apple_ipad_ver1_sub9_subuaucweb',

        'generic_ms_phone_os8_subuaucweb',
        'generic_ms_phone_os8_1_subuaucweb',
        'generic_ms_phone_os10_subuaucweb',
    );

    public function canHandle($userAgent)
    {
        if (WURFL_Handlers_Utils::isDesktopBrowser($userAgent)) {
            return false;
        }

        return (WURFL_Handlers_Utils::checkIfStartsWith($userAgent, 'Mozilla') && WURFL_Handlers_Utils::checkIfContains($userAgent, 'UCBrowser'));
    }

    public function applyConclusiveMatch($userAgent)
    {
        $tolerance = WURFL_Handlers_Utils::toleranceToRisDelimeter($userAgent);
        if ($tolerance !== false) {
            return $this->getDeviceIDFromRIS($userAgent, $tolerance);
        }

        return WURFL_Constants::NO_MATCH;
    }

    public function applyRecoveryMatch($userAgent)
    {

        // Windows Phone U3K
        if ($deviceID = $this->applyRecoveryWindowsPhone($userAgent)) {
            return $deviceID;
        }

        // Android U3K Mobile + Tablet. This will also handle UCWEB7 recovery and point it to the UCWEB generic IDs.
        if ($deviceID = $this->applyRecoveryAndroid($userAgent)) {
            return $deviceID;
        }

        // iPhone U3K
        if ($deviceID = $this->applyRecoveryiPhone($userAgent)) {
            return $deviceID;
        }

        // iPad U3K
        if ($deviceID = $this->applyRecoveryiPad($userAgent)) {
            return $deviceID;
        }

        return 'generic_ucweb';
    }

    private function applyRecoveryWindowsPhone($userAgent)
    {
        if (!WURFL_Handlers_Utils::checkIfContains($userAgent, 'Windows Phone')) {
            return null;
        }
        $version             = WURFL_Handlers_WindowsPhoneHandler::getWindowsPhoneVersion($userAgent);
        $significant_version = explode('.', $version);
        //Make sure major and minor versions are both present
        if (count($significant_version) >= 2) {
            $major = $significant_version[0];
            $minor = $significant_version[1];
            //If there is no minor version
            if ($minor === 0) {
                $deviceID = 'generic_ms_phone_os' . $major . '_subuaucweb';
            } else {
                $deviceID = 'generic_ms_phone_os' . $major . '_' . $minor . '_subuaucweb';
            }
            if (in_array($deviceID, self::$constantIDs)) {
                return $deviceID;
            }
        }

        return 'generic_ms_phone_os8_subuaucweb';
    }

    private function applyRecoveryAndroid($userAgent)
    {
        if (!WURFL_Handlers_Utils::checkIfContains($userAgent, 'Android')) {
            return null;
        }
        $version             = WURFL_Handlers_AndroidHandler::getAndroidVersion($userAgent, false);
        $significant_version = explode('.', $version);
        //We only care about major version
        if (count($significant_version) >= 1) {
            $deviceID = 'generic_ucweb_android_ver' . $significant_version[0];
            if (in_array($deviceID, self::$constantIDs)) {
                return $deviceID;
            }
        }

        return 'generic_ucweb_android_ver1';
    }

    private function applyRecoveryiPhone($userAgent)
    {
        if (!WURFL_Handlers_Utils::checkIfContains($userAgent, 'iPhone')) {
            return null;
        }
        if (preg_match('/iPhone OS (\d+)(?:_\d+)?.+ like/', $userAgent, $matches)) {
            $significant_version = $matches[1];
            $deviceID            = 'apple_iphone_ver' . $significant_version . '_subuaucweb';
            if (in_array($deviceID, self::$constantIDs)) {
                return $deviceID;
            }
        }

        return 'apple_iphone_ver1_subuaucweb';
    }

    private function applyRecoveryiPad($userAgent)
    {
        if (!WURFL_Handlers_Utils::checkIfContains($userAgent, 'iPad')) {
            return null;
        }
        if (preg_match('/CPU OS (\d+)(?:_\d+)?.+like Mac/', $userAgent, $matches)) {
            $significant_version = $matches[1];
            $deviceID            = 'apple_ipad_ver1_sub' . $significant_version . '_subuaucweb';
            if (in_array($deviceID, self::$constantIDs)) {
                return $deviceID;
            }
        }

        return 'apple_ipad_ver1_subuaucweb';
    }

    public static function getUcBrowserVersion($ua, $use_default = true)
    {
        if (preg_match('/UCBrowser\/(\d+)\.\d/', $ua, $matches)) {
            $uc_version = $matches[1];

            return $uc_version;
        }

        return null;
    }

    public static function getUcAndroidVersion($ua, $use_default = true)
    {
        if (preg_match('/; Adr (\d+\.\d+)\.?/', $ua, $matches)) {
            $u2k_an_version = $matches[1];
            if (in_array($u2k_an_version, WURFL_Handlers_AndroidHandler::$validAndroidVersions)) {
                return $u2k_an_version;
            }
        }

        return $use_default ? WURFL_Handlers_AndroidHandler::ANDROID_DEFAULT_VERSION : null;
    }

    //Slightly modified from Android's get model function
    public static function getUcAndroidModel($ua, $use_default = true)
    {
        // Locales are optional for matching model name since UAs like Chrome Mobile do not contain them
        if (!preg_match('#Adr [\d\.]+; [a-zA-Z]+-[a-zA-Z]+; (.*)\) U2#', $ua, $matches)) {
            return null;
        }

        $model = $matches[1];

        // HTC
        if (strpos($model, 'HTC') !== false) {
            // Normalize "HTC/"
            $model = preg_replace('#HTC[ _\-/]#', 'HTC~', $model);
            // Remove the version
            $model = preg_replace('#(/| +V?\d)[\.\d]+$#', '', $model);
            $model = preg_replace('#/.*$#', '', $model);
        }
        // Samsung
        $model = preg_replace('#(SAMSUNG[^/]+)/.*$#', '$1', $model);
        // Orange
        $model = preg_replace('#ORANGE/.*$#', 'ORANGE', $model);
        // LG
        $model = preg_replace('#(LG-[A-Za-z0-9\-]+).*$#', '$1', $model);
        // Serial Number
        $model = preg_replace('#\[[\d]{10}\]#', '', $model);

        $model = trim($model);

        return (strlen($model) === 0) ? null : $model;
    }
}
