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
 * ReksioUserAgentHandler
 *
 *
 * @category   WURFL
 * @copyright  ScientiaMobile, Inc.
 * @license     GNU Affero General Public License
 */
class WURFL_Handlers_ReksioHandler extends WURFL_Handlers_Handler
{
    protected $prefix = 'REKSIO';

    public static $constantIDs = array(
        'generic_reksio',
    );

    public function canHandle($userAgent)
    {
        if (WURFL_Handlers_Utils::isDesktopBrowser($userAgent)) {
            return false;
        }

        return WURFL_Handlers_Utils::checkIfStartsWith($userAgent, 'Reksio');
    }

    public function applyConclusiveMatch($userAgent)
    {
        return 'generic_reksio';
    }
}
