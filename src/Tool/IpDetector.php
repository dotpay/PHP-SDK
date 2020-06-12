<?php

namespace Dotpay\Tool;

use Dotpay\Model\Configuration;

abstract class IpDetector
{
    /**
     * @var string/null IP address of client
     */
    static private $ipaddress = null;
    
    /**
     * Return ip address from is the confirmation request.
     * 
     * @param Configuration $config Configuration of plugin using SDK
     *
     * @return string
     */
 
    public static function detect(Configuration $config)
		{	

			self::$ipaddress = '';
			// CloudFlare support
			if (array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER)) 
			{
				// Validate IP address (IPv4/IPv6)
				if (filter_var($_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
					self::$ipaddress = $_SERVER['HTTP_CF_CONNECTING_IP'];
					return self::$ipaddress;
				}
			}
			if (array_key_exists('X-Forwarded-For', $_SERVER)) 
			{
				$_SERVER['HTTP_X_FORWARDED_FOR'] = $_SERVER['X-Forwarded-For'];
			}
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR']) 
			{
				if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
					$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
					self::$ipaddress = $ips[0];
				} else {
					self::$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
			} else 
			{
				self::$ipaddress = $_SERVER['REMOTE_ADDR'];
			}
			
			if(self::$ipaddress === '0:0:0:0:0:0:0:1' || self::$ipaddress === '::1') 
			{
				self::$ipaddress = $config::LOCAL_IP;
			}
			
			return self::$ipaddress;

		}
	
	
}