<?php

namespace Silverslice\HttpTester;

/**
 * Checks html
 */
class HtmlTester
{
    protected $html;
    protected $charset;

    /** @var  \DOMDocument */
    protected $domDocument;

    /**
     * @param string $html    Html code
     * @param string $charset
     */
    public function __construct($html, $charset = 'UTF-8')
    {
        $this->html = $html;
        $this->charset  = $charset;
    }

    /**
     * Checks if html has title with provided text
     *
     * @param string $text
     * @return bool
     */
    public function hasTitle($text)
    {
        $value = $this->findXpathValue('/html/head/title');

        return $value == $text;
    }

    /**
     * Checks if html has meta description tag with provided content
     *
     * @param string $text
     * @return bool
     */
    public function hasMetaDescription($text)
    {
        $value = $this->findXpathValue('/html/head/meta[@name="description"]/@content');

        return $value == $text;
    }

    /**
     * Checks if html has meta keywords tag with provided content
     *
     * @param string $text
     * @return bool
     */
    public function hasMetaKeywords($text)
    {
        $value = $this->findXpathValue('/html/head/meta[@name="keywords"]/@content');

        return $value == $text;
    }

    /**
     * Checks if html has h1 tag with provided text
     *
     * @param string $html
     * @return bool
     */
    public function hasH1($html)
    {
        $value = $this->findXpathValue('//h1');

        return $value == $html;
    }

    /**
     * Checks if html contains provided html
     *
     * @param $html
     * @return bool
     */
    public function hasHtml($html)
    {
        return stripos($this->html, $html) !== false;
    }

    /**
     * Performs xpath query
     *
     * @param string $query
     * @return \DOMNodeList
     */
    protected function xpath($query)
    {
        $xpath = new \DOMXPath($this->getDOMDocument());

        return $xpath->query($query);
    }

    /**
     * Finds node value by xpath query
     *
     * @param string $query
     * @return bool|string
     */
    protected function findXpathValue($query)
    {
        $value = false;
        $nodes = $this->xpath($query);
        if ($nodes !== false && $nodes->length) {
            $value = $nodes->item(0)->nodeValue;
        }

        return $value;
    }


    protected function getDOMDocument()
    {
        if (!isset($this->domDocument)) {
            $doc = new \DOMDocument('1.0', $this->charset);
            @$doc->loadHTML($this->html);

            $this->domDocument = $doc;
        }


        return $this->domDocument;
    }
}