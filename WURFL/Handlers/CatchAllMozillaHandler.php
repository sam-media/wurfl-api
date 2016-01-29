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
 * CatchAllUserAgentHandler
 *
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */

class WURFL_Handlers_CatchAllMozillaHandler extends WURFL_Handlers_Handler {
    protected $prefix = "CATCH_ALL_MOZILLA";
	/**
	 * Final Interceptor: Intercept
	 * Everything that has not been trapped by a previous handler
	 *
	 * @param string $userAgent
	 * @return boolean always true
	 */
    public function canHandle($userAgent) {
        return WURFL_Handlers_Utils::checkIfStartsWithAnyOf($userAgent, array('Mozilla/3', 'Mozilla/4', 'Mozilla/5'));
    }
	
	/**
	 * If UA starts with Mozilla, apply LD with tolerance 5.
	 *
	 * @param string $userAgent
	 * @return string
	 */
	public function applyConclusiveMatch($userAgent) {
        if (WURFL_Configuration_ConfigHolder::getWURFLConfig()->isHighPerformance()) {
            //High performance mode
            $tolerance = WURFL_Handlers_Utils::firstCloseParen($userAgent);
            return $this->getDeviceIDFromRIS($userAgent, $tolerance);
        } else {
            //High accuracy mode
            return $this->getDeviceIDFromLD($userAgent, 5);
        }
	}
}
