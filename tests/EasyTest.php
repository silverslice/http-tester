<?php

require __DIR__ . '/../vendor/autoload.php';

use Silverslice\HttpTester\Request;

class EasyTest extends \PHPUnit_Framework_TestCase
{
    public function testSimple()
    {
        $request = new Request();
        $response = $request->get('https://getcomposer.org/')->send();

        $this->assertContains('Dependency Manager for PHP', $response->getBody());
    }
}