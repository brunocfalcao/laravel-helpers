<?php

namespace Brunocfalcao\LaravelHelpers\Utils;

use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class Crawlie
{
    protected $url;

    protected $xpath;

    /**
     * Constructor. Sets the URL to be crawled.
     *
     * @param  string  $url The URL to be crawled.
     *
     * @throws \InvalidArgumentException If the URL is empty.
     */
    public function __construct($url)
    {
        if (empty($url)) {
            throw new \InvalidArgumentException('The URL parameter cannot be empty.');
        }

        $this->url = $url;
    }

    /**
     * Sets the XPath expression to select elements from the crawled page.
     *
     * @param  string  $xpath The XPath expression.
     * @return CrawlData Returns the current object for method chaining.
     *
     * @throws \InvalidArgumentException If the XPath is empty.
     */
    public function xPath($xpath)
    {
        if (empty($xpath)) {
            throw new \InvalidArgumentException('The XPath parameter cannot be empty.');
        }

        $this->xpath = $xpath;

        return $this;
    }

    /**
     * Starts the crawling process and returns the extracted value from the element.
     *
     * @return string The extracted value from the element.
     *
     * @throws \InvalidArgumentException If the XPath has not been set.
     */
    public function get()
    {
        if (! $this->xpath) {
            throw new \InvalidArgumentException('The XPath parameter has not been set.');
        }

        return $this->crawl();
    }

    /**
     * Fetches the URL and returns the HTML content inside the element matched by the XPath expression.
     *
     * @return string The HTML content inside the element matched by the XPath expression.
     *
     * @throws \Exception If the URL fetch fails or if the XPath does not return any elements.
     */
    public function getHTML()
    {
        if (! $this->xpath) {
            throw new \InvalidArgumentException('The XPath parameter has not been set.');
        }

        $response = Http::get($this->url);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch the URL: '.$this->url);
        }

        $crawler = new Crawler($response->body());

        $element = $crawler->filterXPath($this->xpath);

        if ($element->count() > 0) {
            return $element->html();
        } else {
            throw new \Exception('The specified XPath did not return any elements.');
        }
    }

    /**
     * Fetches the URL and returns the extracted value from the element.
     *
     * @return string The extracted value from the element.
     *
     * @throws \Exception If the URL fetch fails or if the XPath does not return any elements.
     */
    protected function crawl()
    {
        $response = Http::get($this->url);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch the URL: '.$this->url);
        }

        $crawler = new Crawler($response->body());

        $element = $crawler->filterXPath($this->xpath);

        if ($element->count() > 0) {
            return $this->extractValueFromElement($element);
        } else {
            throw new \Exception('The specified XPath did not return any elements.');
        }
    }

    /**
     * Extracts the value or text from the specified element.
     *
     * @param  Crawler  $element The element to extract the value or text from.
     * @return string The extracted value or text from the element.
     */
    protected function extractValueFromElement($element)
    {
        $inputFormElements = ['input', 'select', 'textarea', 'radio', 'checkbox'];
        $value = '';
        $nodeName = $element->nodeName();

        if (in_array($nodeName, $inputFormElements)) {
            switch ($nodeName) {
                case 'input':
                    $value = $element->attr('value');
                    break;
                case 'select':
                    $value = $element->filter('option:selected')->attr('value');
                    break;
                case 'textarea':
                    $value = $element->text();
                    break;
                case 'radio':
                case 'checkbox':
                    $value = $element->attr('checked') ? $element->attr('value') : '';
                    break;
            }
        } else {
            $value = $element->text();
        }

        // Trim whitespace and remove non-breaking spaces
        $value = str_replace("\n", '', $value);
        $value = str_replace('&nbsp;', ' ', $value);
        $value = str_replace("\u{A0}", ' ', $value); // Replace non-breaking space with a regular space
        $value = trim(preg_replace('/\s+/', ' ', $value));

        return $value;
    }
}
