<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/12/25
 * Time: 10:43 AM
 */

namespace EasySwoole\HttpClient;


use EasySwoole\HttpClient\Bean\Response;
use EasySwoole\HttpClient\Bean\Url;
use EasySwoole\HttpClient\Exception\InvalidUrl;
use Swoole\Coroutine\Http\Client;
use Swoole\WebSocket\Frame;

class HttpClient
{
    // HTTP 1.0/1.1 标准请求方法
    const METHOD_GET = 'GET';
    const METHOD_PUT = 'PUT';
    const METHOD_POST = 'POST';
    const METHOD_HEAD = 'HEAD';
    const METHOD_TRACE = 'TRACE';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_OPTIONS = 'OPTIONS';

    // 常用POST提交请求头
    const CONTENT_TYPE_TEXT_XML = 'text/xml';
    const CONTENT_TYPE_TEXT_JSON = 'text/json';
    const CONTENT_TYPE_FORM_DATA = 'multipart/form-data';
    const CONTENT_TYPE_APPLICATION_XML = 'application/xml';
    const CONTENT_TYPE_APPLICATION_JSON = 'application/json';
    const CONTENT_TYPE_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    /**
     * 当前请求路径
     * @var Url
     */
    protected $url;

    /**
     * 协程客户端
     * @var Client
     */
    protected $httpClient;

    /**
     * 协程客户端设置项
     * @var array
     */
    protected $clientSetting = [];

    /**
     * 请求携带的Cookies
     * @var array
     */
    protected $cookies = [];

    /**
     * 强制开启SSL请求
     * @var bool
     */
    protected $enableSSL = false;

    /**
     * 默认请求头
     * @var array
     */
    protected $header = [
        "user-agent"      => 'EasySwooleHttpClient/0.1',
        'accept'          => 'text/html,application/xhtml+xml,application/xml,application/json',
        'accept-encoding' => 'gzip',
        'pragma'          => 'no-cache',
        'cache-control'   => 'no-cache'
    ];

    /**
     * HttpClient constructor.
     * @param string|null $url
     * @throws InvalidUrl
     */
    public function __construct(?string $url = null)
    {
        if (!empty($url)) {
            $this->setUrl($url);
        }
        $this->setTimeout(3);
        $this->setConnectTimeout(5);
    }

    public function setQuery(?array $data = null)
    {
        if($data){
            $old = $this->url->getQuery();
            parse_str($old,$old);
            $this->url->setQuery(http_build_query($data + $old));
        }
        return $this;
    }

    // --------  客户端配置设置方法  --------

    /**
     * 设置当前要请求的URL
     * @param string $url 需要请求的网址
     * @return HttpClient
     * @throws InvalidUrl
     */
    public function setUrl(string $url): HttpClient
    {
        $info = parse_url($url);
        if (empty($info['scheme'])) {
            $info = parse_url('//' . $url); // 防止无scheme导致的host解析异常 默认作为http处理
        }
        $this->url = new Url($info);
        if (empty($this->url->getHost())) {
            throw new InvalidUrl("HttpClient: {$url} is invalid");
        }
        return $this;
    }

    /**
     * 强制开启SSL
     * @param bool $enableSSL
     */
    public function setEnableSSL(bool $enableSSL = true)
    {
        $this->enableSSL = $enableSSL;
    }

    /**
     * 设置请求等待超时时间
     * @param float $timeout 超时时间 单位秒(可传入浮点数指定毫秒)
     * @return HttpClient
     */
    public function setTimeout(float $timeout): HttpClient
    {
        $this->clientSetting['timeout'] = $timeout;
        return $this;
    }

    /**
     * 设置连接服务端的超时时间
     * @param float $connectTimeout 超时时间 单位秒(可传入浮点数指定毫秒)
     * @return HttpClient
     */
    public function setConnectTimeout(float $connectTimeout): HttpClient
    {
        $this->clientSetting['connect_timeout'] = $connectTimeout;
        return $this;
    }

    /**
     * 启用或关闭HTTP长连接
     * @param bool $keepAlive 是否开启长连接
     * @return $this
     */
    public function setKeepAlive(bool $keepAlive = true)
    {
        $this->clientSetting['keep_alive'] = $keepAlive;
        return $this;
    }

    /**
     * 启用或关闭服务器证书验证
     * 可以同时设置是否允许自签证书(默认不允许)
     * @param bool $sslVerifyPeer 是否开启验证
     * @param bool $sslAllowSelfSigned 是否允许自签证书
     * @return HttpClient
     */
    public function setSslVerifyPeer(bool $sslVerifyPeer = true, $sslAllowSelfSigned = false)
    {
        $this->clientSetting['ssl_verify_peer'] = $sslVerifyPeer;
        $this->clientSetting['ssl_allow_self_signed'] = $sslAllowSelfSigned;
        return $this;
    }

    /**
     * 设置服务器主机名称
     * 与ssl_verify_peer配置或Client::verifyPeerCert配合使用
     * @param string $sslHostName 服务器主机名称
     * @return HttpClient
     */
    public function setSslHostName(string $sslHostName)
    {
        $this->clientSetting['ssl_host_name'] = $sslHostName;
        return $this;
    }

    /**
     * 设置验证用的SSL证书
     * @param string $sslCafile 证书文件路径
     * @return $this
     */
    public function setSslCafile(string $sslCafile)
    {
        $this->clientSetting['ssl_cafile'] = $sslCafile;
        return $this;
    }

    /**
     * 设置SSL证书目录(验证用)
     * @param string $sslCapath 证书目录
     * @return $this
     */
    public function setSslCapath(string $sslCapath)
    {
        $this->clientSetting['ssl_capath'] = $sslCapath;
        return $this;
    }

    /**
     * 设置请求使用的证书文件
     * @param string $sslCertFile 证书文件路径
     * @return $this
     */
    public function setSslCertFile(string $sslCertFile)
    {
        $this->clientSetting['ssl_cert_file'] = $sslCertFile;
        return $this;
    }

    /**
     * 设置请求使用的证书秘钥文件
     * @param string $sslKeyFile 秘钥文件路径
     * @return $this
     */
    public function setSslKeyFile(string $sslKeyFile)
    {
        $this->clientSetting['ssl_key_file'] = $sslKeyFile;
        return $this;
    }

    /**
     * 设置HTTP代理
     * @param string $proxyHost 代理地址
     * @param int $proxyPort 代理端口
     * @param string|null $proxyUser 鉴权用户(非必须)
     * @param string|null $proxyPass 鉴权密码(非必须)
     * @return $this
     */
    public function setProxyHttp(string $proxyHost, int $proxyPort, string $proxyUser = null, string $proxyPass = null)
    {
        $this->clientSetting['http_proxy_host'] = $proxyHost;
        $this->clientSetting['http_proxy_port'] = $proxyPort;

        if (!empty($proxyUser)) {
            $this->clientSetting['http_proxy_user'] = $proxyUser;
        }

        if (!empty($proxyPass)) {
            $this->clientSetting['http_proxy_password'] = $proxyPass;
        }

        return $this;
    }

    /**
     * 设置Socks5代理
     * @param string $proxyHost 代理地址
     * @param int $proxyPort 代理端口
     * @param string|null $proxyUser 鉴权用户(非必须)
     * @param string|null $proxyPass 鉴权密码(非必须)
     * @return HttpClient
     */
    public function setProxySocks5(string $proxyHost, int $proxyPort, string $proxyUser = null, string $proxyPass = null)
    {
        $this->clientSetting['socks5_host'] = $proxyHost;
        $this->clientSetting['socks5_port'] = $proxyPort;

        if (!empty($proxyUser)) {
            $this->clientSetting['socks5_username'] = $proxyUser;
        }

        if (!empty($proxyPass)) {
            $this->clientSetting['socks5_password'] = $proxyPass;
        }

        return $this;
    }

    /**
     * 设置端口绑定
     * 用于客户机有多张网卡的时候
     * 设置本客户端底层Socket使用哪张网卡和端口进行通讯
     * @param string $bindAddress 需要绑定的地址
     * @param integer $bindPort 需要绑定的端口
     * @return HttpClient
     */
    public function setSocketBind(string $bindAddress, int $bindPort)
    {
        $this->clientSetting['bind_address'] = $bindAddress;
        $this->clientSetting['bind_port'] = $bindPort;
        return $this;
    }

    /**
     * 直接设置客户端配置
     * @param string $key 配置key值
     * @param mixed $setting 配置value值
     * @return HttpClient
     */
    public function setClientSetting(string $key, $setting): HttpClient
    {
        $this->clientSetting[$key] = $setting;
        return $this;
    }

    /**
     * 直接批量设置客户端配置
     * @param array $settings 需要设置的配置项
     * @param bool $isMerge 是否合并设置(默认直接覆盖配置)
     * @return HttpClient
     */
    public function setClientSettings(array $settings, $isMerge = true): HttpClient
    {
        if ($isMerge) {  // 合并配置项到当前配置中
            foreach ($settings as $name => $value) {
                $this->clientSetting[$name] = $value;
            }
        } else {
            $this->clientSetting = $settings;
        }
        return $this;
    }

    public function getClient(): Client
    {
        if ($this->httpClient instanceof Client) {
            return $this->httpClient;
        }
        $url = $this->parserUrlInfo();
        $this->httpClient = new Client($url->getHost(), $url->getPort(), $url->getIsSsl());
        $this->httpClient->set($this->clientSetting);
        return $this->getClient();
    }

    public function setMethod(string $method):HttpClient
    {
        $this->getClient()->setMethod($method);
        return $this;
    }

    /**
     * 解析当前的请求Url
     * @throws InvalidUrl
     */
    protected function parserUrlInfo(?array $qurey = null)
    {
        // 请求时当前对象没有设置Url
        if (!($this->url instanceof Url)) {
            throw new InvalidUrl("HttpClient: Url is empty");
        }

        // 获取当前的请求参数
        $path = $this->url->getPath();
        $host = $this->url->getHost();
        $port = $this->url->getPort();
        $query = $this->url->getQuery();
        $scheme = strtolower($this->url->getScheme());
        if (empty($scheme)) {
            $scheme = 'http';
        }
        // 支持的scheme
        $allowSchemes = ['http' => 80, 'https' => 443, 'ws' => 80, 'wss' => 443];

        // 只允许进行支持的请求
        if (!array_key_exists($scheme, $allowSchemes)) {
            throw new InvalidUrl("HttpClient: Clients are only allowed to initiate HTTP(WS) or HTTPS(WSS) requests");
        }

        // URL即使解析成功了也有可能存在HOST为空的情况
        if (empty($host)) {
            throw new InvalidUrl("HttpClient: Current URL is invalid because HOST is empty");
        }

        // 如果端口是空的 那么根据协议自动补全端口 否则使用原来的端口
        if (empty($port)) {
            $port = isset($allowSchemes[$scheme]) ? $allowSchemes[$scheme] : 80;
            $this->url->setPort($port);
        }

        // 如果当前是443端口 或者enableSSL 则开启SSL安全链接
        if ($this->enableSSL || $port === 443) {
            $this->url->setIsSsl(true);
        }

        // 格式化路径和查询参数
        $path = empty($path) ? '/' : $path;
        $query = empty($query) ? '' : '?' . $query;
        $this->url->setFullPath($path . $query);
        return $this->url;
    }

    /**
     * 设置为XMLHttpRequest请求
     * @return $this
     */
    public function setXMLHttpRequest()
    {
        $this->setHeader('x-requested-with', 'xmlhttprequest');
        return $this;
    }

    /**
     * 设置为Json请求
     * @return $this
     */
    public function setContentTypeJson()
    {
        $this->setContentType(HttpClient::CONTENT_TYPE_APPLICATION_JSON);
        return $this;
    }

    /**
     * 设置为Xml请求
     * @return $this
     */
    public function setContentTypeXml()
    {
        $this->setContentType(HttpClient::CONTENT_TYPE_APPLICATION_XML);
        return $this;
    }

    /**
     * 设置为FromData请求
     * @return $this
     */
    public function setContentTypeFormData()
    {
        $this->setContentType(HttpClient::CONTENT_TYPE_FORM_DATA);
        return $this;
    }

    /**
     * 设置为FromUrlencoded请求
     * @return $this
     */
    public function setContentTypeFormUrlencoded()
    {
        $this->setContentType(HttpClient::CONTENT_TYPE_X_WWW_FORM_URLENCODED);
        return $this;
    }

    /**
     * 设置ContentType
     * @param string $contentType
     * @return HttpClient
     */
    public function setContentType(string $contentType)
    {
        $this->setHeader('content-type', $contentType);
        return $this;
    }

    /**
     * 生成一个响应结构体
     * @param Client $client
     * @return Response
     */
    private function createHttpResponse(Client $client): Response
    {
        $response = new Response((array)$client);
        $response->setClient($client);
        return $response;
    }


    /**
     * 进行一次RAW请求
     * 此模式下直接发送Raw数据需要手动组装
     * 请注意此方法会忽略设置的POST数据而使用参数传入的RAW数据
     * @param string $httpMethod 请求使用的方法 默认为GET
     * @param null $rawData 请注意如果需要发送JSON或XML需要自己先行编码
     * @param string $contentType 请求类型 默认不去设置
     * @return Response
     * @throws InvalidUrl
     */
    protected function rawRequest($httpMethod = HttpClient::METHOD_GET, $rawData = null, $contentType = null): Response
    {
        $client = $this->getClient();
        //预处理。合并cookie 和header
        $this->setMethod($httpMethod);
        $client->setHeaders($this->header);
        $client->setCookies((array)$this->cookies + (array)$client->cookies);
        if($httpMethod == self::METHOD_POST){
            if(is_array($rawData)){
                foreach ($rawData as $key => $item){
                    if($item instanceof \CURLFile){
                        $client->addFile($item->getFilename(),$key,$item->getMimeType(),$item->getPostFilename());
                        unset($rawData[$key]);
                    }
                }
                $client->setData($rawData);
            }else if($rawData !== null){
                $client->setData($rawData);
            }
        }else if($rawData !== null){
            $client->setData($rawData);
        }
        if(is_string($rawData)){
            $this->setHeader('Content-Length', strlen($rawData));
        }
        if (!empty($contentType)) {
            $this->setContentType($contentType);
        }
        $client->execute($this->url->getFullPath());
        // 如果不设置保持长连接则直接关闭当前链接
        if (!isset($this->clientSetting['keep_alive']) || $this->clientSetting['keep_alive'] !== true) {
            $client->close();
        }
        return $this->createHttpResponse($client);
    }

    /**
     * 快速发起GET请求
     * 设置的请求头会合并到本次请求中
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function get(array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_GET);
    }

    /**
     * 快速发起HEAD请求
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function head(array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_HEAD);

    }

    /**
     * 快速发起TRACE请求
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function trace(array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_TRACE);

    }

    /**
     * 快速发起DELETE请求
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function delete(array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_DELETE);

    }

    // --------  以下四种方法可以设置请求BODY数据  --------

    /**
     * 快速发起PUT请求
     * @param null $data
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function put($data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_PUT, $data);
    }

    /**
     * 快速发起POST请求
     * @param null $data
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function post($data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_POST, $data);
    }

    /**
     * 快速发起PATCH请求
     * @param null $data
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function patch($data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_PATCH, $data);
    }

    /**
     * 快速发起预检请求
     * 需要自己设置预检头部
     * @param null $data
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function options($data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_OPTIONS, $data);
    }

    // -------- 针对POST方法另外给出两种快捷POST  --------

    /**
     * 快速发起XML POST
     * @param string $data 数据需要自己先行转为字符串
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function postXml(string $data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_POST, $data, HttpClient::CONTENT_TYPE_APPLICATION_XML);
    }

    /**
     * 快速发起JSON POST
     * @param string $data 数据需要自己先行转为字符串
     * @param array $headers
     * @return Response
     * @throws InvalidUrl
     */
    public function postJson(string $data = null, array $headers = []): Response
    {
        return $this->setHeaders($headers)->rawRequest(HttpClient::METHOD_POST, $data, HttpClient::CONTENT_TYPE_APPLICATION_JSON);
    }

    /**
     * 文件下载直接落盘不走Body拼接更节省内存
     * 可以通过偏移量(offset=原文件字节数)实现APPEND的效果
     * @param string $filename 文件保存到路径
     * @param int $offset 写入偏移量 (设0时如文件已存在底层会自动清空此文件)
     * @param string $httpMethod 设置请求的HTTP方法
     * @param null $rawData 设置请求数据
     * @param null $contentType 设置请求类型
     * @return Response|false 当文件打开失败或feek失败时会返回false
     * @throws InvalidUrl
     */
    public function download(string $filename, int $offset = 0, $httpMethod = HttpClient::METHOD_GET, $rawData = null, $contentType = null)
    {
        $client = $this->getClient();
        $client->setMethod($httpMethod);

        // 如果提供了数组那么认为是x-www-form-unlencoded快捷请求
        if (is_array($rawData)) {
            $rawData = http_build_query($rawData);
            $this->setContentTypeFormUrlencoded();
        }

        // 直接设置请求包体 (特殊格式的包体可以使用提供的Helper来手动构建)
        if (!empty($rawData)) {
            $client->setData($rawData);
            $this->setHeader('Content-Length', strlen($rawData));
        }

        // 设置ContentType(如果未设置默认为空的)
        if (!empty($contentType)) {
            $this->setContentType($contentType);
        }

        $response = $client->download($this->url->getFullPath(), $filename, $offset);
        return $response ? $this->createHttpResponse($client) : false;
    }


    /**
     * 设置请求头集合
     * @param array $header
     * @param bool $isMerge
     * @param bool strtolower
     * @return HttpClient
     */
    public function setHeaders(array $header, $isMerge = true, $strtolower = true): HttpClient
    {
        if (empty($header)) {
            return $this;
        }

        // 非合并模式先清空当前的Header再设置
        if (!$isMerge) {
            $this->header = [];
        }

        foreach ($header as $name => $value) {
            $this->setHeader($name, $value, $strtolower);
        }
        return $this;
    }

    /**
     * 设置单个请求头
     * 根据 RFC 请求头不区分大小写 会全部转成小写
     * @param string $key
     * @param string $value
     * @param bool strtolower
     * @return HttpClient
     */
    public function setHeader(string $key, string $value, $strtolower = true): HttpClient
    {
        if($strtolower){
            $this->header[strtolower($key)] = strtolower($value);
        }else{
            $this->header[$key] = $value;
        }
        return $this;
    }


    /**
     * 设置携带的Cookie集合
     * @param array $cookies
     * @param bool $isMerge
     * @return HttpClient
     */
    public function addCookies(array $cookies, $isMerge = true): HttpClient
    {
        if ($isMerge) {  // 合并配置项到当前配置中
            foreach ($cookies as $name => $value) {
                $this->cookies[$name] = $value;
            }
        } else {
            $this->cookies = $cookies;
        }
        return $this;
    }

    /**
     * 设置携带的Cookie
     * @param string $key
     * @param string $value
     * @return HttpClient
     */
    public function addCookie(string $key, string $value): HttpClient
    {
        $this->cookies[$key] = $value;
        return $this;
    }
    /**
     * 升级为Websocket请求
     * @param bool $mask
     * @return bool
     * @throws InvalidUrl
     */
    public function upgrade(bool $mask = true): bool
    {
        $this->clientSetting['websocket_mask'] = $mask;
        $client = $this->getClient();
        return $client->upgrade($this->url->getFullPath());
    }

    /**
     * 发送数据
     * @param string|Frame $data
     * @return bool
     */
    public function push($data): bool
    {
        return $this->getClient()->push($data);
    }

    /**
     * 接受数据
     * 请注意如果持续接受需要自己处理while(true)逻辑
     * @param float $timeout
     * @return Frame
     */
    public function recv(float $timeout = 1.0)
    {
        return $this->getClient()->recv($timeout);
    }

    function __destruct()
    {
        if($this->httpClient instanceof Client){
            if($this->httpClient->connected){
                $this->httpClient->close();
            }
            $this->httpClient = null;
        }
    }
}
