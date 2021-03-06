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
 * User Agent Normalizer - Returns the Thunderbird/{Version} sub-string
 */
class WURFL_Request_UserAgentNormalizer_Specific_DesktopApplication implements WURFL_Request_UserAgentNormalizer_Interface
{
    public function normalize($userAgent)
    {
        $idx = strpos($userAgent, 'Thunderbird');
        if ($idx !== false) {
            return substr($userAgent, $idx);
        }

        return $userAgent;
    }
}
