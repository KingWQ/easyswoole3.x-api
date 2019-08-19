<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/12/25
 * Time: 1:28 PM
 */

namespace EasySwoole\HttpClient\Bean;

use EasySwoole\Spl\SplBean;
use Swoole\Coroutine\Http\Client;

/**
 * 标准响应体
 * Class Response
 * @package EasySwoole\HttpClient\Bean
 */
class Response extends SplBean
{
    protected $headers;
    protected $body;
    protected $errCode;
    protected $errMsg;
    protected $statusCode;
    protected $set_cookie_headers;
    protected $cookies;
    protected $connected;
    protected $host;
    protected $port;
    protected $ssl;
    protected $setting;
    protected $requestMethod;
    protected $requestHeaders;
    protected $requestBody;
    protected $uploadFiles;
    protected $downloadFile;
    protected $downloadOffset;

    protected $client;

    /**
     * Headers Getter
     * @return mixed
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Body Getter
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * ErrCode Getter
     * @return mixed
     */
    public function getErrCode()
    {
        return $this->errCode;
    }

    /**
     * ErrMsg Getter
     * @return mixed
     */
    public function getErrMsg()
    {
        return $this->errMsg;
    }

    /**
     * StatusCode Getter
     * @return mixed
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * SetCookieHeaders Getter
     * @return mixed
     */
    public function getSetCookieHeaders()
    {
        return $this->set_cookie_headers;
    }

    /**
     * Cookies Getter
     * @return mixed
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Connected Getter
     * @return mixed
     */
    public function getConnected()
    {
        return $this->connected;
    }

    /**
     * Host Getter
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Port Getter
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Ssl Getter
     * @return mixed
     */
    public function getSsl()
    {
        return $this->ssl;
    }

    /**
     * Setting Getter
     * @return mixed
     */
    public function getSetting()
    {
        return $this->setting;
    }

    /**
     * RequestMethod Getter
     * @return mixed
     */
    public function getRequestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * RequestHeaders Getter
     * @return mixed
     */
    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    /**
     * RequestBody Getter
     * @return mixed
     */
    public function getRequestBody()
    {
        return $this->requestBody;
    }

    /**
     * UploadFiles Getter
     * @return mixed
     */
    public function getUploadFiles()
    {
        return $this->uploadFiles;
    }

    /**
     * DownloadFile Getter
     * @return mixed
     */
    public function getDownloadFile()
    {
        return $this->downloadFile;
    }

    /**
     * DownloadOffset Getter
     * @return mixed
     */
    public function getDownloadOffset()
    {
        return $this->downloadOffset;
    }

    /**
     * Client Getter
     * @return mixed
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Client Setter
     * @param mixed $client
     */
    public function setClient($client): void
    {
        $this->client = $client;
    }
}