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

    /**
     * @var \Redis
     */
    public $redis;

    public function __construct()
    {
        $this->connectObj = new Common();
        $this->redis = $this->connectObj->redisConnect();
    }

    public function getPointPackUnRegisters()
    {
        $defaultTable = 70;
        $maxUserQuery = $this->getQueryMaxUserId();
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
        $maxIdQuery = $this->getQueryUnRegisterMaxId($currentTable);
        $result = $this->connectObj->fetchCnt($maxIdQuery);
        $limitMaxId = 0;
        while ($limitMaxId < $result['maxId']) {
            $unRegisterItems = $this->connectObj->fetchAssoc($this->getQueryUnRegisterCollection($currentTable, $limitMaxId));
            foreach ($unRegisterItems as $item) {
                if ($item['id'] >= $limitMaxId) {
                    $limitMaxId = $item['id'];
                }
                $phonePrivate = $this->getPrivate($item['phone']);
                if (!empty($phonePrivate)) {
                    continue;
                }
                $item['followers'] = $this->getFollowersCnt($item['phone']);
                $this->injectUnRegisterInfoMongoDb($item);
            }
            echo "Obtain the table: " . $currentTable . " MaxId: " . $limitMaxId . " \n";
        }
        echo $currentTable . " Success injectMongo \n";
    }

    public function getPrivate($phone)
    {
        $hashedNumber = $this->get_hashed_number($phone);
        $hashedNumber = array($hashedNumber);
        $existPrivate = $this->_queryPrivacy($hashedNumber);
        $value = $existPrivate[$hashedNumber[0]];
        return $value;
    }

    public function getFollowersCnt($unRegisterPhone)
    {
        $items = $this->redis->sMembers($this->_follower_key($unRegisterPhone));
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
        $unRegisterInfo = $collection->findOne($query);
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
                'by' => 0,
                'bm' => 0,
                'bd' => 0,
                'bl' => 0,
                'g' => -1,
                'send' => 0
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
        if ($param['birth_y']) {
            $updateList['by'] = intval($param['birth_y']);
        }
        if (abs($param['birth_m'])) {
            $updateList['bm'] = intval(abs($param['birth_m']));
        }
        if ($param['birth_d']) {
            $updateList['bd'] = intval($param['birth_d']);
        }
        $updateList['bl'] = intval($param['birth_is_lunar']);
        $updateList['g'] = intval($param['gender']);
        $updates = array(
            '$set' => $updateList
        );
        $ret = array();
        try {
            $ret = $collection->update($query, $updates);
        } catch (\MongoException $e) {
            echo "error Mongo Exception: " .$e->getMessage() . " AND id: ". $param['id'] . " \n";
        }
        return $ret;
    }
    
    private function _queryPrivacy($hashedNumbers)
    {
        $settings = $this->redis->hMGet('S:', $hashedNumbers);

        foreach ($settings as &$setting) {
            $setting = (intval($setting) >> 2) & 0x3;
        }
        unset($setting);

        return $settings;
    }

}