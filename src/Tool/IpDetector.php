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
                self::$ipAddress = getenv('HTTP_CLIENT_IP');
            } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
                self::$ipAddress = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_X_FORWARDED')) {
                self::$ipAddress = getenv('HTTP_X_FORWARDED');
            } elseif (getenv('HTTP_FORWARDED_FOR')) {
                self::$ipAddress = getenv('HTTP_FORWARDED_FOR');
            } elseif (getenv('HTTP_FORWARDED')) {
                self::$ipAddress = getenv('HTTP_FORWARDED');
            } elseif (getenv('REMOTE_ADDR')) {
                self::$ipAddress = getenv('REMOTE_ADDR');
            } else {
                self::$ipAddress = 'UNKNOWN';
            }
            if (self::$ipAddress === '0:0:0:0:0:0:0:1' || self::$ipAddress === '::1') {
                self::$ipAddress = $config::LOCAL_IP;
            }
        }

        return self::$ipAddress;
    }
}
