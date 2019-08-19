<?php


namespace EasySwoole\Config\Test;


use EasySwoole\Config\SplArrayConfig;
use PHPUnit\Framework\TestCase;

class SplArrayTest extends TestCase
{

    protected $config;

    public function setUp()/* The :void return type declaration that should be here would cause a BC issue */
    {
        $this->config = new \EasySwoole\Config\SplArrayConfig();
    }

    private function getConfigObj(): SplArrayConfig
    {
        return $this->config;
    }

    private function loadConfig()
    {
        $config = [
            'SERVER_NAME' => "EasySwoole",//服务名
            'MAIN_SERVER' => [
                'LISTEN_ADDRESS' => '0.0.0.0',//监听地址
                'PORT' => 9901,//监听端口
                'SERVER_TYPE' => 'EASYSWOOLE_WEB_SERVER', //可选为 EASYSWOOLE_SERVER  EASYSWOOLE_WEB_SERVER EASYSWOOLE_WEB_SOCKET_SERVER
                'SOCK_TYPE' => 'SWOOLE_TCP',//该配置项当为SERVER_TYPE值为TYPE_SERVER时有效
                'RUN_MODEL' => 'SWOOLE_PROCESS',// 默认Server的运行模式
                'SETTING' => [// Swoole Server的运行配置（ 完整配置可见[Swoole文档](https://wiki.swoole.com/wiki/page/274.html) ）
                    'worker_num' => 8,//运行的  worker进程数量
                    'max_request' => 5000,// worker 完成该数量的请求后将退出，防止内存溢出
                    'task_worker_num' => 8,//运行的 task_worker 进程数量
                    'task_max_request' => 1000// task_worker 完成该数量的请求后将退出，防止内存溢出
                ]
            ],
            'TEMP_DIR' => null,//临时文件存放的目录
            'LOG_DIR' => null,//日志文件存放的目录;
        ];
        $this->getConfigObj()->load($config);
    }


    public function testSetConf()
    {
        $this->assertEquals(null, $this->getConfigObj()->getConf('TEMP_DIR'));
        $this->getConfigObj()->setConf('TEMP_DIR', '/Temp');
        $this->assertEquals('/Temp', $this->getConfigObj()->getConf('TEMP_DIR'));
    }

    public function testGetConf()
    {
        $this->loadConfig();
        $this->assertEquals('EasySwoole', $this->getConfigObj()->getConf('SERVER_NAME'));
        $this->assertEquals(9901, $this->getConfigObj()->getConf('MAIN_SERVER.PORT'));
        $this->assertEquals(8, $this->getConfigObj()->getConf('MAIN_SERVER.SETTING.worker_num'));
    }


    public function testMerge()
    {
        $this->loadConfig();
        $this->assertEquals('EasySwoole', $this->getConfigObj()->getConf('SERVER_NAME'));
        $arr = ['SERVER_NAME' => 'bb'];
        $this->getConfigObj()->merge($arr);
        $this->assertEquals('bb', $this->getConfigObj()->getConf('SERVER_NAME'));
    }

    public function testClear()
    {
        $this->loadConfig();
        $this->getConfigObj()->clear();
        $arr = $this->getConfigObj()->getConf();
        $this->assertEmpty($arr);
    }
}