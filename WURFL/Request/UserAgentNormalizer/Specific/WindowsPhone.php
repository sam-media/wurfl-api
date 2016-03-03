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
 * @package	WURFL_Request_UserAgentNormalizer_Specific
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @author	 Fantayeneh Asres Gizaw
 * @version	$id$
 */
/**
 * User Agent Normalizer
 * @package	WURFL_Request_UserAgentNormalizer_Specific
 */
class WURFL_Request_UserAgentNormalizer_Specific_WindowsPhone implements WURFL_Request_UserAgentNormalizer_Interface
{
    public function normalize($userAgent)
    {
        if (WURFL_Handlers_Utils::checkIfContainsAnyOf($userAgent, array('WPDesktop', 'ZuneWP7'))
          || WURFL_Handlers_Utils::checkIfContainsAll($userAgent, array('Mozilla/5.0 (Windows NT ', ' ARM;', ' Edge/'))) {
            $model = WURFL_Handlers_WindowsPhoneHandler::getWindowsPhoneDesktopModel($userAgent);
            $version = WURFL_Handlers_WindowsPhoneHandler::getWindowsPhoneDesktopVersion($userAgent);
        } elseif (WURFL_Handlers_Utils::checkIfStartsWithAnyOf($userAgent, array('Windows Phone Ad Client', 'WindowsPhoneAdClient'))) {
            $model = WURFL_Handlers_WindowsPhoneHandler::getWindowsPhoneAdClientModel($userAgent);
            $version = WURFL_Handlers_WindowsPhoneHandler::getWindowsPhoneVersion($userAgent);
        } elseif (WURFL_Handlers_Utils::checkIfContains($userAgent, 'NativeHost')) {
            return $userAgent;
        } else {
            $model = WURFL_Handlers_WindowsPhoneHandler::getWindowsPhoneModel($userAgent);
            $version = WURFL_Handlers_WindowsPhoneHandler::getWindowsPhoneVersion($userAgent);
        }
        if ($model !== null && $version !== null) {
            // "WP" is for Windows Phone
            $prefix = 'WP'.$version.' '.$model.WURFL_Constants::RIS_DELIMITER;
            return $prefix.$userAgent;
        }
        return $userAgent;
    }
}
