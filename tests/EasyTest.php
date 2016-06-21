<?php

require __DIR__ . '/../vendor/autoload.php';

use Silverslice\HttpTester\Request;

class EasyTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $request = new Request();

        $response = $request
            ->get('https://getcomposer.org/')
            ->setReferrer('http://php.net/')
            ->setUserAgent('Curl agent')
            ->setCookie('hello', 'world')
            ->send();

        // the response has status code "200"
        $this->assertTrue($response->isSuccess());

        // the response body has title "Composer"
        $this->assertTrue($response->getHtmlTester()->hasTitle('Composer'));

        // the response body has this part
        $this->assertTrue($response->getHtmlTester()->hasHtml('<a href="http://packagist.org/">Browse Packages</a>'));

        // the response hasn't this cookie
        $this->assertFalse($response->hasCookie('laravel_session'));
    }

    public function testSetAjaxHeader() 
    {
        $request = new Request();

        $request
            ->get('https://getcomposer.org/')
            ->setAjaxHeader()
            ->send();
        
        $header = $request->getSentHeader();
        $this->assertContains('X-Requested-With: XMLHttpRequest', $header);
    }
}