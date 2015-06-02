<?php

require __DIR__ . '/../vendor/autoload.php';

use Silverslice\HttpTester\Request;
use Silverslice\HttpTester\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testIsSuccess()
    {
        $response = $this->getResponse();

        $this->assertTrue($response->isSuccess());
    }

    public function testHasStatus()
    {
        $response = $this->getResponse();

        $this->assertTrue($response->hasStatus(200));
    }

    public function testHasCookie()
    {
        $response = $this->getResponse();

        $this->assertTrue($response->hasCookie('laravel_session'));
    }

    public function testGetCookie()
    {
        $response = $this->getResponse();
        $expected = [
            'XSRF-TOKEN' => [
                'value' => 'eyJpdiI6IlR5WTAzb2FBY1wvbnJkTHJ3MFdhejB3PT0iLCJ2YWx1ZSI6ImdMcWxncmxHdElDUWNhZVBCU1wvZ1A0dkJzXC9UYUZ5aFpDRDFEaXpRSXBDM1VBWlNsMWZmYzh3VmEwa3d3ekE2TmF1ZEhyS1FyenNNemNRZ3pSdWE5d2c9PSIsIm1hYyI6IjZiNmY4NzMwMGJjNTExNWVlMmE2Mjg2OTc3OWQ1Yjc0NWU0ZTQ4MTIzYzU5NmRmYjExNmQ0ODk5M2I5OWMyNGQifQ==',
                'name' => 'XSRF-TOKEN',
                'expires' => 'Tue, 02-Jun-2015 02:50:49 GMT',
                'Max-Age' => '7200',
                'path' => '/',
            ],
            'laravel_session' => [
                'value' => 'eyJpdiI6IjNJWFpGdnZSUWQ3MHpyMGVEUGUxTnc9PSIsInZhbHVlIjoiYmdCK1FYVjBjWXI5S1ljR1lmbDVyd3JXK3IrR3ZaQzcrRWF3R2tycTFFYmRpekJ5Z2tzbnErMFRkTmUySWpjQnUrc3JpQWpXMWt2MmpRZFVibWcrOGc9PSIsIm1hYyI6IjA4YzNlNTU4NjVlYWNhMjlkZDMzZDg3NjZkZTI2M2VkMmNjMDBkM2IwYmZlOGVjZTJmNWY0ZDk0Yzc0YmFjMzkifQ==',
                'name' => 'laravel_session',
                'expires' => 'Tue, 02-Jun-2015 02:50:49 GMT',
                'Max-Age' => '7200',
                'path' => '/',
                'httponly' => '',
            ],
        ];
        $cookies = $response->getCookies();

        $this->assertEquals($expected, $cookies);
    }

    public function testHas301Redirect()
    {
        $response = $this->getResponse('redirect');

        $this->assertTrue($response->has301RedirectTo('http://symfony.com/'));
    }

    public function testHasTitle()
    {
        $response = $this->getResponse();

        $this->assertTrue($response->getHtmlTester()->hasTitle('Laravel - The PHP Framework For Web Artisans'));
    }

    public function testHasMetaDescription()
    {
        $response = $this->getResponse();

        $this->assertTrue($response->getHtmlTester()->hasMetaDescription('Laravel - The PHP framework for web artisans.'));
    }

    public function testHasMetaKeywords()
    {
        $response = $this->getResponse();

        $this->assertTrue($response->getHtmlTester()->hasMetaKeywords('laravel, php, framework, web, artisans, taylor otwell'));
    }

    public function testHasH1()
    {
        $response = $this->getResponse();

        $this->assertTrue($response->getHtmlTester()->hasH1('Love beautiful code? We do too.'));
    }

    public function testHasHtml()
    {
        $response = $this->getResponse();

        $this->assertTrue($response->getHtmlTester()->hasHtml('<h2>Expressive, Beautiful syntax.</h2>'));
    }

    protected function getResponse($type = 'base')
    {
        $rawResponse = file_get_contents(__DIR__ . '/data/'. $type .'/response');
        $curlInfo = include __DIR__ . '/data/'. $type .'/curl_info.php';

        return new Response($rawResponse, $curlInfo);
    }

    protected function saveResponse($url, $type)
    {
        $response = (new Request())
            ->get($url)
            ->send();

        $dir = __DIR__ . '/data/'. $type;
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        file_put_contents($dir .'/response', $response->getRaw());
        file_put_contents($dir .'/curl_info.php', '<?php return ' . var_export($response->getInfo(), true) . ';');
    }
}