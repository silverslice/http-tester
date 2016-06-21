<?php

namespace Silverslice\HttpTester;

/**
 * Curl wrapper sending HTTP request
 */
class Request
{
    protected $curl;

    /** @var array */
    protected $cookies;

    /** @var array */
    protected $headers;

    protected $baseUrl = '';

    /** @var array curl_info result */
    protected $info;

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
     * @return Request
     */
    public function setBaseUrl($url)
    {
        $this->baseUrl = $url;

        return $this;
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
     * Sets http header
     *
     * @param  string $value
     * @return Request
     */
    public function setHeader($value)
    {
        $this->headers[] = $value;

        return $this;
    }

    /**
     * Sets X-Requested-With header
     *
     * @return Request
     */
    public function setAjaxHeader()
    {
        $this->setHeader('X-Requested-With: XMLHttpRequest');

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
        if ($this->headers) {
            $this->setOpt(CURLOPT_HTTPHEADER, $this->headers);
        }

        $this->setOpt(CURLINFO_HEADER_OUT, true);

        $res = curl_exec($this->curl);
        if ($res === false) {
            throw new \Exception('Curl error: ' . curl_error($this->curl));
        }

        $info = curl_getinfo($this->curl);
        $this->info = $info;

        return new Response($res, $info);
    }

    /**
     * Returns sent request header
     *
     * @return mixed|null
     */
    public function getSentHeader()
    {
        return isset($this->info['request_header']) ? $this->info['request_header'] : null;
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