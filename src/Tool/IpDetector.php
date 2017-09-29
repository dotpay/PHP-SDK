<?php

namespace Dotpay\Tool;

use Dotpay\Model\Configuration;

abstract class IpDetector
{
    /**
     * @var string/null IP address of client
     */
    static private $ipAddress = null;
    
    /**
     * Return ip address from is the confirmation request.
     * 
     * @param Configuration $config Configuration of plugin using SDK
     *
     * @return string
     */
    public static function detect(Configuration $config)
    {
        if(self::$ipAddress === null) {
            if (getenv('HTTP_CLIENT_IP')) {
                self::$ipaddress = getenv('HTTP_CLIENT_IP');
            } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
                self::$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_X_FORWARDED')) {
                self::$ipaddress = getenv('HTTP_X_FORWARDED');
            } elseif (getenv('HTTP_FORWARDED_FOR')) {
                self::$ipaddress = getenv('HTTP_FORWARDED_FOR');
            } elseif (getenv('HTTP_FORWARDED')) {
                self::$ipaddress = getenv('HTTP_FORWARDED');
            } elseif (getenv('REMOTE_ADDR')) {
                self::$ipaddress = getenv('REMOTE_ADDR');
            } else {
                self::$ipaddress = 'UNKNOWN';
            }
            if (self::$ipaddress === '0:0:0:0:0:0:0:1' || self::$ipaddress === '::1') {
                self::$ipaddress = $config::LOCAL_IP;
            }
        }

        return self::$ipaddress;
    }
}
