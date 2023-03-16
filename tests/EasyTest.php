<?php

require __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
use Silverslice\HttpTester\Request;

class EasyTest extends TestCase
{
    public function testSimple()
    {
        $request = new Request();

        $response = $request
            ->get('http://localhost:8000/')
            ->setReferrer('http://php.net/')
            ->setUserAgent('Curl agent')
            ->setCookie('hello', 'world')
            ->send();

        // the response has status code "200"
        $this->assertTrue($response->isSuccess());

        // the response body has title "Composer"
        $this->assertTrue($response->hasTitle('Composer'));

        // the response body has this part
        $this->assertTrue($response->hasHtml('Dependency Manager for PHP'));

        // the response hasn't this cookie
        $this->assertFalse($response->hasCookie('laravel_session'));
    }

    public function testSetAjaxHeader() 
    {
        $request = new Request();

        $request
            ->get('http://localhost:8000/')
            ->setAjaxHeader()
            ->send();
        
        $header = $request->getSentHeader();
        $this->assertStringContainsString('X-Requested-With: XMLHttpRequest', $header, '', true);
    }

    public function testSendJson()
    {
        $request = new Request();

        $response = $request
            ->post('http://localhost:8000/json', ['name' => 'John'])
            ->asJson()
            ->send();

        $json = $response->getBodyJson();
        $this->assertEquals('John', $json['name']);
    }
}