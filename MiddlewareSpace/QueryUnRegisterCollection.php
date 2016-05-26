<?php

namespace MiddlewareSpace;

use CommonSpace\Common;
use UtilSpace\UtilSqlTool;
use UtilSpace\UtilTool;

class QueryUnRegisterCollection
{
    use UtilSqlTool;
    use UtilTool;

    public $connectObj;

    public function __construct()
    {
        $this->connectObj = new Common();
    }

    public function getPointPackUnRegisters()
    {
        $defaultTable = 71;
        $maxUserQuery = $this->getQueryMaxUserId();
        $this->getQueryUnRegisterCollection($defaultTable);
        $user = $this->connectObj->fetchCnt($maxUserQuery);
        $maxTable = $this->get_number_birthday_number($user['id']);
        while ($defaultTable <= $maxTable) {
            $this->getCurrentDevice($defaultTable);
            $defaultTable += 1;
        }
    }

    public function getCurrentDevice($table)
    {
        $currentTable ="oibirthday.br_birthdays_" . $table;
        $this->getQueryUnRegisterCollection($currentTable);
        $unRegisterItems = $this->connectObj->fetchAssoc($this->getQueryUnRegisterCollection($currentTable));
        foreach ($unRegisterItems as $item) {
            $item['followers'] = $this->getFollowersCnt($item['phone']);
            $this->injectUnRegisterInfoMongoDb($item);
        }
        echo $currentTable . " Success injectMongo \n";
    }

    public function getFollowersCnt($unRegisterPhone)
    {
        $redis = $this->connectObj->redisConnect();
        $items = $redis->sMembers($this->_follower_key($unRegisterPhone));
        return count($items);
    }

    public function queryCollectionItems($params = array())
    {
        $collection = $this->connectObj->fetchUnRegisterInfoCollection();
        $query = array(
            'age' => array('$gte' => $params['minAge'], '$lte' => $params['maxAge']),
            'fc' => array('$gte' => $params['minFc'], '$lte' => $params['maxFc']),
        );
        return $collection->find($query)->count();
    }
    
    private function injectUnRegisterInfoMongoDb($param = array())
    {
        if (empty($param)) {
            echo 'Uncatch UnRegister Params , Please check !!!';
            return;
        }
        $collection = $this->connectObj->fetchUnRegisterInfoCollection();
        $query = array('_id' => $param['phone']);
        $unRegisterInfo = $collection->find($query);
        if (empty($unRegisterInfo)) {
            $this->_create_if_n_exit($collection, $query);
        }
        try {
            $this->update_unRegister_info($collection, $query, $param);
        } catch (\MongoException  $e) {
            echo $e->getMessage();
        }
    }

    private function _create_if_n_exit(\MongoCollection $collection, $query)
    {
        try {
            $new_doc = array(
                'fc' => 0,
                'age' => 0,
            );
            $collection->update($query, $new_doc, ['upsert' => true]);
        } catch (\MongoException $e) {
            echo "error Mongo Exception: " .$e->getMessage() . " \n";
        }
    }

    private function update_unRegister_info(\MongoCollection $collection, $query, $param)
    {
        $updateList = array();
        if ($param['followers']) {
            $updateList['fc'] = intval($param['followers']);
        }
        if ($param['age']) {
            $updateList['age'] = intval($param['age']);
        }
        $updates = array(
            '$set' => $updateList
        );
        $ret = array();
        try {
            $ret = $collection->update($query, $updates);
        } catch (\MongoException $e) {
            echo "error Mongo Exception: " .$e->getMessage() . " \n";
        }
        return $ret;
    }

    private function _follower_key( $number ) {
        return "F:{$number}:follower";
    }
}