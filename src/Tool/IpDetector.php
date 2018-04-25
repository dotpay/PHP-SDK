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
			self::$ipaddress = '';
			 
			if (function_exists('apache_request_headers')) {
				$headers = apache_request_headers();
			} else {
				$headers = $_SERVER;
			}
			// CloudFlare support
			if (array_key_exists('HTTP_CF_CONNECTING_IP', $headers)) {
				// Validate IP address (IPv4/IPv6)
				if (filter_var($headers['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) 
				{
					self::$ipaddress = $headers['HTTP_CF_CONNECTING_IP']; 
					return self::$ipaddress;   
				}
			}
			if (array_key_exists('X-Forwarded-For', $headers)) {
				$_SERVER['HTTP_X_FORWARDED_FOR'] = $headers['X-Forwarded-For'];
			}
			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && (!isset($_SERVER['REMOTE_ADDR'])
				|| preg_match('/^127\..*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^172\.16.*/i', trim($_SERVER['REMOTE_ADDR']))
				|| preg_match('/^192\.168\.*/i', trim($_SERVER['REMOTE_ADDR'])) || preg_match('/^10\..*/i', trim($_SERVER['REMOTE_ADDR'])))) {
				if (strpos($_SERVER['HTTP_X_FORWARDED_FOR'], ',')) {
					$ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
					self::$ipaddress = $ips[0];
				} else {
					self::$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
			} else {
				self::$ipaddress = $_SERVER['REMOTE_ADDR'];
			}
			
			if(self::$ipaddress === '0:0:0:0:0:0:0:1' || self::$ipaddress === '::1') {
				self::$ipaddress = $config::LOCAL_IP;
			}		
			
			return self::$ipaddress;
		}
	
	
}
