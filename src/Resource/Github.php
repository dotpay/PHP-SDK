<?php
/**
 * Copyright (c) 2018 Dotpay sp. z o.o. <tech@dotpay.pl>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author    Dotpay Team <tech@dotpay.pl>
 * @copyright Dotpay sp. z o.o.
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Resource;

use Dotpay\Resource\Github\Version;
use Dotpay\Exception\Resource\Github\VersionNotFoundException;
use Dotpay\Exception\Resource\Github\ResourceMalformedException;
use Dotpay\Exception\Resource\Github\AssetNotFoundException;
use Dotpay\Exception\BadReturn\TypeNotCompatibleException;
use Dotpay\Exception\Resource\NotFoundException;
use DateTime;

/**
 * Allow to use Github Api for checking a version of this SDK and other projects.
 */
class Github extends Resource
{
    /**
     * Basic url of the Github API.
     */
    const githubUrl = 'https://api.github.com/';

    /**
     * Return the Version object with informations about the newest version of this SDK.
     *
     * @return Version
     */
    public function getSdkVersion()
    {
        return $this->getLatestProjectVersion('dotpay', 'PHP-SDK');
    }

    /**
     * Check if the current version is the same or newer than the version on Github.
     *
     * @return bool
     */
    public function isSdkNewest()
    {
        $config = $this->config;
        $githubVersion = $this->getSdkVersion()->getNumber();
        $installedVersion = $config::SDK_VERSION;

        return version_compare($githubVersion, $installedVersion, '<=');
    }

    /**
     * Return details of version of the project which is developed on the given Github user account.
     *
     * @param string $username Username of Github user
     * @param string $project  Name of a project
     *
     * @return Version
     *
     * @throws VersionNotFoundException   Thrown when any latest version of the project is not found
     * @throws ResourceMalformedException Thrown when the received data from Github is malformed
     * @throws TypeNotCompatibleException Thrown when a response from Github server is in incompatible type
     */
    public function getLatestProjectVersion($username, $project)
    {
        $asset = NULL;
        try {
            $content = $this->getContent(self::githubUrl.'repos/'.$username.'/'.$project.'/releases/latest');
            if(!is_array($content)) {
                throw new ResourceMalformedException();
            } else if(!isset($content['assets'][0])) {
                //throw new AssetNotFoundException($username.'/'.$project.' ('.$content['tag_name'].')');
                $asset = $content['html_url'];
            } else {
                $asset = $content['assets'][0]['browser_download_url'];
            }
        } catch (NotFoundException $ex) {
            throw new VersionNotFoundException($username.'/'.$project);
        }
        if (!is_array($content)) {
            throw new TypeNotCompatibleException(gettype($content));
        }
        $version = new Version($content['tag_name'], $asset);
        $version->setApiUrl($content['url'])
                ->setUrl($content['html_url'])
                ->setCreated(new DateTime($content['created_at']))
                ->setPublished(new DateTime($content['published_at']));

        return $version;
    }

    /**
     * Return a string which contain a header with Accept rule.
     *
     * @return string
     */
    protected function getAcceptHeader()
    {
        return 'Accept: application/vnd.github.v3+json';
    }
}
