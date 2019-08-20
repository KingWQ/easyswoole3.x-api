<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/7/16
 * Time: 11:39
 */
namespace App\HttpController\Test;

use App\Model\AccountModel;
use App\Model\ConditionBean;
use App\Model\Test\TestBean;
use App\Model\Test\TestModel;
use App\Task\Task;
use App\Utility\JwtAuth;
use App\Utility\Pool\MysqlPool;
use App\Utility\Pool\RedisPool;
use EasySwoole\EasySwoole\Swoole\Task\TaskManager;
use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Http\Message\Status;
use EasySwoole\HttpClient\HttpClient;
use EasySwoole\Spl\SplBean;

class Test extends Controller
{
    public function index()
    {
        $db = MysqlPool::defer();
        $model = new AccountModel($db);
        $map['where'][] = ['user_id', 37111, '=', 'and'];
        $map['where'][] = ['booking_status', 1];
        $res = $model->countOrder($map);
        echo $db->getLastQuery();
        var_dump($res);

        $this->response()->write('hello world');
        // TODO: Implement index() method.
    }


    public function insertData($res)
    {
        if($res == 'ok'){
            echo "insert ok\n";
        }else{
            echo "insert fail\n";
        }
        return;
    }
    public function corouHttp()
    {
        $baseUrl = \Yaconf::get('supplier.base_url');
        $url = $baseUrl.\Yaconf::get('supplier.Gta.order_hotel');
//        go(function () use($url){
            $res = new HttpClient($url);
            var_dump($res->post()->getBody());
            return 'ok';
//        });
//        $res = new HttpClient($url);

        echo "start http\n";

        return;
    }


    public function task()
    {
        TaskManager::async(function () {
            echo "执行异步任务1...\n";
            return true;
        }, function () {
            echo "异步任务执行完毕2...\n";
        });
        // 在定时器中投递的例子
        $a = \EasySwoole\Component\Timer::getInstance()->loop(1000, function () {
            TaskManager::async(function () {
                echo "执行异步任务3...\n";
            });
        });

        echo "success \n";
    }

    public function taskQuick()
    {
        TaskManager::async(\App\Task\QuickTaskTest::class);
    }

    public function taskTemplate()
    {

        $taskClass = new Task(['type'=>'hotel']);
        TaskManager::async($taskClass);

        $taskClass = new Task(['type'=>'flight']);
        TaskManager::async($taskClass);
        //不写echo 执行顺序是 1-1 ，1-0
        //协程 可以用在 一组IO操作 ，利用IO等待时间，挂起去执行其他操作 如：一组sql
        //异步 遇到IO 不会等待该逻辑返回效果 直接往下执行
        echo 'success';
    }

    function multiTaskConcurrency(){
        // 多任务并发
        $tasks[] = function () { sleep(1);return 'this is 1'; }; // 任务1
        $tasks[] = function () { sleep(2);return 'this is 2'; };     // 任务2
        $tasks[] = function () { sleep(3);return 'this is 3'; }; // 任务3
        $results = \EasySwoole\EasySwoole\Swoole\Task\TaskManager::barrier($tasks, 6);
        var_dump($results);
        $this->response()->write('执行并发任务成功');
    }




    public function testRedis()
    {
        $redis = RedisPool::defer();
        $redis->set('key','swoole');
        $data = $redis->get('key');
        $this->response()->write($data);
    }

    public function add()
    {
        $db = MysqlPool::defer();
        $testModel = new TestModel($db);
        $testBean = new TestBean();
        $testBean->setName('iapp');
        $testBean->setAppSecret(md5('123456'));
        $testBean->setRemark('I am a app');

        $result = $testModel->create($testBean);
        if($result === false){
            return $this->writeJson(Status::CODE_BAD_REQUEST,[],'数据库添加数据失败');
        }
        return $this->writeJson(Status::CODE_OK, [],'ok');
    }

    public function update()
    {
        $db = MysqlPool::defer();
        $testModel = new TestModel($db);
        $testBean = new TestBean();
        $testBean->setId(1);

        $updateData = [
            'name'=>'iapp',
            'remark'=>'i am iapp',
            'update_time'=>date('Y-m-d H:i:s')
        ];
        $result = $testModel->update($testBean, $updateData);
        if($result === false){
            return $this->writeJson(Status::CODE_BAD_REQUEST,[],'数据库修改数据失败 '.$db->getLastQuery());
        }
        return $this->writeJson(Status::CODE_OK, [],'ok');
    }

    public function delete()
    {
        $db = MysqlPool::defer();
        $testModel = new TestModel($db);
        $testBean = new TestBean();
        $testBean->setId(1);

        $result = $testModel->delete($testBean);

        if($result === false){
            return $this->writeJson(Status::CODE_BAD_REQUEST,[],'数据库删除数据失败 '.$db->getLastQuery());
        }
        return $this->writeJson(Status::CODE_OK, [],'ok '.$db->getLastQuery());
    }


    public function getOne()
    {
        $db = MysqlPool::defer();
        $testModel = new TestModel($db);
        $testBean = new TestBean();
        $testBean->setId(8);

        $result = $testModel->getOne($testBean);
        var_dump($result->toArray());
    }

    public function getAll()
    {
        $db = MysqlPool::defer();
        $testModel = new TestModel($db);
        $conditionBean = new ConditionBean();
        $conditionBean->addWhere('name','','<>');
        $conditionBean->setColumns('name,remark');
        var_dump($conditionBean->toArray([], SplBean::FILTER_NOT_NULL));return;

        $result = $testModel->getAll($conditionBean->toArray([], SplBean::FILTER_NOT_NULL),1,10, $conditionBean->getColumns());
        return $this->writeJson(Status::CODE_OK, $result, 'ok');
    }

//    public function tranTest()
//    {
//        $db = MysqlPool::defer();
//        $userModel = new UserModel($db);
//        $authModel = new UserAuthModel($db);
//        $params = [];
//
//        $db->startTransaction();
//
//        try{
//            $userBean = $userModel->create(new UserBean($params));
//            if(empty($userBean)){
//                throw new \Exception('创建失败');
//            }
//
//            $authBean = new UserAuthBean($params);
//            $authBean->setUserId($userBean->getUserId());
//            $authBean = $authModel->create($authBean);
//
//            var_dump($authBean);
//        }catch (\Exception $e){
//            $db->rollback();
//        }finally{
//            $db->commit();
//        }
//
//
//
//    }
}
