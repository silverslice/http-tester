# HttpTester - lightweight http testing tool

HttpTester is a thin wrapper around curl to test http requests. If your need more complex tool, please use
[Goutte](https://github.com/FriendsOfPHP/Goutte) or one of its components.


## Installation

`php composer.phar require silverslice/http-tester:dev-master`

## Example of usage
```php
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
        $this->assertTrue($response->hasTitle('Composer'));

        // the response body has this part
        $this->assertTrue($response->hasHtml('<a href="http://packagist.org/">Browse Packages</a>'));

        // the response hasn't this cookie
        $this->assertFalse($response->hasCookie('laravel_session'));
    }
}
```