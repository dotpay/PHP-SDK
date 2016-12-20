<?php

namespace Dotpay\Resource;

use Dotpay\Model\Configuration;
use Dotpay\Resource\Github\Version;
use Dotpay\Exception\Resource\Github\VersionNotFoundException;
use Dotpay\Exception\BadReturn\TypeNotCompatibleException;
use Dotpay\Exception\Resource\NotFoundException;
use \DateTime;

class Github extends Resource {
    const githubUrl = 'https://api.github.com/';
    
    public function getSdkVersion() {
        try {
            $content = $this->getGatewayVersion('dotpay', 'phpSDK');
            $version = new Version($content['tag_name'], $content['assets'][0]['browser_download_url']);
            $version->setUrl($content['url'])
                    ->setCreated(new DateTime($content['created_at']))
                    ->setPublished(new DateTime($content['published_at']));
            return $version;
        } catch (NotFoundException $ex) {
            throw new VersionNotFoundException('phpSDK');
        }
    }
    
    public function isSdkNewest() {
        $config = $this->config;
        $githubVersion = $this->getSdkVersion()->getNumber();
        $installedVersion = $config::sdkVersion;
        return version_compare($githubVersion, $installedVersion, '<=');
    }
    
    public function getGatewayVersion($username, $project) {
        $content = $this->getContent(self::githubUrl.'/repos/'.$username.'/'.$project.'/releases/latest');
        if(!is_array($content))
            throw new TypeNotCompatibleException(gettype($content));
        return $content;
    }
}
