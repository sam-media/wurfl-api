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
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */

/**
 * BotCrawlerTranscoderUserAgentHandler
 * 
 *
 * @category   WURFL
 * @package	WURFL_Handlers
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
class WURFL_Handlers_BotCrawlerTranscoderHandler extends WURFL_Handlers_Handler
{
    protected $prefix = 'BOT_CRAWLER_TRANSCODER';
    
    public function canHandle($userAgent)
    {
        return WURFL_Handlers_Utils::isRobot($userAgent);
    }

    public function applyConclusiveMatch($userAgent)
    {
        if (WURFL_Handlers_Utils::checkIfContains($userAgent, "GoogleImageProxy")) {
            return 'google_image_proxy';
        }

        if (WURFL_Handlers_Utils::checkIfStartsWith($userAgent, "Mozilla")) {
            $tolerance = WURFL_Handlers_Utils::firstCloseParen($userAgent);
        } else {
            $tolerance = WURFL_Handlers_Utils::firstSlash($userAgent);
        }

        return $this->getDeviceIDFromRIS($userAgent, $tolerance);
    }

    public function applyRecoveryMatch($userAgent)
    {
        return WURFL_Constants::GENERIC_WEB_CRAWLER;
    }
}
