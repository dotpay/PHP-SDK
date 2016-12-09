<?php

namespace Dotpay\Resource;

use Dotpay\Model\Configuration;
use Dotpay\Tool\Curl;
use Dotpay\Exception\Resource\ServerException;
use Dotpay\Exception\Resource\ForbiddenException;
use Dotpay\Exception\Resource\UnauthorizedException;
use Dotpay\Exception\Resource\NotFoundException;

abstract class Resource {
    protected $config;
    protected $curl;
    
    public function __construct(Configuration $config, Curl $curl) {
        $this->config = $config;
        $this->curl = $curl;
        $this->curl->addOption(CURLOPT_SSL_VERIFYPEER, false)
                   ->addOption(CURLOPT_HEADER, false)
                   ->addOption(CURLOPT_RETURNTRANSFER, true);
    }
    
    public function getCurl() {
        return $this->curl;
    }
    
    protected function getContent($url) {
        $this->curl->addOption(CURLOPT_URL, $url);
        $headers = [
            'Accept' => 'application/xml',
            'Content-Type' => 'application/xml'
        ];
        $this->curl->addOption(CURLOPT_HTTPHEADER, $headers);
        $result = $this->curl->exec();
        $info = $this->curl->getInfo();
        $httpCode = (int)$info['http_code'];
        if($httpCode >= 200 && $httpCode < 300 || $httpCode == 400)
            return json_decode($result, true);
        switch($httpCode) {
            case 401:
                throw new UnauthorizedException($url);
                break;
            case 403:
                throw new ForbiddenException($url);
                break;
            case 404:
                throw new NotFoundException($url);
                break;
            default:
                throw new ServerException($this->curl->error(), $httpCode);
        }
    }
}

?>