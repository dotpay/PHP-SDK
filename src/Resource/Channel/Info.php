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

namespace Dotpay\Resource\Channel;

use Dotpay\Exception\Resource\Channel\NotFoundException;

/**
 * Represent a structure of information about payment channels which is downloaded from Dotpay.
 */
class Info
{
    /**
     * @var array Data of channels
     */
    private $channels = [];

    /**
     * @var array Data of form's fields
     */
    private $forms = [];

    /**
     * Initialize informations about payment channels.
     *
     * @param array $channels Data of channels
     * @param array $forms    Data of form's fields
     */
    public function __construct(array $channels, array $forms)
    {
        $this->channels = $channels;
        $this->forms = $forms;
    }

    /**
     * Return saved original data of channels.
     *
     * @return array
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Return saved original data of forms.
     *
     * @return array
     */
    public function getForms()
    {
        return $this->forms;
    }

    /**
     * Return a structure of payment channel informations for the channel which has the given id.
     *
     * @param int $channelId Channel id
     *
     * @return OneChannel
     *
     * @throws NotFoundException Thrown when doesn't exist a payment channel with the given id
     */
    public function getChannelInfo($channelId)
    {
        foreach ($this->getChannels() as $channel) {
            if (isset($channel['id']) && $channel['id'] == $channelId) {
                return new OneChannel($channel);
            }
        }
        throw new NotFoundException($channelId);
    }

    /**
     * Return an array of Agreements object with agreements which are dedicated for the payment channel which has the given id.
     *
     * @param int $channelId Channel id
     *
     * @return array
     */
    public function getAgreements($channelId)
    {
        $channelInfo = $this->getChannelInfo($channelId);
        $fields = $channelInfo->getFormNames();
        $agreements = [];
        if (array_search('agreement', $fields) !== false) {
            $agreements = $this->createAgreementsList();
        }

        return $agreements;
    }

    /**
     * Return all agreements independly of the channel.
     *
     * @return
     */
    public function getUniversalAgreements()
    {
        return $this->createAgreementsList();
    }

    /**
     * Return array oof Agreement class contains agreement data.
     *
     * @return array
     */
    private function createAgreementsList()
    {
        $agreements = [];
        foreach ($this->getForms() as $form) {
            if (isset($form['form_name']) && $form['form_name'] == 'agreement' && isset($form['fields'])) {
                foreach ($form['fields'] as $field) {
                    if ($field['required']) {
                        $agreements[] = new Agreement($field);
                    }
                }
            }
        }

        return $agreements;
    }
}
