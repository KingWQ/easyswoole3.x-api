<?php
/**
 * Created by PhpStorm.
 * User: wysmacmini02
 * Date: 2019/8/21
 * Time: 18:53
 */
namespace App\HttpController\Test;

use App\Model\Test\TestModel;
use App\Utility\Pool\MysqlPool;
use EasySwoole\Http\AbstractInterface\Controller;

class MysqlOptTest extends Controller
{
    public function index()
    {
        // TODO: Implement index() method.
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

    public function tranTest()
    {
        $db = MysqlPool::defer();
        $userModel = new UserModel($db);
        $authModel = new UserAuthModel($db);
        $params = [];

        $db->startTransaction();

        try{
            $userBean = $userModel->create(new UserBean($params));
            if(empty($userBean)){
                throw new \Exception('创建失败');
            }

            $authBean = new UserAuthBean($params);
            $authBean->setUserId($userBean->getUserId());
            $authBean = $authModel->create($authBean);

            var_dump($authBean);
        }catch (\Exception $e){
            $db->rollback();
        }finally{
            $db->commit();
        }
    }
}