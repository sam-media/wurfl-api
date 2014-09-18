<?php
/**
 * Copyright (c) 2014 ScientiaMobile, Inc.
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
 * @package    WURFL_VirtualCapability
 * @copyright  ScientiaMobile, Inc.
 * @license    GNU Affero General Public License
 * @version    $id$
 */

/**
 * Virtual capability helper
 * @package    WURFL_VirtualCapability
 */
class WURFL_VirtualCapability_FormFactor extends WURFL_VirtualCapability
{

    protected $required_capabilities = array(
        'ux_full_desktop',
        'is_smarttv',
        'is_wireless_device',
        'is_tablet',
        'can_assign_phone_number',
    );
    
    public function compute() {
        $map = array(
            'Robot' => $this->device->is_robot,
            'Desktop' => $this->device->ux_full_desktop,
            'Smart-TV' => $this->device->is_smarttv,
            'Other Non-Mobile' => !$this->device->is_wireless_device,
            'Tablet' => $this->device->is_tablet,
            'Smartphone' => $this->device->is_smartphone,
            'Feature Phone' => $this->device->can_assign_phone_number,
        );

        foreach ($map as $type => $condition) {
            if ($condition == "true") {
                return $type;
            }
        }

        return 'Feature Phone';
    }
}