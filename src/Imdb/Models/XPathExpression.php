<?php

namespace Imdb\Models;

class XPathExpression
{
    /**
     * @var \DOMXPath
     */
    private $processor;

    /**
     * @var \DOMNode
     */
    private $root;

    /**
     * @var string
     */
    private $xPath;

    public function __constructor(\DOMXPath $xPath)
    {
        $this->processor = $xPath;
    }

    protected function fromHere(\DOMNode $rootNode) : XPathExpression
    {
        $this->root = $rootNode;
        $this->xPath = '.';
    }

    public function divWithClassName(string $name) : XPathExpression
    {
        return $this->getChild('div', 'class', $name);
    }

    public function getChild(string $tagType, string $attr, string $attrValue) : XPathExpression
    {
        $this->xPath += '//' + $tagType + '[@' + $attr + '="' + $attrValue + '"]';
        return $this;
    }

    public function getAll() : DOMNodeList
    {
        return $this->processor->query($this->xPath, $this->root);
    }

    public function getThis() : DOMNode
    {
        return $this->processor->query($this->xPath, $this->root)->item(0);
    }
}
