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

class WURFL_Handlers_CatchAllRisHandler extends WURFL_Handlers_Handler {
    protected $prefix = "CATCH_ALL_RIS";
	/**
	 * Final Interceptor: Intercept
	 * Everything that has not been trapped by a previous handler
	 *
	 * @param string $userAgent
	 * @return boolean always true
	 */
	public function canHandle($userAgent) {
		return true;
	}
	
	/**
	 * Apply RIS on Firts slash
	 *
	 * @param string $userAgent
	 * @return string
	 */
    public function applyConclusiveMatch($userAgent) {
        if (WURFL_Handlers_Utils::checkIfStartsWith($userAgent, "CFNetwork/")) {
            $tolerance = WURFL_Handlers_Utils::firstSpace($userAgent);
        } else {
            $tolerance = WURFL_Handlers_Utils::firstSlash($userAgent);
        }
        return $this->getDeviceIDFromRIS($userAgent, $tolerance);
    }
}
