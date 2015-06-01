<?php

namespace SilverSlice\HttpTester;

/**
 * Class Response
 *
 * Represents HTTP response
 */
class Response
{
    protected $response;

    protected $body;

    protected $headers;

    protected $info;

    public function __construct($response, $info)
    {
        $this->response = $response;
        $this->info = $info;

        $res = $this->parse($response);
        $this->headers = $res['headers'];
        $this->body = $res['body'];
    }

    /**
     * Returns list of cookies
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->headers['cookie'];
    }

    /**
     * Does the response have status code 200?
     *
     * @return bool
     */
    public function isSuccess()
    {
        return $this->info['http_code'] == 200;
    }

    /**
     * Does the response have status code?
     *
     * @param  int $status
     * @return bool
     */
    public function hasStatus($status)
    {
        return $this->info['http_code'] == $status;
    }

    /**
     * Does the response contain cookie?
     *
     * @param string $name Cookie name
     * @return bool
     */
    public function hasCookie($name)
    {
        return isset($this->headers['cookie'][$name]);
    }

    /**
     * Returns the body of the response
     *
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Does the response contain redirect to url?
     *
     * @param  string $url
     * @return bool
     */
    public function hasRedirectTo($url)
    {
        return isset($this->headers['Location']) && ($this->headers['Location'] == $url);
    }

    /**
     * Does the response have status code 200 and contain redirect to url?
     *
     * @param $url
     * @return bool
     */
    public function has301RedirectTo($url)
    {
        return $this->hasStatus(301) && $this->hasRedirectTo($url);
    }


    protected function parse($response)
    {
        list($headers, $body) = explode("\r\n\r\n", $response, 2);
        $headers = $this->parseHeaders($headers);

        return compact('headers', 'body');
    }

    protected function parseHeaders($raw_headers)
    {
        $headers = array();
        $key = '';

        foreach (explode("\n", $raw_headers) as $h) {
            $h = explode(':', $h, 2);

            if (isset($h[1])) {
                if (!isset($headers[$h[0]])) {
                    $headers[$h[0]] = trim($h[1]);
                } elseif (is_array($headers[$h[0]])) {
                    $headers[$h[0]] = array_merge($headers[$h[0]], array(trim($h[1])));
                } else {
                    $headers[$h[0]] = array_merge(array($headers[$h[0]]), array(trim($h[1])));
                }

                $key = $h[0];
            } else {
                if (substr($h[0], 0, 1) == "\t") {
                    $headers[$key] .= "\r\n\t" . trim($h[0]);
                } elseif (!$key) {
                    $headers[0] = trim($h[0]);
                    trim($h[0]);
                }

            }
        }

        $headers['cookie'] = array();
        if (isset($headers['Set-Cookie'])) {
            $headers['Set-Cookie'] = (array) $headers['Set-Cookie'];
            $headers['cookie'] = $this->parseCookie($headers['Set-Cookie']);
        }

        return $headers;
    }

    /**
     * @link  http://tools.ietf.org/html/rfc6265#section-5.2
     *
     * @param $cookie
     * @return array
     */
    protected function parseCookie($cookie)
    {
        // CUSTOMER=WILE_E_COYOTE; path=/; expires=Wednesday, 09-Nov-99 23:12:40 GMT
        $res = array();
        foreach ($cookie as $c) {
            $params = explode(';', $c);
            $name = '';
            foreach ($params as $i => $param) {
                $val = explode('=', trim($param));
                if ($i === 0) {
                    $name = $val[0];
                    $res[$name] = array('value' => urldecode($val[1]), 'name' => $name);
                } else {
                    $res[$name][$val[0]] = $val[1];
                }
            }

        }

        return $res;
    }
}