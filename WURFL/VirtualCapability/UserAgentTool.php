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
 * @package	WURFL_VirtualCapability
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @version	$id$
 */
/**
 * Standalone utility for deriving device capabilities from a user agent
 * @package WURFL_VirtualCapability
 */
class WURFL_VirtualCapability_UserAgentTool
{
    /**
     * @var WURFL_CustomDevice
     */
    protected $device;

    /**
     * WURFL_VirtualCapability_UserAgentTool constructor.
     * @param \WURFL_CustomDevice $device
     */
    public function __construct(\WURFL_CustomDevice $device)
    {
        $this->device = $device;
    }

    /**
     * Gets a device from the UA
     * @param WURFL_Request_GenericRequest $request
     * @return WURFL_VirtualCapability_UserAgentTool_Device
     */
    public function getDevice(WURFL_Request_GenericRequest $request)
    {
        $device = $this->assignProperties(new WURFL_VirtualCapability_UserAgentTool_Device($request));
        $device->normalize();
        return $device;
    }
    
    /**
     * Gets a device from the UA
     * @param WURFL_VirtualCapability_UserAgentTool_Device $device
     * @return WURFL_VirtualCapability_UserAgentTool_Device
     */
    protected function assignProperties($device)
    {

        //Is UA Windows Mobile?
        if ($device->os->setContains($device->device_ua, 'Windows CE', 'Windows Mobile') && $device->browser->set('IE Mobile')) {
            return $device;
        }

        //Is UA Windows Phone OS? - WP before Android
        if (strpos($device->device_ua, 'Windows Phone') !== false || strpos($device->device_ua, '; wds') !== false) {
            if ($device->os->setRegex($device->device_ua, '/Windows Phone(?: OS)? ([\d\.]+)/', 'Windows Phone', 1) || $device->os->setRegex($device->device_ua, '#UCWEB/\d\.\d \(Windows;.+?; wds ?([\d\.]+?);.+UCBrowser#', 'Windows Phone', 1)) {
                if ($device->browser->setRegex($device->browser_ua, '/UCBrowser\/([\d\.]+)/', 'UC Browser', 1)) {
                    return $device;
                }
                if ($device->browser->setRegex($device->browser_ua, '/IEMobile\/([\d\.]+)/', 'IE Mobile', 1)) {
                    return $device;
                }
                if ($device->browser->setRegex($device->browser_ua, '/Edge\/([\d\.]+)/', 'Edge Mobile', 1)) {
                    return $device;
                }
            }
        }

        //Is UA Android?
        if (strpos($device->device_ua, 'Android') !== false || strpos($device->device_ua, ' Adr ') !== false) {
            $device->os->setRegex($device->device_ua, '#Android(?: |/)([\d\.]+).+#', 'Android', 1);
            $device->os->setRegex($device->device_ua, '# Adr(?: |/)([\d\.]+).+#', 'Android', 1);
            
            //Is Dalvik?
            if (strpos($device->browser_ua, 'Dalvik') !== false) {
                $device->browser->name = 'Android App';
                if ($device->browser->setRegex($device->browser_ua, '/Android ([\d\.]+)/', null, 1)) {
                    return $device;
                }
            }
                        
            //Is FB app?
            if ($device->browser->setRegex($device->browser_ua, '/^Mozilla\/[45]\.0.+?Android.+?AppleWebKit.+FBAN/', 'FaceBook Android App', $device->os->version)) {
                return $device;
            }

            //Is UA Opera?
            if ($device->browser->setRegex($device->browser_ua, '/OPR\/([\d\.]+)/', 'Opera', 1)) {
                return $device;
            }

            //Is 360Browser?
            if (strpos($device->browser_ua, 'Aphone Browser') !== false || strpos($device->browser_ua, '360browser') !== false) {
                $device->browser->set('360 Browser', null);
                return $device;
            }
            
            //Is UA Fennec?
            if ($device->browser->setRegex($device->browser_ua, '/(?:Firefox|Fennec)\/([\d\.]+)/', 'Firefox Mobile', 1)) {
                return $device;
            }
            
            //Is UA Opera Mobi?
            if ($device->browser->setRegex($device->browser_ua, '/Opera Mobi\/.*Version\/([\d\.]+)/', 'Opera Mobile', 1)) {
                return $device;
            }
            
            //Is UA Opera Mini?
            if ($device->browser->setRegex($device->browser_ua, '/Opera Mini\/([\d\.]+)/', 'Opera Mini', 1)) {
                return $device;
            }
            
            //Is UA Opera Tablet?
            if ($device->browser->setRegex($device->browser_ua, '/Opera Tablet\/.*Version\/([\d\.]+)/', 'Opera Tablet', 1)) {
                return $device;
            }
            
            //Is UA UC Browser with UCBrowser tag?
            if ($device->browser->setRegex($device->browser_ua, '/UCBrowser\/([\d\.]+)/', 'UC Browser', 1)) {
                return $device;
            }
            
            //Is UA UC Browser with UCWEB tag?
            if ($device->browser->setRegex($device->browser_ua, '/^JUC.*UCWEB([0-9])/', 'UC Browser', 1)) {
                return $device;
            }
            
            //Is UA Amazon Silk browser?
            if ($device->browser->setRegex($device->browser_ua, '/Silk\/([\d\.]+).+?Silk\-Accelerated/', 'Amazon Silk Browser', 1)) {
                return $device;
            }

            //Is UA Baidu browser?
            if ($device->browser->setRegex($device->browser_ua, '/bdbrowser(?:_i18n)?\/(\d+)/', 'Baidu Browser', 1)) {
                return $device;
            }

            //Is UA Samsung Browser?
            if ($device->browser->setRegex($device->browser_ua, '#SamsungBrowser/([\d\.]+) Chrome/[\d\.]+#', 'Samsung Browser', 1)) {
                return $device;
            }

            //Is UA Chromium?
            if ($device->browser->setRegex($device->browser_ua, '/Version\/.+?Chrome\/([\d\.]+)/', 'Chromium', 1)) {
                return $device;
            }

            //Is UA Chrome Mobile?
            if ($device->browser->setRegex($device->browser_ua, '/Chrome\/([\d\.]+)?/', 'Chrome Mobile', 1)) {
                return $device;
            }

            //Is UA Android Webkit UA
            if ($device->browser->setRegex($device->browser_ua, '/Version\/\d/', 'Android Webkit', $device->os->version)) {
                return $device;
            }

            //Catchall for all other Android UAs
            $device->browser->set('Android', $device->os->version);

            return $device;
        }
        
        //Is UA Amazon Silk browser without the word Android?
        if (strpos($device->device_ua, 'Silk') !== false && $device->browser->setRegex($device->browser_ua, '/Silk\/([\d\.]+).+?Silk\-Accelerated/', 'Amazon Silk Browser', 1)
            && $device->os->set("Android", null)) {
            return $device;
        }
        
        //Is UA iOS?
        if (strpos($device->device_ua_normalized, 'iPhone') !== false || strpos($device->device_ua_normalized, 'iPad') !== false || strpos($device->device_ua_normalized, 'iPod') !== false || strpos($device->device_ua_normalized, '(iOS;') !== false) {
            $device->os->name = 'iOS';

            if ($device->os->setRegex($device->device_ua_normalized, '/Mozilla\/[45]\.0 \((iPhone|iPod|iPad);(?: U;)? CPU(?: iPhone|) OS ([\d_]+) like Mac OS X/', 'iOS', 2)
                || $device->os->setRegex($device->device_ua_normalized, '#^[^/]+?/[\d\.]+? \(i[A-Za-z]+; iOS ([\d\.]+); Scale/[\d\.]+\)#', 'iOS', 1)
                || $device->os->setRegex($device->device_ua_normalized, '#^server-bag \[iPhone OS,([\d\.]+),#', 'iOS', 1)
                || $device->os->setRegex($device->device_ua_normalized, '#^i(?:Phone|Pad|Pod)\d+?,\d+?/([\d\.]+)#', 'iOS', 1)) {
                $device->os->version = str_replace("_", ".", $device->os->version);
            }

            // Get Device OS version for UCBrowser 2K?
            if ($device->os->setRegex($device->device_ua, '#UCWEB/[\d\.]+ \(iOS;.+?OS ([\d_]+);.+UCBrowser/#', 'iOS', 1)) {
                $device->os->version = str_replace("_", ".", $device->os->version);
            }

            //Is UA Chrome Mobile on iOS?
            if ($device->browser->setRegex($device->browser_ua, '/^Mozilla\/[45]\.0.+?like Mac OS X.+?CriOS\/([\d\.]+).+?Mobile\/[0-9A-Za-z]+ Safari\/[0-9A-Za-z]+\./',
                'Chrome Mobile on iOS', 1)) {
                return $device;
            }

            //Is UA Firefox on iOS?
            if ($device->browser->setRegex($device->browser_ua, '/^Mozilla\/[45]\.0.+?like Mac OS X.+?FxiOS\/([\d\.]+).+?Mobile\/[0-9A-Za-z]+ Safari\/[0-9A-Za-z]+\./',
                'Firefox on iOS', 1)) {
                return $device;
            }

            //Is UA Opera Mini on iOS?
            if ($device->browser->setRegex($device->browser_ua, '/^Mozilla\/[45]\.0.+?like Mac OS X.+?OPiOS\/([\d\.]+).+?Mobile\/[0-9A-Za-z]+ Safari\/[0-9A-Za-z]+\./',
                'Opera Mini on iOS', 1)) {
                return $device;
            }

            //Is UA UC Web Browser?
            if ($device->browser->setRegex($device->browser_ua, '/^Mozilla\/[45]\.0.+?OS \d_\d.+?like Mac OS X.+?AppleWebKit.+?.+UCBrowser\/([\d\.]+)/',
                'UC Web Browser on iOS', 1)) {
                return $device;
            }

            // Is UA UC Web Browser 2K?
            if ($device->browser->setRegex($device->browser_ua, '#UCWEB/\d\.\d \(iOS;.+?OS [\d_]+;.+UCBrowser/([\d\.]+)#', 'UC Web Browser on iOS', 1)) {
                return $device;
            }

            //Is UA Facebook on iOS?
            if ($device->browser->setRegex($device->browser_ua, '/^Mozilla\/[45]\.0.+?like Mac OS X.+?AppleWebKit.+?Mobile\/[0-9A-Za-z]+.*FBAN/', 'FaceBook on iOS',
                $device->os->version)) {
                return $device;
            }

            // Is UA iOS Safari?
            if ($device->browser->setRegex($device->browser_ua, '#^Mozilla.+like Mac OS X.+Version/([\d\.]+)#', 'Mobile Safari', 1)) {
                return $device;
            }

            //Catchall for all other iOS UAs including Mobile Safari
            $device->browser->set('Mobile Safari', $device->os->version);

            return $device;
        }
        
        //Is UA S40 Ovi Browser?
        if (strpos($device->device_ua, 'OviBrowser') !== false && $device->browser->setRegex($device->browser_ua, '/\bS40OviBrowser\/([\d\.]+)/', 'S40 Ovi Browser', 1) && $device->os->set('Nokia Series 40')) {
            return $device;
        }
        
        //Is Series60?
        if ($device->os->setRegex($device->device_ua, '#(?:SymbianOS|Series60|S60)/([\d\.]+)#', 'Symbian S60', 1) || $device->os->setRegex($device->device_ua, '#UCWEB/\d\.\d \(Symbian;.+?S60 V(\d+)#', 'Symbian S60', 1)) {
            if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[45]\.0 \(Symbian\/3/', 'Symbian', '^3'));
            
            if ($device->browser->setRegex($device->browser_ua, '/NokiaBrowser\/([\d\.]+)/', 'Symbian S60 Browser', 1)) {
                return $device;
            }

            if ($device->browser->setRegex($device->browser_ua, '/Opera Mobi.+Version\/([\d\.]+)/', 'Opera Mobi', 1)) {
                return $device;
            }

            if ($device->browser->setRegex($device->browser_ua, '#UCWEB/[\d\.]+ \(Symbian;.+?UCBrowser/([\d\.]+)#', 'UC Web Browser on Symbian', 1)) {
                return $device;
            }

            $device->browser->set('Symbian S60 Browser');
            return $device;
        }
        
        //Is UA Blackberry?
        if (strpos($device->device_ua, 'BlackBerry') !== false || strpos($device->device_ua, '(BB10; ') !== false) {
            
            // Set resonable defaults
            $device->os->setRegex($device->device_ua, '/(?:BlackBerry)|(?:^Mozilla\/5.0 \(BB10; )/', 'BlackBerry');
            $device->os->setRegex($device->device_ua, '/^BlackBerry[0-9A-Za-z]+?\/([\d\.]+)/', null, 1);
            
            if ($device->os->setRegex($device->device_ua, '/^BlackBerry[0-9A-Za-z]+?\/([\d\.]+).+?UC Browser\/?([\d\.]+)/', null, 1)) {
                $device->browser->set('UC Web', $device->os->getLastRegexMatch(2));
                return $device;
            }
            
            if ($device->os->setRegex($device->device_ua, '/^UCWEB\/[0-9]\.0.+?; [a-zA-Z][a-zA-Z]?\-[a-zA-Z]?[a-zA-Z]; [0-9]+?\/([\d\.]+).+?UCBrowser\/?([\d\.]+)/', null, 1)) {
                $device->browser->set('UC Web', $device->os->getLastRegexMatch(2));
                return $device;
            }
            
            // Is UA Opera Mini?
            if ($device->browser->setRegex($device->browser_ua, '/Opera Mini\/([\d\.]+)/', 'Opera Mini', 1)) {
                return $device;
            }
            
            if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[45]\.0 \(BlackBerry;(?: U;)? BlackBerry.+?Version\/([\d\.]+)/', null, 1)) {
                $device->browser->set('BlackBerry Browser', $device->os->version);
                return $device;
            }
            
            if ($device->os->setRegex($device->device_ua, '#^Mozilla/[45]\.0 \(BB10; .+?Version/([\d\.]+)#', null, 1)) {
                $device->browser->set('BlackBerry Webkit Browser', $device->os->version);
                return $device;
            }
            
            $device->browser->set('BlackBerry Browser', $device->os->version);

            return $device;
        }
        
        //Is UA RIM Tablet OS?
        if (strpos($device->device_ua, 'RIM Tablet OS') !== false
            && $device->os->setRegex($device->device_ua, '/RIM Tablet OS ([\d\.]+).+?Version\/([\d\.]+)/', 'RIM Tablet OS', 1)) {
            $device->browser->set('RIM OS Browser', $device->os->getLastRegexMatch(2));
            return $device;
        }
        
        //Is UA Netfront?
        if (strpos($device->device_ua, 'NetFront') !== false
            && $device->browser->setRegex($device->browser_ua, '/NetFront\/([\d\.]+)/', 'NetFront', 1)) {
            return $device;
        }
        
        //Is UA Teleca Obigo
        if ($device->browser->setContains($device->device_ua, 'Obigo', 'Teleca Obigo')
            && $device->browser->setRegex($device->browser_ua, '/Obig[a-zA-Z]+?\/(Q[0-9\.ABC]+)/', null, 1)) {
            return $device;
        }
        
        //Is UA Samsung's Bada OS?
        if (strpos($device->device_ua, 'Dolfin') !== false
            && $device->os->setRegex($device->device_ua, '/SAMSUNG.+?\bBada\/([\d\.]+);?.+Dolfin\/([\d\.]+)/', 'Bada', 1)) {
            $device->browser->set('Dolfin Browser', $device->os->getLastRegexMatch(2));
            return $device;
        }
        
        //Is UA a MAUI browser?
        if ($device->browser->setContains($device->device_ua, 'MAUI', 'MAUI Browser')) {
            return $device;
        }
        
        //Is UA an Openwave browser?
        if (strpos($device->device_ua, 'Dolfin') !== false
            && $device->browser->setRegex($device->browser_ua, '/UP\.(?:Browser|Link)\/([\d\.]+)/', 'Openwave Browser', 1)) {
            return $device;
        }
        
        //Is UA webOS?
        if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[45]\.0 \((?:Linux; )?webOS\/([\d\.]+)/', 'webOS', 1)) {
            $device->browser->set('webOS Browser', $device->os->version);
            return $device;
        }
        
        if (strpos($device->device_ua, 'Opera') !== false) {
            //Is UA Opera Mobi?
            if ($device->browser->setContains($device->device_ua, 'Opera Mobi', 'Opera Mobile')) {
                if ($device->browser->setRegex($device->device_ua, '/Opera Mobi.+Version\/([\d\.]+)/', null, 1)) {
                    return $device;
                }
                return $device;
            }
            
            //Is UA Opera Mini?
            if ($device->browser->setRegex($device->device_ua, '/Opera Mini\/([\d\.]+)/', 'Opera Mini', 1)) {
                return $device;
            }
            
            //Is UA Opera Sync?
            if ($device->browser->setRegex($device->device_ua, '/Browser\/Opera Sync\/SyncClient.+?Version\/([\d\.]+)/',
                'Opera Link Sync', 1)) {
                return $device;
            }
        }
        
        if (strpos($device->device_ua, 'Maemo') !== false) {
            $device->os->set('Maemo');
            //Maemo
            if ($device->browser->setRegex($device->browser_ua, '/Maemo.+?Firefox\/([0-9a\.]+) /', 'Firefox', 1)) {
                return $device;
            }
        }

        //UCBrowser on Java devices
        if (strpos($device->device_ua, 'Java') !== false && strpos($device->device_ua, 'UCBrowser/') !== false) {
            if ($device->browser->setRegex($device->browser_ua, '#UCWEB/\d\.\d \(Java;.+?UCBrowser/([\d\.]+)#', 'UCBrowser Java Applet', 1)) {
                return $device;
            }
        }

        //Final ditch effort
        if ($device->browser->setRegex($device->browser_ua, '/(?:MIDP.+?CLDC)|(?:UNTRUSTED)|(?:MIDP-2.0)/', 'Java Applet')) {
            return $device;
        }
        
        
        // Desktop Browsers

        //Baidu browser?
        if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[0-9]\.0 .+?((?:Windows|Linux|PPC|Intel) [a-zA-Z0-9 _\.\-]+).+bdbrowser(?:_i18n)?\/([\d\.]+)/', 1)) {
            $device->browser->set('Baidu Browser', $device->os->getLastRegexMatch(2));
            return $device;
        }

        //360 Browser
        if ((strpos($device->device_ua, '360Browser') !== false || strpos($device->device_ua, ' 360SE') !== false) && $device->os->setRegex($device->device_ua, '/^Mozilla\/[0-9]\.0 .+?((?:Windows|Linux|PPC|Intel) [a-zA-Z0-9 _\.\-]+).+(?:360Browser|360SE)/', 1)) {
            $device->browser->set('360 Browser', null);
            return $device;
        }

        //MSIE - If UA says MSIE
        if (strpos($device->device_ua, 'MSIE') !== false) {
            if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[0-9]\.0 \(compatible; MSIE ([\d\.]+); ((?:Windows NT [0-9]+\.[0-9])|(?:Windows [0-9]\.[0-9])|(?:Windows [0-9]+)|(?:Mac_PowerPC))/', 2)) {
                $device->browser->set('IE', $device->os->getLastRegexMatch(1));
                return $device;
            }
        }

        //MSIE - If UA says Trident - This logic must stay above Chrome
        if (strpos($device->device_ua, 'Trident') !== false || strpos($device->device_ua, ' Edge/') !== false) {
            //MSIE 11 does not say MSIE and needs this
            if ($device->os->setRegex($device->device_ua, '#^Mozilla/[45]\.0 \((Windows NT [\d\.]+);.+Trident.+; rv:([\d\.]+)\.[0-9]+#', 1)) {
                $device->browser->set('IE', $device->os->getLastRegexMatch(2));
                return $device;
            }
            if ($device->os->setRegex($device->device_ua, '#^Mozilla/[45]\.0 \((Windows NT [\d\.]+).+? Edge/([\d\.]+)#', 1)) {
                $device->browser->set('Edge', $device->os->getLastRegexMatch(2));
                return $device;
            }
        }
        
        //Yandex Browser
        if (strpos($device->device_ua, 'YaBrowser') !== false
            && $device->os->setRegex($device->device_ua, '/^Mozilla\/[45]\.[0-9] \((?:Macintosh; )?([a-zA-Z0-9\._ ]+)\) AppleWebKit.+YaBrowser\/([\d\.]+)/', 1)) {
            $device->browser->set('Yandex browser', $device->os->getLastRegexMatch(2));
            return $device;
        }

        //Opera - OPR
        if (strpos($device->device_ua, 'OPR') !== false
            && $device->os->setRegex($device->device_ua, '/^Mozilla\/[0-9]\.0 .+?((?:Windows|Linux|PPC|Intel) [a-zA-Z0-9 _\.\-]+).+Chrome\/.+OPR\/([\d\.]+)/', 1)
        ) {
            $device->browser->set('Opera', $device->os->getLastRegexMatch(2));
            return $device;
        }

        //Opera - Old UA
        if (strpos($device->device_ua, 'Opera') !== false
            && $device->os->setRegex($device->device_ua, '/^Opera\/([\d\.]+) .+?((?:Windows|Linux|PPC|Intel) [a-zA-Z0-9 _\.\-]+) ?;/', 2)) {
            $device->browser->set('Opera', $device->os->getLastRegexMatch(1));
            $device->browser->setRegex($device->browser_ua, '/^Opera\/.+? Version\/([\d\.]+)/', null, 1);
            return $device;
        }
        
        if (strpos($device->device_ua, 'Chrome') !== false) {
            //Chrome Mac
            if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[0-9]\.0 \(Macintosh;(?: U;)?([a-zA-Z_ \.0-9]+)(?:;)?.+? Chrome\/([\d\.]+)\.?/', 1)) {
                $device->browser->set('Chrome', $device->os->getLastRegexMatch(2));
                return $device;
            }
            
            //Chrome
            if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[0-9]\.0 \((?:Windows;|X11;)?(?: U; )?([a-zA-Z_ \.0-9]+)(?:;)?.+? Chrome\/([\d\.]+)\.?/', 1)) {
                $device->browser->set('Chrome', $device->os->getLastRegexMatch(2));
                return $device;
            }
        }
        
        //Safari
        if (strpos($device->device_ua_normalized, 'Safari') !== false
            && $device->os->setRegex($device->device_ua_normalized, '/Mozilla\/[0-9]\.0 \((?:(?:Windows|Macintosh); (?:U; |WOW64; )?)?([a-zA-Z_ \.0-9]+)(?:;)?.+? Version\/([\d\.]+)\.?/', 1)) {
            $device->browser->set('Safari', $device->os->getLastRegexMatch(2));
            return $device;
        }
        
        if (strpos($device->device_ua, 'Firefox') !== false) {
            //Firefox - Windows
            if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[0-9]\.0 .+(Windows [0-9A-Za-z \.]+;).+?rv:.+?Firefox\/([\d\.]+)/', 1)) {
                $device->browser->set('Firefox', $device->os->getLastRegexMatch(2));
                return $device;
            }
            
            //Firefox
            if ($device->os->setRegex($device->device_ua, '/^Mozilla\/[0-9]\.0 \((?:X11|Macintosh); (?:U; |Ubuntu; |)((?:Intel|PPC|Linux) [a-zA-Z0-9\- \._\(\)]+);.+?rv:.+?Firefox\/([\d\.]+)/', 1)) {
                $device->browser->set('Firefox', $device->os->getLastRegexMatch(2));
                return $device;
            }
        }

        // Is UA CFNetwork?
        if (strpos($device->browser_ua, 'CFNetwork') !== false) {
            $device->os->set($this->device->getCapability('device_os'), $this->device->getCapability('device_os_version'));
            $device->browser->set('CFNetwork App', $this->device->getCapability('mobile_browser_version'));
            return $device;
        }
        
        return $device;
    }
}
