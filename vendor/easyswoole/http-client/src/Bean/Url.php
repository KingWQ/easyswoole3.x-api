<?php
/**
 * Created by PhpStorm.
 * User: yf
 * Date: 2018/12/25
 * Time: 10:49 AM
 */

namespace EasySwoole\HttpClient\Bean;


use EasySwoole\Spl\SplBean;

class Url extends SplBean
{
    protected $scheme = 'http';
    protected $host;
    protected $port;
    protected $path;
    protected $query;
    protected $isSsl = false;
    protected $fullPath;

    /**
     * @return mixed
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * @param mixed $scheme
     */
    public function setScheme($scheme): void
    {
        $this->scheme = $scheme;
    }

    /**
     * @return mixed
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param mixed $host
     */
    public function setHost($host): void
    {
        $this->host = $host;
    }

    /**
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * @param mixed $port
     */
    public function setPort($port): void
    {
        $this->port = $port;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query): void
    {
        $this->query = $query;
    }

    /**
     * IsSsl Getter
     * @return mixed
     */
    public function getIsSsl()
    {
        return $this->isSsl;
    }

    /**
     * IsSsl Setter
     * @param mixed $isSsl
     */
    public function setIsSsl($isSsl): void
    {
        $this->isSsl = $isSsl;
    }

    /**
     * FullPath Getter
     * @return mixed
     */
    public function getFullPath()
    {
        return $this->fullPath;
    }

    /**
     * FullPath Setter
     * @param mixed $fullPath
     */
    public function setFullPath($fullPath): void
    {
        $this->fullPath = $fullPath;
    }
}