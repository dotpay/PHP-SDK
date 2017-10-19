<?php
/**
 * Copyright (c) 2017 Dotpay S.A. <tech@dotpay.pl>.
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
 * @copyright Dotpay S.A
 * @license   https://opensource.org/licenses/MIT  The MIT License
 */

namespace Dotpay\Html\Container;

use Dotpay\Html\Element;
use Dotpay\Html\Node;
use Dotpay\Html\PlainText;

/**
 * Represent an abstract container which can contain other HTML elements.
 */
abstract class Container extends Element
{
    /**
     * @var array Elements which are children of the container
     */
    private $children = [];

    /**
     * Initialize the container.
     *
     * @param string $type     Type of the container
     * @param array  $children Children contained in the container
     */
    public function __construct($type, $children = [])
    {
        parent::__construct($type);
        if ($children instanceof Node) {
            $children = [$children];
        } elseif ($children === null ||
                is_scalar($children) ||
                is_callable([$children, '__toString'])) {
            $children = [new PlainText($children)];
        }
        $this->setChildren($children);
    }

    /**
     * Return children contained in the container.
     *
     * @return array
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Add a new child to the container.
     *
     * @param Node $child A child of the container
     */
    public function addChild(Node $child)
    {
        $this->children[] = $child;
    }

    /**
     * Set a set of children for the container.
     *
     * @param array $children Children of the container
     */
    public function setChildren(array $children)
    {
        $this->children = [];
        foreach ($children as $child) {
            if ($child instanceof Node) {
                $this->addChild($child);
            }
        }
    }

    /**
     * Remove the given element from a set of children of the container.
     *
     * @param Node $child A HTML element which is a child of the container
     *
     * @return Container
     */
    public function removeChild(Node $child)
    {
        foreach ($this->getChildren() as $index => $oneChild) {
            if ($oneChild === $child) {
                array_splice($this->children, $index, 1);
                break;
            }
        }

        return $this;
    }

    /**
     * Return a HTML string of the container.
     *
     * @return string
     */
    public function __toString()
    {
        $text = '<'.$this->getType().$this->getAttributeList().'>';
        foreach ($this->getChildren() as $child) {
            $text .= (string) $child;
        }
        $text .= '</'.$this->getType().'>';

        return $text;
    }
}
