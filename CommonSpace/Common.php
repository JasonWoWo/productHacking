<?php

/**
 * Created by PhpStorm.
 * User: wangxionghao
 * Date: 16/4/29
 * Time: 下午12:48
 */
namespace CommonSpace;

class Common
{
    const OI_TMP_HOST = "10.6.1.112";
    const OI_PRODUCT_HOST = "db003.septinn.com";
    const OI_PRODUCT_HOST_USER = 'oiproduct';
    const OI_PRODUCT_HOST_PASSWORD = 'vrdAKl4fkUBu7qEJklY0h9Gym32kMluvnzj2VOwLpHM=';
    const OI_PRODUCT_HOST_DB_NAME = 'oiplatform';

    const OI_CAKESTAT_HOST = "db003.septinn.com";
    const OI_CAKESTAT_HOST_USER = 'stat_dev';
    const OI_CAKESTAT_HOST_PASSWORD = '59AcB586b5AABx4D';
    const OI_CAKESTAT_HOST_DB_NAME = 'oicakestat';

    const DB_STRING = 'mongodb://me001.septinn.com,me002.septinn.com/?replicaSet=rs0';
    const DB_CONNET_TIMEOUT_MS = 500;

    public $error;

    public $errno;

    public $sql;

    public $connet_id;

    public function connect()
    {
        $connectObj = mysqli_connect(self::OI_PRODUCT_HOST, self::OI_PRODUCT_HOST_USER, self::OI_PRODUCT_HOST_PASSWORD, self::OI_CAKESTAT_HOST_DB_NAME);
        if ($connectObj == false) {
            $this->error = mysqli_connect_error();
            $this->errno = mysqli_connect_errno();

            return false;
        }
        $this->connet_id = $connectObj;
        return $connectObj;
    }

    public function mongoConnect()
    {
        $mongoObj = new \MongoClient(self::DB_STRING);

        return $mongoObj;
    }

    public function fetchDeviceCollection()
    {
        $mongoObj = $this->mongoConnect();

        return $mongoObj->ur->dev;
    }

    public function fetchDeviceInfoCollection()
    {
        $mongoObj = $this->mongoConnect();

        return $mongoObj->devices->device_info;
    }
    
    public function fetchRetainCollection()
    {
        $mongoObj = $this->mongoConnect();
        
        return $mongoObj->devices->retain_info;
    }

    public function fetchAppListCollection()
    {
        $mongoObj = $this->mongoConnect();

        return $mongoObj->ur->app_list;
    }
    
    public function fetchCnt($sql, $insert = false)
    {
        $connectObj = $this->connect();

        $this->sql = $sql;
        if (!@mysqli_ping($connectObj)) {
            @mysqli_close($connectObj);
        }
        $connectObj = $this->connect();
        if (!$connectObj) {
            echo "mysql connect error!";
        }
        $query = @mysqli_query($connectObj, $this->sql);
        if (!$query) {
            $query = @mysqli_query($connectObj, $this->sql);
        }

        $this->save_error($connectObj);
        if ($insert) {
            return $query;
        }
        $result = $query->fetch_assoc();

        return $result;

    }

    public function getCakeStatConDb()
    {
        $cakeConnectObj = mysqli_connect(self::OI_CAKESTAT_HOST,
            self::OI_CAKESTAT_HOST_USER,
            self::OI_CAKESTAT_HOST_PASSWORD,
            self::OI_CAKESTAT_HOST_DB_NAME
        );
        if ($cakeConnectObj == false) {
            $this->error = mysqli_connect_error();
            $this->errno = mysqli_connect_errno();

            return false;
        }
        $this->connet_id = $cakeConnectObj;
        return $cakeConnectObj;
    }

    public function fetchCakeStatQuery($sql)
    {
        $cakeConnectObj = $this->getCakeStatConDb();
        $this->sql = $sql;
        if (!@mysqli_ping($cakeConnectObj)) {
            @mysqli_close($cakeConnectObj);
            $cakeConnectObj = $this->getCakeStatConDb();
        }
        if (!$cakeConnectObj) {
            echo "mysqli connect error, Please Check! \n";
        }
        $query = @mysqli_query($cakeConnectObj, $this->sql);
        if (!$query) {
            $query = @mysqli_query($cakeConnectObj, $this->sql);
        }
        return $query;

    }

    // 简单的 insert 语句拼接
    public function insertParamsQuery($tableName, $params = array())
    {
        if (!is_array($params)) {
            return false;
        }
        if (empty($params)) {
            return false;
        }
        $columnValue = implode(",", array_keys($params));
        $paramsValue = implode(",", array_values($params));
        $currentTable = 'oicakestat.' . $tableName;
        $sql = sprintf("INSERT INTO %s (%s) VALUES (%s)",
            $currentTable,
            $columnValue,
            $paramsValue);
        echo $sql . " \n";
        return $sql;
    }

    // 简单的 update 语句拼接
    public function updateParamsQuery($tableName, $params = array(), $where = array())
    {
        if (!is_array($params)) {
            return false;
        }
        if (empty($params)) {
            return false;
        }
        $paramsList = array();
        foreach ($params as $key => $value) {
            $paramsList[] = $key . " = " . $value;
        }
        $whereList = array();
        foreach ($where as $key => $value) {
            $whereList[] = $key .  " = " . $value;
        }
        $updateParamsString = implode(',', $paramsList);
        $whereString = implode(' AND ', $whereList);
        $currentTable = 'oicakestat.' . $tableName;
        $sql = "UPDATE " . $currentTable . " SET " . $updateParamsString . " WHERE " . $whereString;
        echo $sql . "\n";
        return $sql;
    }

    public function selectParamsQuery($tableName, $params = array(), $where = array(), $groupBy = array(), $orderBy = array(), $limit = 0)
    {
        if (empty($params)) {
            return false;
        }
        $whereList = array();
        foreach ($where as $key => $value) {
            $whereList[] = $key . " = " . $value;
        }
        $orderByList = array();
        foreach ($orderBy as $key => $value) {
            $orderByList[] = $key . " " . $value;
        }
        $contentString = implode(' , ', $params);
        $whereString = implode(' AND ', $whereList);
        $groupByString = implode(' , ', $groupBy);
        $orderByString = implode(' , ', $orderByList);
        $currentTable = 'oicakestat.' . $tableName;
        $sql = "SELECT " . $contentString . " FROM " . $currentTable . " WHERE " . $whereString;
        if (!empty($groupByString)) {
            $sql .=  " GROUP BY " . $groupByString;
        }
        if (!empty($orderByString)) {
            $sql .= " ORDER BY " . $orderByString;
        }
        if (!empty($limit)) {
            $sql .= " LIMIT " . $limit;
        }
        echo $sql . " \n";
        return $sql;
    }

    public function fetchAssoc($sql)
    {
        $connectObj = $this->connect();

        $this->sql = $sql;
        if (!@mysqli_ping($connectObj)) {
            @mysqli_close($connectObj);
        }
        $connectObj = $this->connect();
        if (!$connectObj) {
            echo "mysql connect error!";
        }
        $query = @mysqli_query($connectObj, $this->sql);
        if (!$query) {
            $query = @mysqli_query($connectObj, $this->sql);
        }
        $result = array();
        while ($row = $query->fetch_assoc()) {
            $result[] = $row;
        }

        $this->save_error($connectObj);

        return $result;

    }

    private function save_error($dblink)
    {
        $this->error = mysqli_error($dblink);
        $this->errno = mysqli_errno($dblink);
    }
    

    // 插入Mongo
    public function injectMongo($params = array(), $forward = 0)
    {
        $retainCollection = $this->fetchRetainCollection();
        $query = array('_id' => $params['udid']);
        $retainList = $retainCollection->findOne($query);
        if (empty($retainList)) {
            $this->_create_retain_if_n_exists($retainCollection, $query);
        }
        try {
            $this->_update_retain($retainCollection, $query, $params, $forward);
        } catch (MongoException $e) {

        }
    }

    private function _create_retain_if_n_exists(MongoCollection $collection, $query)
    {
        try {
            $new_doc = array(
                'uid' => 0,
                'max_bct' => 0,
                'dct_lt' => 0,
                'sst_lt' => 0,
                'wct_lt' => 0,
                'mct_lt' => 0,
                'mst_lt' => 0
            );
            $collection->update($query, $new_doc, ['upsert' => true]);
        } catch (MongoException $e) {
            echo "error Mongo Exception: " .$e->getMessage() . " \n";
        }
    }

    private function _update_retain(MongoCollection $collection, $query = array(), $param = array(), $forward = 0)
    {
        $updateList = array();
        if ($param['uid']) {
            $updateList['uid'] = $param['uid'];
        }
        if ($param['max_bct']) {
            $updateList['max_bct'] = intval($param['max_bct']);
        }
        $currentDate = new \DateTime();
        $currentDate->modify('-1 day');
        if ($forward == 0) {
            //当天保存的天数据
            $updateList['dct_lt'] = new MongoInt64(strtotime($param['create_on']));
        } elseif ($forward == 7) {
            //每周周一
            $updateList['wct_lt'] = new MongoInt64(strtotime($param['create_on']));
        } elseif ($forward == 30) {
            //每月1号执行
            $updateList['mct_lt'] = new MongoInt64(strtotime($param['create_on']));
        } elseif ($forward == 17) {
            // 每天执行 更新7天累积完成核心任务数量 例如 5月4号 更新 4月27日的7天累积核心任务人数
            $updateList['sst_lt'] = new MongoInt64($currentDate->modify('-6 days')->getTimestamp());
        } elseif ($forward == 130) {
            // 每天执行 更新30天累积完成核心任务数量 例如 5月2号 更新 4月2日的30天累积核心任务人数
            $updateList['mst_lt'] = new MongoInt64($currentDate->modify('-29 days')->getTimestamp());
        }
        $updates = array(
            '$set' => $updateList
        );
        $ret = array();
        try {
            $ret = $collection->update($query, $updates);
        } catch (MongoException $e) {
            echo "error Mongo Exception: " .$e->getMessage() . " \n";
        }
        return $ret;
    }

    public function calculateLoginIn($currentStamp = 0, $isRetain = 0)
    {
        $loginInStamp = $currentStamp - $isRetain * 86400;
        $loginInString = "'" . date('Y-m-d', $loginInStamp) . "'";
        return $loginInString;
    }
}