<?php

namespace Silverslice\HttpTester;

/**
 * Curl wrapper sending HTTP request
 */
class Request
{
    protected $curl;

    protected $cookies;

    protected $baseUrl = '';

    public function __construct()
    {
        $this->curl = curl_init();
        $this->setOpt(CURLINFO_HEADER_OUT, true);
        $this->setOpt(CURLOPT_HEADER, true);
        $this->setOpt(CURLOPT_RETURNTRANSFER, true);

        return $this;
    }

    /**
     * Sets base part of url for every type of request
     *
     * @param $url
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;
    }

    /**
     * Sets GET request
     *
     * @param  string  $url
     * @return Request
     */
    public function get($url)
    {
        $this->setOpt(CURLOPT_URL, $this->buildUrl($url));

        return $this;
    }

    /**
     * Sets POST request
     *
     * @param  string  $url
     * @param  array   $data
     * @return Request
     */
    public function post($url, $data = [])
    {
        $this->setOpt(CURLOPT_URL, $this->buildUrl($url));
        $this->setOpt(CURLOPT_POST, true);
        $this->setOpt(CURLOPT_POSTFIELDS, http_build_query($data));

        return $this;
    }

    /**
     * Sets HEAD request
     *
     * @param  string $url
     * @return Request
     */
    public function head($url)
    {
        $this->setOpt(CURLOPT_URL, $this->buildUrl($url));
        $this->setOpt(CURLOPT_NOBODY, true);

        return $this;
    }

    /**
     * Sets user agent
     *
     * @param  string $userAgent
     * @return Request
     */
    public function setUserAgent($userAgent)
    {
        $this->setOpt(CURLOPT_USERAGENT, $userAgent);

        return $this;
    }

    /**
     * Sets referrer
     *
     * @param  string $referrer
     * @return Request
     */
    public function setReferrer($referrer)
    {
        $this->setOpt(CURLOPT_REFERER, $referrer);

        return $this;
    }

    /**
     * Sets cookie
     *
     * @param  string $key
     * @param  string $value
     * @return Request
     */
    public function setCookie($key, $value)
    {
        $this->cookies[$key] = $value;
        $this->setOpt(CURLOPT_COOKIE, http_build_query($this->cookies, '', '; '));

        return $this;
    }

    /**
     * Sets cookies from array. Each array element should contains 'name' and 'value' keys
     *
     * @param array $cookies
     * @return Request
     */
    public function setCookies(array $cookies)
    {
        foreach ($cookies as $c) {
            $this->setCookie($c['name'], $c['value']);
        }

        return $this;
    }

    /**
     * Sends request
     *
     * @return Response
     * @throws \Exception
     */
    public function send()
    {
        $res = curl_exec($this->curl);
        if ($res === false) {
            throw new \Exception('Curl error: ' . curl_error($this->curl));
        }

        $info = curl_getinfo($this->curl);

        return new Response($res, $info);
    }

    /**
     * Sets curl option
     *
     * @param string $opt
     * @param mixed  $value
     */
    protected function setOpt($opt, $value)
    {
        curl_setopt($this->curl, $opt, $value);
    }

    /**
     * Returns url with base url if url scheme is not specified
     *
     * @param  $url
     * @return string
     */
    protected function buildUrl($url)
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (empty($scheme)) {
            $url = rtrim($this->baseUrl, '/') . '/' . ltrim($url, '/');
        }

        return $url;
    }
}